<?php

namespace App\Observers;

use App\Models\Email;
use App\Models\EmailNotification;
use App\Notifications\NewEmailNotification;
use Illuminate\Support\Facades\Log; // Добавить эту строку
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage; // И эту

class EmailObserver
{
    /**
     * Handle the Email "created" event.
     */
    public function created(Email $email): void
    {
        // Если это не черновик и письмо отправлено через SMTP
        if (!$email->is_draft && $email->sent_at) {
            // Создаем уведомления для пользователей отдела
            $this->createNotifications($email);

            // Обновляем счетчик непрочитанных
            if ($email->department) {
                $email->department->increment('unread_emails_count');
            }
        }
    }

    /**
     * Handle the Email "updated" event.
     */
    public function updated(Email $email): void
    {
        // Если письмо было отправлено (из черновика в отправленное)
        if ($email->wasChanged('is_draft') && !$email->is_draft && $email->sent_at) {
            $this->createNotifications($email);
        }

        // Если письмо прочитано
        if ($email->wasChanged('is_read') && $email->is_read && $email->department) {
            $email->department->decrement('unread_emails_count');
        }

        // Если письмо помечено как непрочитанное
        if ($email->wasChanged('is_read') && !$email->is_read && $email->department) {
            $email->department->increment('unread_emails_count');
        }
    }

    /**
     * Handle the Email "deleted" event.
     */
    public function deleted(Email $email): void
    {
        // Удаляем связанные уведомления
        $email->notifications()->delete();

        // Если письмо не было прочитано, обновляем счетчик
        if (!$email->is_read && $email->department) {
            $email->department->decrement('unread_emails_count');
        }

        // Помечаем связанные файлы для удаления (мягкое удаление)
        $email->files()->delete();
    }

    /**
     * Handle the Email "restored" event.
     */
    public function restored(Email $email): void
    {
        // Восстанавливаем связанные уведомления
        $email->notifications()->restore();

        // Восстанавливаем связанные файлы
        $email->files()->restore();

        // Обновляем счетчик непрочитанных
        if (!$email->is_read && $email->department) {
            $email->department->increment('unread_emails_count');
        }
    }

    /**
     * Handle the Email "force deleted" event.
     */
    public function forceDeleted(Email $email): void
    {
        // Удаляем физически связанные файлы
        foreach ($email->files as $emailFile) {
            $file = $emailFile->file;
            if ($file) {
                Storage::disk($file->disk)->delete($file->path);
                $file->forceDelete();
            }
        }
    }

    private function createNotifications(Email $email): void
    {
        // Проверяем, существует ли отдел
        if (!$email->department) {
            Log::warning('Не удалось создать уведомления: отдел не найден', ['email_id' => $email->id]);
            return;
        }

        try {
            // Получаем пользователей отдела, которые должны получить уведомления
            $users = $email->department->users()
                ->where('users.id', '!=', $email->sent_by)
                ->whereHas('role', function ($query) {
                    $query->where('permissions->email_notifications', true);
                })
                ->get();

            foreach ($users as $user) {
                // Отправляем email уведомление
                Notification::send($user, new NewEmailNotification($email));

                // Создаем запись в БД для внутренних уведомлений
                EmailNotification::create([
                    'user_id' => $user->id,
                    'email_id' => $email->id,
                    'department_id' => $email->department_id,
                    'type' => 'new_email',
                    'data' => [
                        'subject' => $email->subject,
                        'from' => $email->from_name,
                        'preview' => \Illuminate\Support\Str::limit(strip_tags($email->body), 100),
                    ],
                ]);
            }

            Log::info('Уведомления созданы для письма', [
                'email_id' => $email->id,
                'department_id' => $email->department_id,
                'users_count' => $users->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка создания уведомлений: ' . $e->getMessage(), [
                'email_id' => $email->id,
                'exception' => $e
            ]);
        }
    }
}
