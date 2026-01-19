<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EmailTemplate;

class EmailTemplatePolicy
{
    public function view(User $user, EmailTemplate $template): bool
    {
        if ($template->is_global && $user->hasPermission('view_global_templates')) {
            return true;
        }

        return $template->company_id === $user->company_id
            && ($template->department_id === null
                || $template->department_id === $user->department_id
                || $user->hasPermission('view_all_templates'));
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_templates');
    }

    public function update(User $user, EmailTemplate $template): bool
    {
        if ($template->created_by === $user->id) {
            return true;
        }

        if ($template->is_global && $user->hasPermission('edit_global_templates')) {
            return true;
        }

        return $template->company_id === $user->company_id
            && $user->hasPermission('edit_templates')
            && ($template->department_id === null
                || $template->department_id === $user->department_id);
    }

    public function delete(User $user, EmailTemplate $template): bool
    {
        if ($template->created_by === $user->id) {
            return true;
        }

        return $template->company_id === $user->company_id
            && $user->hasPermission('delete_templates');
    }
}
