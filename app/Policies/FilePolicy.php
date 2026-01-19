<?php


namespace App\Policies;

use App\Models\User;
use App\Models\File;

class FilePolicy
{
    /**
     * Determine whether the user can view the file.
     */
    public function view(User $user, File $file): bool
    {
        return $file->company_id === $user->company_id;
    }

    /**
     * Determine whether the user can download the file.
     */
    public function download(User $user, File $file): bool
    {
        return $file->company_id === $user->company_id
            && ($file->is_public
                || $file->department_id === $user->department_id
                || $user->hasPermission('download_all_files'));
    }

    /**
     * Determine whether the user can delete the file.
     */
    public function delete(User $user, File $file): bool
    {
        return $file->company_id === $user->company_id
            && ($file->uploaded_by === $user->id
                || $user->hasPermission('delete_files'));
    }
}
