<?php


namespace App\Policies;

use App\Models\User;
use App\Models\Email;

class EmailPolicy
{
    /**
     * Determine whether the user can view the email.
     */
    public function view(User $user, Email $email): bool
    {
        // Пользователь может просматривать письма своего отдела
        return $email->department_id === $user->department_id
            || $user->hasPermission('view_all_emails');
    }

    /**
     * Determine whether the user can create emails.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('send_emails');
    }

    /**
     * Determine whether the user can update the email.
     */
    public function update(User $user, Email $email): bool
    {
        // Только отправитель может редактировать черновики
        return $email->sent_by === $user->id
            && $email->is_draft
            && $user->hasPermission('edit_own_emails');
    }

    /**
     * Determine whether the user can delete the email.
     */
    public function delete(User $user, Email $email): bool
    {
        // Пользователь может удалять свои письма
        if ($email->sent_by === $user->id && $user->hasPermission('delete_own_emails')) {
            return true;
        }

        // Руководитель или менеджер может удалять любые письма отдела
        if ($email->department_id === $user->department_id) {
            return $user->isManager() || $user->isLeader() || $user->hasPermission('delete_all_emails');
        }

        return false;
    }

    /**
     * Determine whether the user can archive the email.
     */
    public function archive(User $user, Email $email): bool
    {
        // Пользователь может архивировать свои письма
        if ($email->sent_by === $user->id && $user->hasPermission('archive_own_emails')) {
            return true;
        }

        // Руководитель или менеджер может архивировать любые письма отдела
        if ($email->department_id === $user->department_id) {
            return $user->isManager() || $user->isLeader() || $user->hasPermission('archive_all_emails');
        }

        return false;
    }

    /**
     * Determine whether the user can restore the email.
     */
    public function restore(User $user, Email $email): bool
    {
        // Восстанавливать может только тот, кто удалил, или руководитель
        if ($email->deleted_by === $user->id) {
            return true;
        }

        if ($email->department_id === $user->department_id) {
            return $user->isManager() || $user->isLeader() || $user->hasPermission('restore_emails');
        }

        return false;
    }

    /**
     * Determine whether the user can force delete the email.
     */
    public function forceDelete(User $user, Email $email): bool
    {
        // Полное удаление только для администраторов
        return $user->hasRole('Администратор') || $user->hasPermission('force_delete_emails');
    }

    /**
     * Determine whether the user can view deleted emails.
     */
    public function viewTrashed(User $user): bool
    {
        return $user->isManager() || $user->isLeader() || $user->hasPermission('view_deleted_emails');
    }
}
