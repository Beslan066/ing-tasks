<?php

namespace App\Jobs;

use App\Models\File;
use App\Models\StorageUsage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessFileUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 минут
    public $tries = 3;

    protected $file;
    protected $path;
    protected $userId;
    protected $companyId;
    protected $departmentId;
    protected $folder;

    public function __construct($file, $path, $userId, $companyId, $departmentId, $folder = null)
    {
        $this->file = $file;
        $this->path = $path;
        $this->userId = $userId;
        $this->companyId = $companyId;
        $this->departmentId = $departmentId;
        $this->folder = $folder;
    }

    public function handle()
    {
        try {
            // Сохраняем файл
            $fileContent = file_get_contents($this->file);
            Storage::disk('public')->put($this->path, $fileContent);

            $fileSize = strlen($fileContent);

            // Обновляем статистику хранилища
            $storageUsage = StorageUsage::where('company_id', $this->companyId)->first();
            if ($storageUsage) {
                $storageUsage->increment('used_storage', $fileSize);
                $storageUsage->increment('file_count');
                $storageUsage->save();
            }

            // Определяем тип файла
            $mimeType = mime_content_type($this->file);
            $extension = pathinfo($this->file, PATHINFO_EXTENSION);

            // Создаем запись в базе данных
            File::create([
                'name' => pathinfo($this->file, PATHINFO_FILENAME),
                'path' => $this->path,
                'size' => $fileSize,
                'mime_type' => $mimeType,
                'extension' => $extension,
                'uploaded_by' => $this->userId,
                'company_id' => $this->companyId,
                'department_id' => $this->departmentId,
                'disk' => 'public',
                'folder' => $this->folder,
                'is_public' => false,
            ]);

        } catch (\Exception $e) {
            // Логируем ошибку
            \Log::error('Ошибка загрузки файла: ' . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        // Очищаем временные файлы при неудаче
        if (Storage::disk('public')->exists($this->path)) {
            Storage::disk('public')->delete($this->path);
        }

        \Log::error('Job ProcessFileUpload failed: ' . $exception->getMessage());
    }
}
