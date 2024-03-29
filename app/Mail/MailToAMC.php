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
        $amc_path = public_path($this->ticket->security->amc->pdf->path);
        $amc_name = $this->ticket->security->amc->pdf->name . '.pdf';

        $pdfPath = 'ticketpdfs/' . 'ticket-' . $this->ticket->id . '.pdf';
        $filePath = storage_path('app/public/' . $pdfPath);

        if (file_exists($filePath)) {
            return $this->subject('Mail To AMC')
                        ->view('emails.mail_to_amc')
                        ->attach($amc_path, [
                            'as' => $amc_name,
                            'mime' => 'application/pdf',
                        ])
                        ->attach($filePath, [
                            'as' => 'AMC.pdf',
                            'mime' => 'application/pdf',
                        ]);
        } else {
            return $this->subject('Mail To AMC')
                        ->view('emails.mail_to_amc')
                        ->withError("File not found: $pdfPath");
        }
    }
}
