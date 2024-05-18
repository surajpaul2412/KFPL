<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class MailToAMC extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The ticket instance.
     *
     * @var Ticket
     */
    public $ticket;
    public $splCase;

    /**
     * Create a new message instance.
     *
     * @param Ticket $ticket
     */
    public function __construct(Ticket $ticket, $splCase = '')
    {
          $this->ticket = $ticket;
		      $this->splCase = $splCase != '' ? $splCase : 0;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = "";
        
        if( $this->ticket->payment_type == 1 )
        {
          $subject .= "Cash";
        }
        if( $this->ticket->payment_type == 2 )
        {
          $subject .= "Basket";
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

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if( $this->splCase == 3) // STEP 3 EMAILs for Buy SELL Basket
		{
			return new Content(
				view: 'emails.spl_case_mail3',
			);
		}
		elseif( $this->splCase == 13) // STEP 13 EMAILs for Buy Basket
		{
			return new Content(
				view: 'emails.spl_case_mail13',
			);
		}
		else
		{
			return new Content(
				view: 'emails.mail_to_amc',
			);
		}
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

		// SPECIAL case when TICKET UPDATED at STATUS_ID = 3
		if( $this->splCase == 3 )
		{
			Log::info("MAilSending in a special case 3 :: Buy/Sell Basket");

			$subject = "Basket Creation request " . now()->format('d-m-Y');

			$mail = $this->subject($subject)->view('emails.spl_case_mail3');

			// Check if the screenshot file exists
			return $mail;
		}

		// SPECIAL case when TICKET UPDATED at STATUS_ID = 13
		if( $this->splCase == 13 )
		{
			Log::info("MAilSending in a special case 13 :: Buy Basket");

			$subject = "Basket Creation request " . now()->format('d-m-Y');

			$mail = $this->subject($subject)->view('emails.spl_case_mail13');

			// Check if the screenshot file exists
            if($this->ticket->screenshot != null){
				Log::info("MAilSending in a special case 13 :: Screenshot field value not NULL");
                if (file_exists(storage_path('app/public/' . $this->ticket->screenshot))) {
					Log::info("MAilSending in a special case 13 :: Screenshot file available for attaching");
                    $mail->attach(storage_path('app/public/' . $this->ticket->screenshot), [
                        'as' => 'screenshot.jpg', // Change the file extension accordingly
                        'mime' => 'image/jpeg', // Change the MIME type accordingly
                    ]);
                }
				else
				{
					Log::info("MAilSending in a special case 13 :: Screenshot file NOT available for attaching");
				}
            }
			// Check if the screenshot file exists
			return $mail;
		}

		$subject = $this->ticket->payment_type == 1 ? 'Cash Creation request ' : 'Basket Creation request ';
        $subject .= now()->format('Y-m-d');

        $amc_path = public_path($this->ticket->security->amc->pdf->path);
        $amc_name = $this->ticket->security->amc->pdf->name . '.pdf';

        $pdfPath = 'ticketpdfs/' . 'ticket-' . $this->ticket->id . '.pdf';
        $filePath = storage_path('app/public/' . $pdfPath);

        if (file_exists($filePath)) {
            $mail = $this->subject($subject)
                     ->view('emails.mail_to_amc')
                     ->attach($amc_path, [
                         'as' => $amc_name,
                         'mime' => 'application/pdf',
                     ])
                     ->attach($filePath, [
                         'as' => 'AMC.pdf',
                         'mime' => 'application/pdf',
                     ]);

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
        } else {
            return $this->subject($subject)
                        ->view('emails.mail_to_amc')
                        ->withError("File not found: $pdfPath");
        }
    }
}
