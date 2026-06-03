<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use App\Models\Task;
use App\Models\Invitation;
use App\Models\File;
use App\Models\Company;

class ActivityLogger
{
    /**
     * Логируем создание задачи
     */
    public static function taskCreated(Task $task, User $author): Activity
    {
        // Проверяем, нет ли уже такого события за последние 5 секунд
        $exists = Activity::where('subject_type', Task::class)
            ->where('subject_id', $task->id)
            ->where('action', 'task_created')
            ->where('created_at', '>=', now()->subSeconds(5))
            ->exists();

        if ($exists) {
            return null;
        }

        return Activity::create([
            'user_id' => $author->id,
            'company_id' => $task->company_id,
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'action' => 'task_created',
            'description' => "{$author->name} создал задачу «{$task->name}»",
            'properties' => [
                'task_name' => $task->name,
                'task_id' => $task->id,
                'author_id' => $author->id,
                'author_name' => $author->name
            ]
        ]);
    }

    /**
     * Логируем назначение задачи
     */
    public static function taskAssigned(Task $task, User $assignedTo, User $assignedBy): Activity
    {
        // Проверяем дубликаты
        $exists = Activity::where('subject_type', Task::class)
            ->where('subject_id', $task->id)
            ->where('action', 'task_assigned')
            ->where('created_at', '>=', now()->subSeconds(5))
            ->exists();

        if ($exists) {
            return null;
        }

        return Activity::create([
            'user_id' => $assignedBy->id,
            'company_id' => $task->company_id,
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'action' => 'task_assigned',
            'description' => "{$assignedBy->name} назначил задачу «{$task->name}» пользователю {$assignedTo->name}",
            'properties' => [
                'task_name' => $task->name,
                'task_id' => $task->id,
                'assigned_to_id' => $assignedTo->id,
                'assigned_to_name' => $assignedTo->name,
                'assigned_by_id' => $assignedBy->id,
                'assigned_by_name' => $assignedBy->name
            ]
        ]);
    }

    /**
     * Логируем выполнение задачи
     */
    public static function taskCompleted(Task $task, User $user): Activity
    {
        $exists = Activity::where('subject_type', Task::class)
            ->where('subject_id', $task->id)
            ->where('action', 'task_completed')
            ->where('created_at', '>=', now()->subSeconds(5))
            ->exists();

        if ($exists) {
            return null;
        }

        return Activity::create([
            'user_id' => $user->id,
            'company_id' => $task->company_id,
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'action' => 'task_completed',
            'description' => "{$user->name} выполнил задачу «{$task->name}»",
            'properties' => [
                'task_name' => $task->name,
                'task_id' => $task->id,
                'completed_by' => $user->name
            ]
        ]);
    }

    /**
     * Логируем удаление задачи
     */
    public static function taskDeleted(Task $task, User $user): Activity
    {
        // Проверяем дубликаты
        $exists = Activity::where('subject_type', Task::class)
            ->where('subject_id', $task->id)
            ->where('action', 'task_deleted')
            ->where('created_at', '>=', now()->subSeconds(5))
            ->exists();

        if ($exists) {
            return null;
        }

        return Activity::create([
            'user_id' => $user->id,
            'company_id' => $task->company_id,
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'action' => 'task_deleted',
            'description' => "{$user->name} удалил задачу «{$task->name}»",
            'properties' => [
                'task_name' => $task->name,
                'task_id' => $task->id,
                'deleted_by_id' => $user->id,
                'deleted_by_name' => $user->name
            ]
        ]);
    }

    /**
     * Логируем загрузку файла
     */
    public static function fileUploaded(File $file, User $user): Activity
    {
        $exists = Activity::where('subject_type', File::class)
            ->where('subject_id', $file->id)
            ->where('action', 'file_uploaded')
            ->where('created_at', '>=', now()->subSeconds(5))
            ->exists();

        if ($exists) {
            return null;
        }

        $context = $file->task_id ? "к задаче" : "";
        $description = $file->task_id
            ? "{$user->name} загрузил файл «{$file->name}» к задаче"
            : "{$user->name} загрузил файл «{$file->name}» в компанию";

        return Activity::create([
            'user_id' => $user->id,
            'company_id' => $file->company_id,
            'subject_type' => File::class,
            'subject_id' => $file->id,
            'action' => 'file_uploaded',
            'description' => $description,
            'properties' => [
                'file_name' => $file->name,
                'file_id' => $file->id,
                'task_id' => $file->task_id
            ]
        ]);
    }

    /**
     * Логируем удаление файла
     */
    public static function fileDeleted(File $file, User $user): Activity
    {
        return Activity::create([
            'user_id' => $user->id,
            'company_id' => $file->company_id,
            'subject_type' => File::class,
            'subject_id' => $file->id,
            'action' => 'file_deleted',
            'description' => "{$user->name} удалил файл «{$file->name}»",
            'properties' => [
                'file_name' => $file->name,
                'file_id' => $file->id,
                'task_id' => $file->task_id
            ]
        ]);
    }

