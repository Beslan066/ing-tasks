<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;

class CheckOverdueTasks extends Command
{
    protected $signature = 'tasks:check-overdue';
    protected $description = 'Check and update status of overdue tasks';

    public function handle(): void
    {
        $count = Task::where('deadline', '<', now())
            ->whereNotIn('status', [Task::STATUS_COMPLETED, Task::STATUS_OVERDUE])
            ->update(['status' => Task::STATUS_OVERDUE]);

        $this->info("Updated {$count} tasks to overdue status.");

        // Логируем результат
        \Log::info("Updated {$count} tasks to overdue status.");
    }
}
