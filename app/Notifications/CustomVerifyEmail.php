<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    public function __construct() {}

    // Указываем, что отправляем именно по почте (mail)
    public function via($notifiable)
    {
        return ['mail'];
    }

    // Здесь мы формируем само письмо
    public function toMail($notifiable)
    {
        // Генерируем стандартную защищенную ссылку Laravel для верификации
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Указываем наш собственный Blade-шаблон для письма
        return (new MailMessage)
            ->subject('Подтвердите ваш Email адрес')
            ->view('emails.verify', [
                'url' => $verificationUrl,
                'user' => $notifiable
            ]);
    }
}
