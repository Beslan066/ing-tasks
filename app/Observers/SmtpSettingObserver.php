<?php

namespace App\Observers;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Log; // Добавить

class SmtpSettingObserver
{
    /**
     * Handle the SmtpSetting "created" event.
     */
    public function created(SmtpSetting $setting): void
    {
        Log::info("Созданы SMTP настройки для отдела {$setting->department_id}");
    }

    /**
     * Handle the SmtpSetting "updated" event.
     */
    public function updated(SmtpSetting $setting): void
    {
        if ($setting->wasChanged('is_active') && $setting->is_active) {
            Log::info("SMTP настройки активированы: {$setting->id}");
        }

        if ($setting->wasChanged('is_default') && $setting->is_default) {
            Log::info("SMTP настройки установлены по умолчанию: {$setting->id}");
        }
    }

    /**
     * Handle the SmtpSetting "deleted" event.
     */
    public function deleted(SmtpSetting $setting): void
    {
        Log::info("SMTP настройки удалены: {$setting->id}");
    }
}
