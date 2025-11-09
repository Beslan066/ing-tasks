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
        $taskUrl = route('tasks.view', $this->task->id);

        return (new MailMessage)
            ->subject('Вам назначена новая задача: ' . $this->task->name)
            ->greeting('Здравствуйте, ' . $notifiable->name . '!')
            ->line('Вам была назначена новая задача.')
            ->line('**Задача:** ' . $this->task->name)
            ->line('**Приоритет:** ' . $this->task->priority)
            ->line('**Дедлайн:** ' . ($this->task->deadline ? $this->task->deadline->format('d.m.Y H:i') : 'Не установлен'))
            ->line('**Статус:** ' . $this->task->status)
            ->action('Перейти к задаче', $taskUrl)
            ->line('Спасибо за использование нашего приложения!');
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
