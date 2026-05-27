<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupportTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $attachmentPath;

    public function __construct($ticket, $attachmentPath = null)
    {
        $this->ticket = $ticket;
        $this->attachmentPath = $attachmentPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новое обращение в поддержку: ' . $this->ticket['subject'],
            replyTo: [$this->ticket['email'] => $this->ticket['name']],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.support_ticket',
        );
    }

    public function attachments(): array
    {
        if ($this->attachmentPath && file_exists(storage_path('app/' . $this->attachmentPath))) {
            return [
                Attachment::fromPath(storage_path('app/' . $this->attachmentPath))
                    ->as($this->ticket['attachment_original_name'] ?? basename($this->attachmentPath)),
            ];
        }

        return [];
    }
}
