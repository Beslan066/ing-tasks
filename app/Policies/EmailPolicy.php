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
        // Отправитель может удалять свои письма
        if ($email->sent_by === $user->id && $user->hasPermission('delete_own_emails')) {
            return true;
        }

        // Руководитель или администратор может удалять любые письма отдела
        return $email->department_id === $user->department_id
            && $user->hasPermission('delete_all_emails');
    }

    /**
     * Determine whether the user can archive the email.
     */
    public function archive(User $user, Email $email): bool
    {
        return $email->department_id === $user->department_id
            && $user->hasPermission('archive_emails');
    }
}
