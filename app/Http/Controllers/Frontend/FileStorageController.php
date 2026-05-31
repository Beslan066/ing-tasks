<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\StorageUsage;
use App\Models\Company;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FileStorageController extends Controller
{
    // Константы с лимитами хранения (в байтах)
    const BASIC_LIMIT = 1073741824;      // 1GB
    const OPTIMAL_LIMIT = 107374182400;  // 100GB
    const PREMIUM_LIMIT = 1073741824000; // 1000GB (1TB)

    // Константы с максимальным размером файла (в байтах)
    const BASIC_FILE_MAX = 104857600;    // 100MB
    const OPTIMAL_FILE_MAX = 524288000;  // 500MB
    const PREMIUM_FILE_MAX = 1073741824; // 1GB

    /**
     * Отображение страницы файлового менеджера
     */
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        // Получаем или создаем запись об использовании хранилища
        $storageUsage = StorageUsage::firstOrCreate(
            ['company_id' => $company->id],
            [
                'total_storage_limit' => $this->getStorageLimitByLicense($company->license_type),
                'used_storage' => 0,
                'file_count' => 0,
                'license_type' => $company->license_type
            ]
        );

        // Получаем файлы компании
        $files = File::where('company_id', $company->id)
            ->with('uploadedBy')
            ->latest()
            ->paginate(7);

        // Группируем файлы по типам для статистики
        $fileStats = $this->getFileStatistics($company->id);

        // Получаем максимальный размер файла для текущей подписки
        $maxFileSizeBytes = $this->getMaxFileSizeByLicense($company->license_type);
        $maxFileSizeFormatted = $this->formatBytes($maxFileSizeBytes);

        return view('frontend.file-manager.index', compact(
            'files',
            'storageUsage',
            'fileStats',
            'company',
            'maxFileSizeBytes',
            'maxFileSizeFormatted'
        ));
    }

    /**
     * Загрузка файла
     */
    public function upload(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        // Получаем максимальный размер файла для текущей подписки
        $maxFileSize = $this->getMaxFileSizeByLicense($company->license_type);
        $maxFileSizeMB = $maxFileSize / 1048576;

        // Валидация с динамическим максимальным размером
        $request->validate([
            'file' => 'required|file|max:' . $maxFileSize,
            'folder' => 'nullable|string|max:255'
        ], [
            'file.max' => 'Максимальный размер файла для вашего тарифа (' . $company->getLicenseTypeName() . ') составляет ' . $this->formatBytes($maxFileSize) . '. Ваш файл превышает этот лимит.'
        ]);

        // Проверяем лицензию и права доступа
        if (!$this->checkUploadPermission($user)) {
            return back()->with('error', 'У вас нет прав для загрузки файлов');
        }

        // Получаем информацию о хранилище
        $storageUsage = StorageUsage::firstOrCreate(
            ['company_id' => $company->id],
            [
                'total_storage_limit' => $this->getStorageLimitByLicense($company->license_type),
                'used_storage' => 0,
                'file_count' => 0,
                'license_type' => $company->license_type
            ]
        );

        $file = $request->file('file');
        $fileSize = $file->getSize();

        // Проверяем, не превышен ли лимит хранилища
        if (!$storageUsage->canUploadFile($fileSize)) {
            $freeSpace = $storageUsage->getFormattedFreeStorage();
            return back()->with('error', "Превышен лимит хранилища. Свободно: {$freeSpace}");
        }

        // Генерируем уникальное имя файла
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = pathinfo($originalName, PATHINFO_FILENAME);
        $uniqueName = $fileName . '_' . Str::random(10) . '.' . $extension;

        // Определяем папку для сохранения
        $folder = $request->input('folder', 'uploads');
        $path = $file->storeAs("companies/{$company->id}/{$folder}", $uniqueName, 'public');

        // Создаем запись в базе данных
        $fileRecord = File::create([
            'name' => $originalName,
            'path' => $path,
            'size' => $fileSize,
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
            'uploaded_by' => $user->id,
            'company_id' => $company->id,
            'department_id' => $user->department_id,
            'disk' => 'public',
            'folder' => $folder,
            'is_public' => false,
        ]);

        // Логируем загрузку файла
        ActivityLogger::fileUploaded($fileRecord, $user);

        // Обновляем статистику использования хранилища
        $storageUsage->increment('used_storage', $fileSize);
        $storageUsage->increment('file_count');

        return back()->with('success', 'Файл "' . $originalName . '" успешно загружен');
    }

    /**
     * Загрузка файла через AJAX
     */
    public function uploadAjax(Request $request)
    {
        try {
            $user = Auth::user();
            $company = $user->company;

            // Получаем максимальный размер файла для текущей подписки
            $maxFileSize = $this->getMaxFileSizeByLicense($company->license_type);

            // Валидация с динамическим максимальным размером
            $request->validate([
                'file' => 'required|file|max:' . $maxFileSize,
                'folder' => 'nullable|string|max:255'
            ], [
                'file.max' => 'Максимальный размер файла для вашего тарифа (' . $company->getLicenseTypeName() . ') составляет ' . $this->formatBytes($maxFileSize)
            ]);

            // Проверяем права
            if (!$this->checkUploadPermission($user)) {
                return response()->json(['error' => 'У вас нет прав для загрузки файлов'], 403);
            }

            // Получаем информацию о хранилище
            $storageUsage = StorageUsage::firstOrCreate(
                ['company_id' => $company->id],
                [
                    'total_storage_limit' => $this->getStorageLimitByLicense($company->license_type),
                    'used_storage' => 0,
                    'file_count' => 0,
                    'license_type' => $company->license_type
                ]
            );

            $file = $request->file('file');
            $fileSize = $file->getSize();

            // Проверяем лимит хранилища
            if (!$storageUsage->canUploadFile($fileSize)) {
                $freeSpace = $storageUsage->getFormattedFreeStorage();
                return response()->json(['error' => "Превышен лимит хранилища. Свободно: {$freeSpace}"], 400);
            }

            // Генерируем уникальное имя файла
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);
            $uniqueName = $fileName . '_' . Str::random(10) . '.' . $extension;

            // Определяем папку для сохранения
            $folder = $request->input('folder', 'uploads');
            $path = $file->storeAs("companies/{$company->id}/{$folder}", $uniqueName, 'public');

            // Создаем запись в базе данных
            $fileRecord = File::create([
                'name' => $originalName,
                'path' => $path,
                'size' => $fileSize,
                'mime_type' => $file->getMimeType(),
                'extension' => $extension,
                'uploaded_by' => $user->id,
                'company_id' => $company->id,
                'department_id' => $user->department_id,
                'disk' => 'public',
                'folder' => $folder,
                'is_public' => false,
            ]);

            // Обновляем статистику
            $storageUsage->increment('used_storage', $fileSize);
            $storageUsage->increment('file_count');

            return response()->json([
                'success' => true,
                'message' => 'Файл "' . $originalName . '" успешно загружен',
                'file' => [
                    'name' => $originalName,
                    'size' => $this->formatBytes($fileSize),
                    'type' => $file->getMimeType()
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()['file'][0] ?? 'Ошибка валидации'], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Произошла ошибка при загрузке файла: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Получение максимального размера файла по типу лицензии
     */
    private function getMaxFileSizeByLicense($licenseType): int
    {
        return match($licenseType) {
            'basic' => self::BASIC_FILE_MAX,
            'optimal' => self::OPTIMAL_FILE_MAX,
            'premium' => self::PREMIUM_FILE_MAX,
            default => self::BASIC_FILE_MAX
        };
    }

    /**
     * Форматирование байтов
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Скачивание файла
     */
    public function download(File $file)
    {
        $user = Auth::user();

        // Проверяем доступ к файлу
        if (!$this->checkFileAccess($user, $file)) {
            abort(403, 'Доступ запрещен');
        }

        if (!Storage::disk($file->disk)->exists($file->path)) {
            abort(404, 'Файл не найден');
        }

        return Storage::disk($file->disk)->download($file->path, $file->name);
    }

    /**
     * Просмотр файла
     */
    public function view(File $file)
    {
        $user = Auth::user();

        if (!$this->checkFileAccess($user, $file)) {
            abort(403, 'Доступ запрещен');
        }

        if (!Storage::disk($file->disk)->exists($file->path)) {
            abort(404, 'Файл не найден');
        }

        $filePath = Storage::disk($file->disk)->path($file->path);

        return response()->file($filePath);
    }

    /**
     * Удаление файла
     */
    public function destroy(File $file)
    {
        $user = Auth::user();

        // Проверяем права на удаление
        if (!$this->checkDeletePermission($user, $file)) {
            abort(403, 'У вас нет прав для удаления этого файла');
        }

        // Уменьшаем использование хранилища
        $storageUsage = StorageUsage::where('company_id', $file->company_id)->first();
        if ($storageUsage) {
            $storageUsage->decrement('used_storage', $file->size);
            $storageUsage->decrement('file_count');
        }

        // Логируем удаление файла
        ActivityLogger::fileDeleted($file, $user);

        // Удаляем физический файл
        Storage::disk($file->disk)->delete($file->path);

        // Удаляем запись из базы данных
        $file->delete();

        return back()->with('success', 'Файл успешно удален');
    }

    /**
     * Получение статистики файлов
     */
    public function getStatistics()
    {
        $user = Auth::user();
        $company = $user->company;

        $storageUsage = StorageUsage::where('company_id', $company->id)->first();
        $fileStats = $this->getFileStatistics($company->id);

        return response()->json([
            'storage_usage' => [
                'used' => $storageUsage->getFormattedUsedStorage(),
                'total' => $storageUsage->getFormattedTotalStorage(),
                'free' => $storageUsage->getFormattedFreeStorage(),
                'percentage' => $storageUsage->getUsagePercentage(),
                'file_count' => $storageUsage->file_count
            ],
            'file_stats' => $fileStats,
            'license_type' => $company->getLicenseTypeName()
        ]);
    }

    /**
     * Проверка прав на загрузку файлов
     */
    private function checkUploadPermission($user): bool
    {
        // Руководитель, менеджер и сотрудник могут загружать файлы
        return in_array($user->role->name, ['Руководитель', 'Менеджер', 'Сотрудник']);
    }

    /**
     * Проверка доступа к файлу
     */
    private function checkFileAccess($user, $file): bool
    {
        // Руководитель имеет доступ ко всем файлам компании
        if ($user->role->name === 'Руководитель') {
            return $file->company_id === $user->company_id;
        }

        // Менеджер имеет доступ к файлам своего отдела и общедоступным файлам
        if ($user->role->name === 'Менеджер') {
            return $file->company_id === $user->company_id &&
                ($file->department_id === $user->department_id || $file->is_public);
        }

        // Сотрудник имеет доступ только к своим файлам и общедоступным файлам своего отдела
        if ($user->role->name === 'Сотрудник') {
            return $file->company_id === $user->company_id &&
                ($file->uploaded_by === $user->id ||
                    ($file->department_id === $user->department_id && $file->is_public));
        }

        return false;
    }

    /**
     * Проверка прав на удаление файла
     */
    private function checkDeletePermission($user, $file): bool
    {
        // Руководитель может удалять любые файлы компании
        if ($user->role->name === 'Руководитель') {
            return $file->company_id === $user->company_id;
        }

        // Менеджер может удалять файлы своего отдела
        if ($user->role->name === 'Менеджер') {
            return $file->company_id === $user->company_id &&
                $file->department_id === $user->department_id;
        }

        // Сотрудник может удалять только свои файлы
        if ($user->role->name === 'Сотрудник') {
            return $file->company_id === $user->company_id &&
                $file->uploaded_by === $user->id;
        }

        return false;
    }

    /**
     * Получение лимита хранилища по типу лицензии
     */
    private function getStorageLimitByLicense($licenseType): int
    {
        return match($licenseType) {
            'basic' => self::BASIC_LIMIT,
            'optimal' => self::OPTIMAL_LIMIT,
            'premium' => self::PREMIUM_LIMIT,
            default => self::BASIC_LIMIT
        };
    }

    /**
     * Получение статистики файлов по типам
     */
    private function getFileStatistics($companyId): array
    {
        $files = File::where('company_id', $companyId)->get();

        $stats = [
            'images' => ['count' => 0, 'size' => 0],
            'videos' => ['count' => 0, 'size' => 0],
            'documents' => ['count' => 0, 'size' => 0],
            'audio' => ['count' => 0, 'size' => 0],
            'archives' => ['count' => 0, 'size' => 0],
            'other' => ['count' => 0, 'size' => 0],
        ];

        foreach ($files as $file) {
            $type = $this->getFileType($file->mime_type, $file->extension);
            $stats[$type]['count']++;
            $stats[$type]['size'] += $file->size;
        }

        return $stats;
    }

    /**
     * Определение типа файла
     */
    private function getFileType($mimeType, $extension): string
    {
        $imageMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        $videoMimes = ['video/mp4', 'video/mpeg', 'video/ogg', 'video/webm', 'video/quicktime'];
        $audioMimes = ['audio/mpeg', 'audio/ogg', 'audio/wav', 'audio/webm'];
        $documentMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $archiveMimes = ['application/zip', 'application/x-rar-compressed', 'application/x-tar', 'application/gzip'];

        if (in_array($mimeType, $imageMimes) || in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
            return 'images';
        }

        if (in_array($mimeType, $videoMimes) || in_array($extension, ['mp4', 'avi', 'mov', 'wmv', 'flv'])) {
            return 'videos';
        }

        if (in_array($mimeType, $audioMimes) || in_array($extension, ['mp3', 'wav', 'ogg', 'flac'])) {
            return 'audio';
        }

        if (in_array($mimeType, $documentMimes) || in_array($extension, ['pdf', 'doc', 'docx', 'txt', 'rtf'])) {
            return 'documents';
        }

        if (in_array($mimeType, $archiveMimes) || in_array($extension, ['zip', 'rar', 'tar', 'gz'])) {
            return 'archives';
        }

        return 'other';
    }
}
