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

class MailScreenshotToAMC extends Mailable
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
        $subject = 'Transfer of ETF Units ';
        $subject .= now()->format('d-m-Y');

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
            view: 'emails.mail_screenshot_to_amc',
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
        $subject = 'Transfer of ETF Units';
        $subject .= now()->format('Y-m-d');

        $mail = $this->subject($subject)
                 ->view('emails.mail_screenshot_to_amc');

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
    }
}
