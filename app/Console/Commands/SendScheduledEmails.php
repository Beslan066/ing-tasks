<?php

namespace App\Console\Commands;

use App\Models\Email;
use App\Services\EmailService;
use Illuminate\Console\Command;

class SendScheduledEmails extends Command
{
    protected $signature = 'emails:send-scheduled';
    protected $description = 'Отправка запланированных писем';

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    public function handle()
    {
        $scheduledEmails = Email::where('is_draft', true)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->with('department')
            ->get();

        foreach ($scheduledEmails as $email) {
            try {
                // Превращаем черновик в отправленное письмо
                $email->update([
                    'is_draft' => false,
                    'sent_at' => now(),
                    'scheduled_at' => null,
                ]);

                $this->info("Отправлено запланированное письмо: {$email->subject}");
            } catch (\Exception $e) {
                $this->error("Ошибка отправки письма {$email->id}: " . $e->getMessage());
            }
        }
    }
}
