<?php

namespace App\Services;

use App\Models\Email;
use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Mail;
use App\Mail\DepartmentEmail;

class EmailService
{
    public function sendViaSmtp(Email $email, SmtpSetting $smtpSetting): bool
    {
        try {
            config([
                'mail.mailers.smtp_department' => [
                    'transport' => 'smtp',
                    'host' => $smtpSetting->host,
                    'port' => $smtpSetting->port,
                    'encryption' => $smtpSetting->encryption,
                    'username' => $smtpSetting->username,
                    'password' => $smtpSetting->password,
                    'timeout' => null,
                ],
            ]);

            $mail = new DepartmentEmail($email);

            Mail::mailer('smtp_department')
                ->to($email->to_emails)
                ->cc($email->cc_emails)
                ->bcc($email->bcc_emails)
                ->send($mail);

            // Обновляем статус письма
            $email->update([
                'sent_at' => now(),
                'is_draft' => false,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('SMTP отправка не удалась: ' . $e->getMessage());
            return false;
        }
    }

    public function syncExternalEmails(Department $department): void
    {
        // Здесь можно реализовать синхронизацию с внешним почтовым ящиком
        // через IMAP или API почтового сервиса
    }
}
