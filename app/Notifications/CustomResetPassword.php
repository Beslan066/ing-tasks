<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends Notification
{
    use Queueable;

    public $token;

    // Передаем токен в конструктор
    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Формируем правильную ссылку сброса пароля (email передается через url)
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Подключаем наш Blade-шаблон
        return (new MailMessage)
            ->subject('Сброс пароля')
            ->view('emails.reset_password', [
                'url' => $resetUrl,
                'user' => $notifiable
            ]);
    }
}
