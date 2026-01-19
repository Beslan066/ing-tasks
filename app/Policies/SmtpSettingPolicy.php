<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SmtpSetting;

class SmtpSettingPolicy
{
    public function view(User $user, SmtpSetting $setting): bool
    {
        return $setting->company_id === $user->company_id
            && ($setting->department_id === $user->department_id
                || $user->hasPermission('manage_company_smtp'));
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('manage_department_smtp')
            || $user->hasPermission('manage_company_smtp');
    }

    public function update(User $user, SmtpSetting $setting): bool
    {
        return $setting->company_id === $user->company_id
            && ($user->hasPermission('manage_company_smtp')
                || ($user->department_id === $setting->department_id
                    && $user->hasPermission('manage_department_smtp')));
    }

    public function delete(User $user, SmtpSetting $setting): bool
    {
        return $this->update($user, $setting);
    }

    public function test(User $user, SmtpSetting $setting): bool
    {
        return $this->update($user, $setting);
    }
}
