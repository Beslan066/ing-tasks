<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Task;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ваша команда для проверки просроченных задач
Artisan::command('tasks:check-overdue', function () {
    $count = Task::where('deadline', '<', now())
        ->whereNotIn('status', [Task::STATUS_COMPLETED, Task::STATUS_OVERDUE])
        ->update(['status' => Task::STATUS_OVERDUE]);

    $this->info("Updated {$count} tasks to overdue status.");
    \Log::info("Automatically updated {$count} tasks to overdue status.");
})->purpose('Check and update status of overdue tasks');
