<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Chat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnreadMessagesNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Chat $chat,
        public User $sender,
        public int $unreadCount,
        public string $messagePreview
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $chatUrl = route('chat.index') . '?chat=' . $this->chat->id;

        // Определяем тип чата для текста
        $chatType = match($this->chat->type) {
            'private' => 'личное сообщение',
            'group' => 'групповой чат',
            'department' => 'чат отдела',
            'company' => 'общий чат компании',
            default => 'чат'
        };

        $senderName = $this->chat->type === 'private'
            ? $this->sender->name
            : $this->chat->name;

        return (new MailMessage)
            ->subject('📩 У вас ' . $this->unreadCount . ' непрочитанных сообщений')
            ->greeting('Здравствуйте, ' . $notifiable->name . '!')
            ->line('У вас **' . $this->unreadCount . '** непрочитанных сообщений в ' . $chatType . ' "' . $senderName . '"')
            ->line('**Последнее сообщение:**')
            ->line('"' . $this->messagePreview . '"')
            ->line('')
            ->line('📱 Перейдите в чат, чтобы прочитать все сообщения.')
            ->action('Перейти к чату', $chatUrl)
            ->line('')
            ->line('*Это уведомление отправлено, потому что вы не прочитали сообщения более 30 минут.*')
            ->line('С уважением, команда МенеджерПлюс!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'chat_id' => $this->chat->id,
            'chat_name' => $this->chat->getDisplayName(),
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'unread_count' => $this->unreadCount,
            'message_preview' => $this->messagePreview,
            'type' => 'unread_messages',
        ];
    }
}
