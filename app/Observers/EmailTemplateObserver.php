<?php

namespace App\Observers;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Log; // Добавить

class EmailTemplateObserver
{
    /**
     * Handle the EmailTemplate "created" event.
     */
    public function created(EmailTemplate $template): void
    {
        Log::info("Создан шаблон письма: {$template->name} пользователем {$template->created_by}");
    }

    /**
     * Handle the EmailTemplate "updated" event.
     */
    public function updated(EmailTemplate $template): void
    {
        if ($template->wasChanged('is_active') && !$template->is_active) {
            Log::info("Шаблон письма деактивирован: {$template->name}");
        }
    }

    /**
     * Handle the EmailTemplate "deleted" event.
     */
    public function deleted(EmailTemplate $template): void
    {
        Log::info("Шаблон письма удален: {$template->name}");
    }
}
