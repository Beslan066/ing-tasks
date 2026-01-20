<?php


namespace App\Services;

use App\Models\Company;
use App\Models\StorageUsage;
use Illuminate\Support\Facades\Log;

class StorageService
{
    const LICENSE_TYPES = [
        'basic' => [
            'name' => 'Базовый',
            'limit' => 1073741824, // 1GB
            'max_file_size' => 104857600, // 100MB
        ],
        'optimal' => [
            'name' => 'Оптимальный',
            'limit' => 107374182400, // 100GB
            'max_file_size' => 536870912, // 500MB
        ],
        'premium' => [
            'name' => 'Премиум',
            'limit' => 1073741824000, // 1000GB (1TB)
            'max_file_size' => 1073741824, // 1GB
        ],
    ];

    /**
     * Инициализация хранилища для компании
     */
    public function initializeCompanyStorage(Company $company): StorageUsage
    {
        $licenseType = $company->license_type ?? 'basic';

        return StorageUsage::updateOrCreate(
            ['company_id' => $company->id],
            [
                'total_storage_limit' => self::LICENSE_TYPES[$licenseType]['limit'],
                'used_storage' => 0,
                'file_count' => 0,
                'license_type' => $licenseType
            ]
        );
    }

    /**
     * Проверка возможности загрузки файла
     */
    public function canUploadFile(Company $company, int $fileSize): array
    {
        $storageUsage = $this->getOrCreateStorageUsage($company);
        $licenseType = $company->license_type ?? 'basic';

        $maxFileSize = self::LICENSE_TYPES[$licenseType]['max_file_size'];

        if ($fileSize > $maxFileSize) {
            return [
                'can_upload' => false,
                'message' => 'Размер файла превышает максимально допустимый для вашего тарифа (' .
                    $this->formatBytes($maxFileSize) . ')'
            ];
        }

        if (!$storageUsage->canUploadFile($fileSize)) {
            return [
                'can_upload' => false,
                'message' => 'Превышен лимит хранилища. Доступно: ' . $storageUsage->getFormattedFreeStorage()
            ];
        }

        return [
            'can_upload' => true,
            'message' => ''
        ];
    }

    /**
     * Обновление использования хранилища после загрузки файла
     */
    public function updateStorageAfterUpload(Company $company, int $fileSize): StorageUsage
    {
        $storageUsage = $this->getOrCreateStorageUsage($company);

        $storageUsage->increment('used_storage', $fileSize);
        $storageUsage->increment('file_count');

        return $storageUsage->fresh();
    }

    /**
     * Обновление использования хранилища после удаления файла
     */
    public function updateStorageAfterDelete(Company $company, int $fileSize): StorageUsage
    {
        $storageUsage = $this->getOrCreateStorageUsage($company);

        $storageUsage->decrement('used_storage', $fileSize);
        $storageUsage->decrement('file_count');

        // Гарантируем, что used_storage не станет отрицательным
        if ($storageUsage->used_storage < 0) {
            $storageUsage->used_storage = 0;
            $storageUsage->save();
        }

        return $storageUsage->fresh();
    }

    /**
     * Изменение тарифного плана компании
     */
    public function changeLicenseType(Company $company, string $newLicenseType): array
    {
        if (!array_key_exists($newLicenseType, self::LICENSE_TYPES)) {
            return [
                'success' => false,
                'message' => 'Некорректный тип лицензии'
            ];
        }

        $storageUsage = $this->getOrCreateStorageUsage($company);
        $newLimit = self::LICENSE_TYPES[$newLicenseType]['limit'];

        // Проверяем, не превышает ли текущее использование новый лимит
        if ($storageUsage->used_storage > $newLimit) {
            return [
                'success' => false,
                'message' => 'Текущее использование хранилища превышает новый лимит. ' .
                    'Удалите некоторые файлы перед изменением тарифа.'
            ];
        }

        // Обновляем тариф
        $company->license_type = $newLicenseType;
        $company->save();

        $storageUsage->license_type = $newLicenseType;
        $storageUsage->total_storage_limit = $newLimit;
        $storageUsage->save();

        return [
            'success' => true,
            'message' => 'Тариф успешно изменен на ' . self::LICENSE_TYPES[$newLicenseType]['name']
        ];
    }

    /**
     * Получение статистики хранилища
     */
    public function getStorageStats(Company $company): array
    {
        $storageUsage = $this->getOrCreateStorageUsage($company);
        $licenseType = $company->license_type ?? 'basic';

        return [
            'license_type' => [
                'code' => $licenseType,
                'name' => self::LICENSE_TYPES[$licenseType]['name'],
                'limit' => $storageUsage->total_storage_limit,
                'formatted_limit' => $this->formatBytes($storageUsage->total_storage_limit),
                'max_file_size' => self::LICENSE_TYPES[$licenseType]['max_file_size'],
                'formatted_max_file_size' => $this->formatBytes(self::LICENSE_TYPES[$licenseType]['max_file_size'])
            ],
            'usage' => [
                'used' => $storageUsage->used_storage,
                'formatted_used' => $storageUsage->getFormattedUsedStorage(),
                'free' => $storageUsage->getFreeStorage(),
                'formatted_free' => $storageUsage->getFormattedFreeStorage(),
                'percentage' => $storageUsage->getUsagePercentage(),
                'file_count' => $storageUsage->file_count,
                'is_limit_exceeded' => $storageUsage->isStorageLimitExceeded()
            ]
        ];
    }

    /**
     * Проверка доступности загрузки для пользователя
     */
    public function checkUserUploadPermission(string $roleName): bool
    {
        return in_array($roleName, ['Руководитель', 'Менеджер', 'Сотрудник']);
    }

    /**
     * Проверка доступа к файлу
     */
    public function checkFileAccess(string $userRole, $file, $user): bool
    {
        switch ($userRole) {
            case 'Руководитель':
                return $file->company_id === $user->company_id;

            case 'Менеджер':
                return $file->company_id === $user->company_id &&
                    ($file->department_id === $user->department_id || $file->is_public);

            case 'Сотрудник':
                return $file->company_id === $user->company_id &&
                    ($file->uploaded_by === $user->id ||
                        ($file->department_id === $user->department_id && $file->is_public));

            default:
                return false;
        }
    }

    /**
     * Получение или создание записи об использовании хранилища
     */
    private function getOrCreateStorageUsage(Company $company): StorageUsage
    {
        return StorageUsage::firstOrCreate(
            ['company_id' => $company->id],
            [
                'total_storage_limit' => self::LICENSE_TYPES[$company->license_type ?? 'basic']['limit'],
                'used_storage' => 0,
                'file_count' => 0,
                'license_type' => $company->license_type ?? 'basic'
            ]
        );
    }

    /**
     * Форматирование байтов в читаемый вид
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
}
