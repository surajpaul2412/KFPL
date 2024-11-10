<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\Amc;
use App\Models\Ticket;
use App\Models\Emailtemplate;
use Illuminate\Support\Facades\Log;

class TemplateBasedMailToAMC extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The ticket instance.
     *
     * @var Ticket
     */
    public $ticket;
    public $forceEmailTemplate;

    /**
     * Create a new message instance.
     *
     * @param Ticket $ticket
     */
    public function __construct(Ticket $ticket, $forceEmailTemplate = '')
    {
          $this->ticket = $ticket;
		  $this->forceEmailTemplate = $forceEmailTemplate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = "";
        
		// LOAD DEFAULT SUBJECT
		if( $this->ticket->payment_type == 1 )
        {
          $subject .= "Cash";
        }
        
        if( $this->ticket->type == 1 )
        {
          $subject .= " Creation";
        }
        if( $this->ticket->type == 2 )
        {
          $subject .= " Redemption";
        }

        $subject .= " Request " . now()->format('d-m-Y');
		
		// TEMPLATE BASED SUBJECTS
		if( 
		   ( $this->ticket->type == 1 &&  $this->ticket->payment_type == 1 ) // Buy CASH
		   ||
		   ( $this->ticket->type == 2 &&  $this->ticket->payment_type == 1 ) // SELL CASH
	    )
		{
			$data = $this->fetchAMCEmailSubjectAndBody( );
			if( $data['subject'] != "" )
			{
				$subject = $data['subject'];	
			}
		}
	    
        return new Envelope(
            subject: $subject,
        );
    }

	
    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
		return new Content(
			view: 'emails.mail_to_amc',
		);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // GET SUBJECT and BODY
		$data = $this->fetchAMCEmailSubjectAndBody();
		
		dd($data);
		
		try 
		{
		
			$end = "
			<div style='padding-top: 2rem;'>
            With Regards,<br/>
            ETF Operations Team<br/>
             <img src='" . url('/') . "/assets/img/mail-logo.jpg' alt='logo' />
            </div>
			";
			
			if( $data['body']!='' )
			{
				$mail = $this->subject($data['subject'])->html( $data['body'] . $end );
			}
			else 
			{
				$mail = $this->subject($data['subject'])->view('emails.mail_to_amc');
			}
	
			// Check if the screenshot file exists
			if($this->ticket->screenshot != null){
				if (file_exists(storage_path('app/public/' . $this->ticket->screenshot))) {
					$mail->attach(storage_path('app/public/' . $this->ticket->screenshot), [
						'as' => 'screenshot.jpg', // Change the file extension accordingly
						'mime' => 'image/jpeg', // Change the MIME type accordingly
					]);
				}
			}
			
			// AMC PDF
			$amc_path = '';
			$amc_name = '';
			
			// ATTACH DEMAT PDF only if it is not NONE
			if( $this->ticket->security->amc->pdf->name != 'None' )
			{
			  $amc_path = public_path($this->ticket->security->amc->pdf->path);
			  $amc_name = $this->ticket->security->amc->pdf->name . '.pdf';
			}
			
			// ATTACH TICKET PDF
			$pdfPath = '';
			$filePath = '';
			if($this->ticket->security->amc->amc_pdf == 1)
			{
			   $pdfPath = 'ticketpdfs/' . 'ticket-' . $this->ticket->id . '.pdf';
			   $filePath = storage_path('app/public/' . $pdfPath);
			}
			
			if( $amc_name!='' && $amc_path != '' && file_exists($amc_path) )
			{
				$mail->attach($amc_path, [
								 'as' => $amc_name,
								 'mime' => 'application/pdf',
							 ]);
			}
			
			if( $pdfPath!='' && $filePath != '' && file_exists($filePath) )
			{
				$mail->attach($filePath, [
							 'as' => 'AMC.pdf',
							 'mime' => 'application/pdf',
						 ]);
			}
			
			return $mail;
			
		} catch (\Exception $e) {
            
			return $this->subject($subject)
                        ->view('emails.mail_to_amc')
                        ->withError("Some Error : " . $e->getMessage());
        }	
        
    }
	
	public function fetchAMCEmailSubjectAndBody()
	{
		
		// BUILD DEFAULT SUBJECT
		$subject = "";
		$body = "";
		$template = "";
		
		if( $this->ticket->payment_type == 1 )
        {
          $subject .= "Cash";
        }
        if( $this->ticket->type == 1 )
        {
          $subject .= " Creation";
        }
        if( $this->ticket->type == 2 )
        {
          $subject .= " Redemption";
        }
        $subject .= " Request " . now()->format('d-m-Y');
		
		// GET AMC and SUBJECT
		$amc = $this->ticket->security->amc;
		
		if( $amc )
		{
			$email_template_id = "";
			
			// FORCING a TEMPLATE
			if( $this->forceEmailTemplate != '' )
			{
				if ( $this->forceEmailTemplate == '1' && $this->ticket->security->amc->buycashtmpl != null )
				{
					$email_template_id = $this->ticket->security->amc->buycashtmpl;
				}
				
				if ( $this->forceEmailTemplate == '2' && $this->ticket->security->amc->sellcashtmpl != null )
				{
					$email_template_id = $this->ticket->security->amc->sellcashtmpl;
				}
				
				if ( $this->forceEmailTemplate == '3' && $this->ticket->security->amc->sellcashwosstmpl != null )
				{
					$email_template_id = $this->ticket->security->amc->sellcashwosstmpl;
				}

			}
			else 
			{
				// BUY, CASH CASE and EMAIL TEMPLATE was SELECTED
				if( $this->ticket->type == 1 &&  $this->ticket->payment_type == 1 && $this->ticket->security->amc->buycashtmpl != null )
				{
					$email_template_id = $this->ticket->security->amc->buycashtmpl;
				}
				
				// SELL, CASH CASE and EMAIL TEMPLATE was SELECTED
				if( $this->ticket->type == 2 &&  $this->ticket->payment_type == 1 )
				{
					// HAS SCREENSHOT 
					if($this->ticket->screenshot != null)
					{
						if ( $this->ticket->security->amc->sellcashtmpl != null )
						{
							$email_template_id = $this->ticket->security->amc->sellcashtmpl;
						}
					
					}
					else // NO Screenshot 
					{
						if ( $this->ticket->security->amc->sellcashwosstmpl != null )
						{
							$email_template_id = $this->ticket->security->amc->sellcashwosstmpl;
						}
						
					}
					
				}
		    
			}
			
			if($email_template_id)
			{
				$emailTemplate = Emailtemplate::where("id", $email_template_id)->first();
				
				if( $emailTemplate )
				{
					$sub_ject = trim($emailTemplate -> subject);
					
					if( $sub_ject != '' )
					{	
						// DO replace 
						$subject = $this->doTheTextReplacement($sub_ject);
					}
					
					$template = trim($emailTemplate -> template);
					
					if( $template != '' )
					{
						// DO replace 
						$template = $this->doTheTextReplacement($template);
					}
					
				}
			}
		}
		
		return ["subject" => $subject, "body" => $template];
	}
	
	public function doTheTextReplacement($source_text)
	{
		$source = [
			"[[-CurrentDate-]]",
			"[[-CurrentTime-]]",
			"[[-AMCName-]]",
			"[[-AMCInvestorDetails-]]",
			"[[-AMCBankDetails-]]",
			"[[-TicketID-]]",
			"[[-TicketDate-]]",
			"[[-TicketScheme-]]",
			"[[-TicketSymbol-]]",
			"[[-TicketBasketSize-]]",
			"[[-TicketBasketNumber-]]",
			"[[-TicketTotalUnits-]]",
			"[[-TicketTotalUnitsInWords-]]",
			"[[-TicketUTRNumber-]]",
			"[[-TicketTotalAmt-]]",
			"[[-TicketTotalAmtInWords-]]"
			
		];
		
		$target = [
			date("jS F, Y", time()),
			date("H:i:s", time()),
			$this->ticket->security->amc->name,
			nl2br($this->ticket->security->amc->investordetails),
			nl2br($this->ticket->security->amc->bankdetails),
			$this->ticket->id,
			date("jS F, Y", strtotime($this->ticket->created_at)),
			$this->ticket->security->name,
			$this->ticket->security->symbol,
			$this->ticket->basket_size,
			$this->ticket->basket_no,
			$this->ticket->basket_size * $this->ticket->basket_no,
			$this->NumberintoWords( $this->ticket->basket_size * $this->ticket->basket_no ),
			$this->ticket->utr_no,
			$this->ticket->total_amt,
			$this->NumberintoWords( $this->ticket->total_amt )
		];
		
		for($i=0; $i<count($source); $i++ )
		{
			$source_text = str_ireplace($source[$i], $target[$i], $source_text);
		}
		
		return $source_text;
	}
	
	public function NumberintoWords(float $number)
    {
      try{
        
		$number_after_decimal = round($number - ($num = floor($number)), 2) * 100;

        // Check if there is any number after decimal
        $amt_hundred = null;
        $count_length = strlen($num);
        $x = 0;
        $string = [];
        $change_words = [
            0 => "Zero",
            1 => "One",
            2 => "Two",
            3 => "Three",
            4 => "Four",
            5 => "Five",
            6 => "Six",
            7 => "Seven",
            8 => "Eight",
            9 => "Nine",
            10 => "Ten",
            11 => "Eleven",
            12 => "Twelve",
            13 => "Thirteen",
            14 => "Fourteen",
            15 => "Fifteen",
            16 => "Sixteen",
            17 => "Seventeen",
            18 => "Eighteen",
            19 => "Nineteen",
            20 => "Twenty",
            30 => "Thirty",
            40 => "Fourty",
            50 => "Fifty",
            60 => "Sixty",
            70 => "Seventy",
            80 => "Eighty",
            90 => "Ninety",
        ];
        $here_digits = ["", "Hundred", "Thousand", "Lakh", "Crore"];
        while ($x < $count_length) {
            $get_divider = $x == 2 ? 10 : 100;
            $number = floor($num % $get_divider);
            $num = floor($num / $get_divider);
            $x += $get_divider == 10 ? 1 : 2;
            if ($number) {
                $add_plural =
                    ($counter = count($string)) && $number > 9 ? "" : null;
                $amt_hundred = $counter == 1 && $string[0] ? " and " : null;
                $string[] =
                    $number < 21
                        ? (isset($change_words[$number])
                                ? $change_words[$number]
                                : "") .
                            " " .
                            (isset($here_digits[$counter])
                                ? $here_digits[$counter]
                                : "") .
                            $add_plural .
                            " " .
                            $amt_hundred
                        : $change_words[floor($number / 10) * 10] .
                            ($number % 10 != 0
                                ? " " . $change_words[$number % 10]
                                : "") .
                            " " .
                            $here_digits[$counter] .
                            $add_plural .
                            " " .
                            $amt_hundred;
            } else {
                $string[] = null;
            }
        }
        $implode_to_Words = implode("", array_reverse($string));

        $get_word_after_point =
            $number_after_decimal > 0
                ? "Point " .
                    ($change_words[$number_after_decimal / 10] .
                        "
      " .
                        $change_words[$number_after_decimal % 10])
                : "";

        return ($implode_to_Words ? $implode_to_Words : "") .
            ($get_word_after_point != "" ? " " . $get_word_after_point : "");
      } catch (\Exception $e) {
        dd($e->getMessage());
      }
    }
}
