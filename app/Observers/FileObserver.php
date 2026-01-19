<?php

namespace App\Observers;

use App\Models\File;
use Illuminate\Support\Facades\Log; // Добавить
use Illuminate\Support\Facades\Storage; // Добавить

class FileObserver
{
    /**
     * Handle the File "created" event.
     */
    public function created(File $file): void
    {
        Log::info("Загружен файл: {$file->name} ({$file->size} bytes) пользователем {$file->uploaded_by}");
    }

    /**
     * Handle the File "deleted" event.
     */
    public function deleted(File $file): void
    {
        // Удаляем физический файл при мягком удалении записи
        if (Storage::disk($file->disk)->exists($file->path)) {
            Storage::disk($file->disk)->delete($file->path);
        }

        Log::info("Файл удален: {$file->name}");
    }

    /**
     * Handle the File "force deleted" event.
     */
    public function forceDeleted(File $file): void
    {
        // Гарантируем удаление файла при полном удалении
        if (Storage::disk($file->disk)->exists($file->path)) {
            Storage::disk($file->disk)->delete($file->path);
        }
    }
}
