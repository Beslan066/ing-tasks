<?php

namespace App\Mail;

use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DepartmentEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->email->subject,
            from: [
                'address' => $this->email->from_email,
                'name' => $this->email->from_name,
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.department',
            with: [
                'email' => $this->email,
            ],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->email->files as $emailFile) {
            $attachments[] = Attachment::fromStorageDisk(
                $emailFile->file->disk,
                $emailFile->file->path
            )->as($emailFile->original_name);
        }

        return $attachments;
    }
}
