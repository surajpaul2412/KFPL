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
    public $selfMail;

    /**
     * Create a new message instance.
     *
     * @param Ticket $ticket
     */
    public function __construct(Ticket $ticket, $selfMail = '')
    {
          $this->ticket = $ticket;
		  $this->selfMail = $selfMail != '' ? $selfMail : 0;
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
			
			// MAIL to SELF
			if( $this->selfMail == 1 )
			{
				if ( $this->ticket->security->amc->mailtoselftmpl != null )
				{
					$email_template_id = $this->ticket->security->amc->mailtoselftmpl;
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
			"[[-TicketUTRNumber-]]",
			"[[-TicketTotalAmt-]]"
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
			$this->ticket->utr_no,
			$this->ticket->total_amt
		];
		
		for($i=0; $i<count($source); $i++ )
		{
			$source_text = str_ireplace($source[$i], $target[$i], $source_text);
		}
		
		return $source_text;
	}
}
