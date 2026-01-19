<?php

namespace App\Notifications;

use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewEmailNotification extends Notification
{
    use Queueable;

    protected $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Новое письмо в отделе: ' . $this->email->subject)
            ->line("Вы получили новое письмо от {$this->email->from_name}")
            ->line("Тема: {$this->email->subject}")
            ->action('Открыть письмо', route('departments.emails.show', [
                'department' => $this->email->department,
                'email' => $this->email
            ]))
            ->line('Спасибо за использование нашего сервиса!');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'email_id' => $this->email->id,
            'department_id' => $this->email->department_id,
            'subject' => $this->email->subject,
            'from' => $this->email->from_name,
            'message' => 'Новое письмо в отделе',
            'link' => route('departments.emails.show', [
                'department' => $this->email->department,
                'email' => $this->email
            ]),
        ];
    }
}
