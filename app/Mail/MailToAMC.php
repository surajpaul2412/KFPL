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

class MailToAMC extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The ticket instance.
     *
     * @var Ticket
     */
    public $ticket;

    /**
     * Create a new message instance.
     *
     * @param Ticket $ticket
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mail To AMC',
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