    /**
     * Логируем приглашение пользователя
     */
    public static function userInvited(Invitation $invitation, User $invitedBy): Activity
    {
        return Activity::create([
            'user_id' => $invitedBy->id,
            'company_id' => $invitation->company_id,
            'subject_type' => Invitation::class,
            'subject_id' => $invitation->id,
            'action' => 'user_invited',
            'description' => "{$invitedBy->name} пригласил пользователя {$invitation->email} в компанию",
            'properties' => [
                'email' => $invitation->email,
                'invited_by_name' => $invitedBy->name
            ]
        ]);
    }

    /**
     * Логируем присоединение пользователя к компании
     */
    public static function userJoined(User $user, Company $company): Activity
    {
        return Activity::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'action' => 'user_joined',
            'description' => "{$user->name} присоединился к компании «{$company->name}»",
            'properties' => [
                'user_name' => $user->name,
                'company_name' => $company->name
            ]
        ]);
    }

    /**
     * Логируем отказ от задачи
     */
    public static function taskRejected(Task $task, User $rejectedBy, ?string $reason = null): Activity
    {
        return Activity::create([
            'user_id' => $rejectedBy->id,
            'company_id' => $task->company_id,
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'action' => 'task_rejected',
            'description' => "{$rejectedBy->name} отказался от задачи «{$task->name}»" . ($reason ? " (причина: {$reason})" : ""),
            'properties' => [
                'task_name' => $task->name,
                'task_id' => $task->id,
                'reason' => $reason
            ]
        ]);
    }

    /**
     * Логируем изменение статуса задачи
     */
    public static function taskStatusChanged(Task $task, string $oldStatus, string $newStatus, User $user): Activity
    {
        return Activity::create([
            'user_id' => $user->id,
            'company_id' => $task->company_id,
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'action' => 'task_status_changed',
            'description' => "{$user->name} изменил статус задачи «{$task->name}» на «{$newStatus}»",
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $newStatus],
            'properties' => [
                'task_name' => $task->name,
                'task_id' => $task->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]
        ]);
    }

    /**
     * Логируем обновление задачи
     */
    public static function taskUpdated(Task $task, User $user, $changes = []): ?Activity
    {
        // Проверяем дубликаты
        $exists = Activity::where('subject_type', Task::class)
            ->where('subject_id', $task->id)
            ->where('action', 'task_updated')
            ->where('created_at', '>=', now()->subSeconds(5))
            ->exists();

        if ($exists) {
            return null;
        }

        // Обрабатываем изменения в зависимости от типа
        $changesText = '';
        if (is_array($changes)) {
            // Если пришел массив, формируем строку
            if (!empty($changes)) {
                $changesParts = [];
                foreach ($changes as $key => $value) {
                    if (is_array($value)) {
                        if (isset($value['old']) && isset($value['new'])) {
                            $changesParts[] = "{$key}: {$value['old']} → {$value['new']}";
                        }
                    } else {
                        $changesParts[] = "{$key}: {$value}";
                    }
                }
                $changesText = implode(', ', $changesParts);
            }
        } else if (is_string($changes) && !empty($changes)) {
            // Если пришла строка, используем её
            $changesText = $changes;
        }

        $description = "{$user->name} обновил задачу «{$task->name}»";
        if (!empty($changesText)) {
            $description .= " (изменения: {$changesText})";
        }

        return Activity::create([
            'user_id' => $user->id,
            'company_id' => $task->company_id,
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'action' => 'task_updated',
            'description' => $description,
            'properties' => [
                'task_name' => $task->name,
                'task_id' => $task->id,
                'updated_by_id' => $user->id,
                'updated_by_name' => $user->name,
                'changes' => $changesText
            ]
        ]);
    }

    /**
     * Логируем обновление задачи (с указанием старых и новых значений)
     */
    public static function taskUpdatedWithValues(Task $task, User $user, array $oldValues, array $newValues): ?Activity
    {
        // Проверяем дубликаты
        $exists = Activity::where('subject_type', Task::class)
            ->where('subject_id', $task->id)
            ->where('action', 'task_updated')
            ->where('created_at', '>=', now()->subSeconds(5))
            ->exists();

        if ($exists) {
            return null;
        }

        // Находим только реально изменившиеся поля
        $changedFields = [];
        foreach ($oldValues as $key => $oldValue) {
            if (isset($newValues[$key]) && $oldValue != $newValues[$key]) {
                $changedFields[$key] = [
                    'old' => $oldValue,
                    'new' => $newValues[$key]
                ];
            }
        }

        if (empty($changedFields)) {
            return null;
        }

        return Activity::create([
            'user_id' => $user->id,
            'company_id' => $task->company_id,
            'subject_type' => Task::class,
            'subject_id' => $task->id,
            'action' => 'task_updated',
            'description' => "{$user->name} обновил задачу «{$task->name}»",
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'properties' => [
                'task_name' => $task->name,
                'task_id' => $task->id,
                'updated_by_id' => $user->id,
                'updated_by_name' => $user->name,
                'changed_fields' => $changedFields
            ]
        ]);
    }
}
