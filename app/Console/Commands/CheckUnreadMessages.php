<?php

namespace App\Console\Commands;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Notifications\UnreadMessagesNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckUnreadMessages extends Command
{
    protected $signature = 'messages:check-unread {--force : Принудительная отправка уведомлений}';
    protected $description = 'Проверяет непрочитанные сообщения и отправляет уведомления на почту';

    public function handle()
    {
        $this->info('🔄 Проверка непрочитанных сообщений...');

        // Находим все сообщения, которые были отправлены более 30 минут назад
        // и еще не были прочитаны
        $threshold = now()->subMinutes(30);

        // Получаем все непрочитанные сообщения
        $unreadMessages = Message::where('created_at', '<=', $threshold)
            ->whereDoesntHave('statuses', function ($query) {
                $query->where('status', 'read');
            })
            ->with(['chat', 'user'])
            ->get();

        if ($unreadMessages->isEmpty()) {
            $this->info('✅ Нет непрочитанных сообщений старше 30 минут.');
            return 0;
        }

        $this->info('📨 Найдено непрочитанных сообщений: ' . $unreadMessages->count());

        // Группируем по чатам и получателям
        $grouped = [];

        foreach ($unreadMessages as $message) {
            $chat = $message->chat;

            // Получаем всех участников чата, кроме отправителя
            $recipients = $chat->users()
                ->where('user_id', '!=', $message->user_id)
                ->get();

            foreach ($recipients as $recipient) {
                // Проверяем, прочитал ли получатель это сообщение
                $isRead = $message->statuses()
                    ->where('user_id', $recipient->id)
                    ->where('status', 'read')
                    ->exists();

                if (!$isRead) {
                    $key = $chat->id . '_' . $recipient->id;

                    if (!isset($grouped[$key])) {
                        $grouped[$key] = [
                            'chat' => $chat,
                            'recipient' => $recipient,
                            'sender' => $message->user,
                            'messages' => [],
                            'last_message' => null,
                        ];
                    }

                    $grouped[$key]['messages'][] = $message;

                    // Сохраняем последнее сообщение
                    if (!$grouped[$key]['last_message'] ||
                        $message->created_at > $grouped[$key]['last_message']->created_at) {
                        $grouped[$key]['last_message'] = $message;
                    }
                }
            }
        }

        $this->info('📧 Отправка уведомлений пользователям...');

        $sentCount = 0;

        foreach ($grouped as $data) {
            $recipient = $data['recipient'];
            $chat = $data['chat'];
            $sender = $data['sender'];
            $unreadCount = count($data['messages']);
            $lastMessage = $data['last_message'];

            // Проверяем, отправляли ли уведомление за последние 2 часа
            $lastNotification = \App\Models\Notification::where('notifiable_id', $recipient->id)
                ->where('type', UnreadMessagesNotification::class)
                ->where('created_at', '>=', now()->subHours(2))
                ->where('data->chat_id', $chat->id)
                ->first();

            if ($lastNotification && !$this->option('force')) {
                $this->warn("⏳ Пропускаем {$recipient->email} - уведомление отправлено менее 2 часов назад");
                continue;
            }

            try {
                $recipient->notify(new UnreadMessagesNotification(
                    $chat,
                    $sender,
                    $unreadCount,
                    $lastMessage ? $this->getMessagePreview($lastMessage) : '...'
                ));

                $sentCount++;
                $this->info("✅ Отправлено уведомление для {$recipient->email} ({$unreadCount} сообщений)");

            } catch (\Exception $e) {
                $this->error("❌ Ошибка отправки для {$recipient->email}: " . $e->getMessage());
                Log::error('Ошибка отправки уведомления о непрочитанных сообщениях: ' . $e->getMessage());
            }
        }

        $this->info("🎉 Уведомления отправлены: {$sentCount}");
        return 0;
    }

    private function getMessagePreview(Message $message): string
    {
        if ($message->type === 'file') {
            return '📎 Отправлен файл: ' . ($message->file_name ?? 'вложение');
        }

        if ($message->type === 'image') {
            return '🖼️ Отправлено изображение';
        }

        if ($message->type === 'system') {
            return 'ℹ️ Системное сообщение';
        }

        $content = $message->content ?? '';
        $preview = mb_strlen($content) > 100 ? mb_substr($content, 0, 100) . '...' : $content;

        return $preview ?: '📝 Сообщение';
    }
}
