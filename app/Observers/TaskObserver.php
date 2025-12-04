<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    /**
     * Обработка перед сохранением задачи
     */
    public function saving(Task $task): void
    {
        // Автоматически проверяем просрочку при каждом сохранении
        if ($task->deadline &&
            $task->deadline->isPast() &&
            !in_array($task->status, [Task::STATUS_COMPLETED, Task::STATUS_OVERDUE])) {
            $task->status = Task::STATUS_OVERDUE;
        }
    }

    /**
     * Обработка при извлечении задачи из базы
     */
    public function retrieved(Task $task): void
    {
        // Автоматически обновляем статус если задача просрочена
        if ($task->isOverdue() && $task->status !== Task::STATUS_OVERDUE) {
            // Прямое обновление в базе чтобы избежать рекурсии
            Task::where('id', $task->id)
                ->update(['status' => Task::STATUS_OVERDUE]);
            // Обновляем статус в текущей модели
            $task->status = Task::STATUS_OVERDUE;
        }
    }
}
