<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailService;
use App\Models\Department;

class SyncExternalEmails extends Command
{
    protected $signature = 'emails:sync {--department= : ID отдела}';
    protected $description = 'Синхронизация внешней почты';

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    public function handle()
    {
        $departmentId = $this->option('department');

        $query = Department::query();
        if ($departmentId) {
            $query->where('id', $departmentId);
        }

        $departments = $query->get();

        foreach ($departments as $department) {
            $this->info("Синхронизация почты для отдела: {$department->name}");
            $this->emailService->syncExternalEmails($department);
            $this->info("Синхронизация завершена");
        }

        $this->info('Все отделы синхронизированы');
    }
}
