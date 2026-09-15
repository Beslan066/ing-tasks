<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $taskUrl = route('welcome');

        [$priorityLabel, $priorityColor, $priorityBg, $priorityBorder] = $this->priorityStyle($this->task->priority);

        return (new MailMessage)
            ->subject('Вам назначена новая задача: ' . $this->task->name)
            ->view('emails.task-assigned', [
                'userName' => $notifiable->name,
                'taskName' => $this->task->name,
                'statusLabel' => $this->task->status,
                'deadline' => $this->task->deadline
                    ? $this->task->deadline->format('d.m.Y H:i')
                    : 'Не установлен',
                'taskUrl' => $taskUrl,
                'priorityLabel' => $priorityLabel,
                'priorityColor' => $priorityColor,
                'priorityBg' => $priorityBg,
                'priorityBorder' => $priorityBorder,
            ]);
    }

    /**
     * Цветовая схема бейджа приоритета.
     * Верните [подпись, цвет текста, фон, рамка].
     */
    protected function priorityStyle(string $priority): array
    {
        return match ($priority) {
            'high', 'Высокий' => ['Высокий', '#b91c1c', '#fef2f2', '#fecaca'],
            'medium', 'Средний' => ['Средний', '#92400e', '#fffbeb', '#fde68a'],
            'low', 'Низкий' => ['Низкий', '#166534', '#f0fdf4', '#bbf7d0'],
            default => [$priority, '#334155', '#f8fafc', '#e2e8f0'],
        };
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_name' => $this->task->name,
            'message' => 'Вам назначена новая задача: ' . $this->task->name,
        ];
    }
}
