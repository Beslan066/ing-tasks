<?php

namespace App\Mail;

use App\Models\SmtpSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SmtpTestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $setting;

    public function __construct(SmtpSetting $setting)
    {
        $this->setting = $setting;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Тестовое письмо - SMTP настройки',
            from: [
                'address' => $this->setting->from_address,
                'name' => $this->setting->from_name,
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.smtp-test',
            with: [
                'setting' => $this->setting,
            ],
        );
    }
}
