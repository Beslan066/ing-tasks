<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'verified',
        'phone',
        'user_id',
        'license_type', // Добавляем это поле
    ];

    protected $casts = [
        'verified' => 'boolean',
    ];

    // === СВЯЗИ ===

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tags()
    {
        return $this->belongsTo(Tag::class, 'tag_id', 'id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function storageUsage(): HasOne
    {
        return $this->hasOne(StorageUsage::class);
    }

    // === МЕТОДЫ ===

    public function getActiveUsersCount(): int
    {
        return $this->users()->where('is_active', true)->count();
    }

    public function getTasksCount(): int
    {
        return Task::whereIn('department_id', $this->departments()->pluck('id'))->count();
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * Получает название тарифного плана
     */
    public function getLicenseTypeName(): string
    {
        return match($this->license_type) {
            'basic' => 'Базовый',
            'premium' => 'Премиум',
            default => 'Базовый'
        };
    }

    /**
     * Получает лимит хранилища в байтах
     */
    public function getStorageLimit(): int
    {
        return match($this->license_type) {
            'basic' => 1073741824,      // 1GB
            'premium' => 1073741824000,  // 1000GB (1TB)
            default => 1073741824
        };
    }

    /**
     * Получает максимальный размер файла для загрузки
     */
    public function getMaxFileSize(): int
    {
        return match($this->license_type) {
            'basic' => 104857600,      // 100MB
            'premium' => 1073741824,    // 1GB
            default => 104857600
        };
    }

    /**
     * Проверяет, превышен ли лимит хранилища
     */
    public function isStorageLimitExceeded(): bool
    {
        if (!$this->storageUsage) {
            return false;
        }

        return $this->storageUsage->isStorageLimitExceeded();
    }

    /**
     * Получает оставшееся свободное место
     */
    public function getFreeStorage(): int
    {
        if (!$this->storageUsage) {
            return $this->getStorageLimit();
        }

        return $this->storageUsage->getFreeStorage();
    }

    /**
     * Получает использованное место в читаемом формате
     */
    public function getFormattedUsedStorage(): string
    {
        if (!$this->storageUsage) {
            return '0 B';
        }

        return $this->storageUsage->getFormattedUsedStorage();
    }

    /**
     * Получает общий лимит в читаемом формате
     */
    public function getFormattedStorageLimit(): string
    {
        $limit = $this->getStorageLimit();
        return $this->formatBytes($limit);
    }

    /**
     * Получает статистику использования хранилища
     */
    public function getStorageStats(): array
    {
        $storageUsage = $this->storageUsage;

        if (!$storageUsage) {
            return [
                'used' => 0,
                'formatted_used' => '0 B',
                'limit' => $this->getStorageLimit(),
                'formatted_limit' => $this->getFormattedStorageLimit(),
                'free' => $this->getStorageLimit(),
                'formatted_free' => $this->getFormattedStorageLimit(),
                'percentage' => 0,
                'file_count' => 0,
                'is_limit_exceeded' => false
            ];
        }

        return [
            'used' => $storageUsage->used_storage,
            'formatted_used' => $storageUsage->getFormattedUsedStorage(),
            'limit' => $storageUsage->total_storage_limit,
            'formatted_limit' => $storageUsage->getFormattedTotalStorage(),
            'free' => $storageUsage->getFreeStorage(),
            'formatted_free' => $storageUsage->getFormattedFreeStorage(),
            'percentage' => $storageUsage->getUsagePercentage(),
            'file_count' => $storageUsage->file_count,
            'is_limit_exceeded' => $storageUsage->isStorageLimitExceeded()
        ];
    }

    /**
     * Форматирует байты в читаемый вид
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
     * Создает приглашение для нового пользователя
     */
    public function inviteUser(string $email, User $inviter, ?Role $role = null, ?Department $department = null, ?array $permissions = null): Invitation
    {
        // Отменяем предыдущие приглашения для этого email
        $this->invitations()
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->update(['expires_at' => now()]);

        return $this->invitations()->create([
            'email' => $email,
            'invited_by' => $inviter->id,
            'role_id' => $role?->id,
            'department_id' => $department?->id,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Получает активные приглашения
     */
    public function getActiveInvitations()
    {
        return $this->invitations()
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->with(['role', 'department', 'inviter'])
            ->get();
    }

    /**
     * Изменяет тарифный план компании
     */
    public function changeLicenseType(string $newLicenseType, bool $force = false): array
    {
        $allowedTypes = ['basic', 'premium'];

        if (!in_array($newLicenseType, $allowedTypes)) {
            return [
                'success' => false,
                'message' => 'Некорректный тип лицензии'
            ];
        }

        $newLimit = match($newLicenseType) {
            'basic' => 1073741824,
            'premium' => 1073741824000,
            default => 1073741824
        };

        // Проверяем, не превышает ли текущее использование новый лимит
        if (!$force && $this->storageUsage && $this->storageUsage->used_storage > $newLimit) {
            return [
                'success' => false,
                'message' => 'Текущее использование хранилища превышает новый лимит. ' .
                    'Удалите некоторые файлы перед изменением тарифа.'
            ];
        }

        // Обновляем тариф
        $this->license_type = $newLicenseType;
        $this->save();

        // Обновляем запись об использовании хранилища
        if ($this->storageUsage) {
            $this->storageUsage->license_type = $newLicenseType;
            $this->storageUsage->total_storage_limit = $newLimit;
            $this->storageUsage->save();
        }

        return [
            'success' => true,
            'message' => 'Тариф успешно изменен на ' . $this->getLicenseTypeName()
        ];
    }

    /**
     * Связь с подпиской
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latest();
    }

    /**
     * Связь со всеми подписками
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Связь с покупками дополнительных пользователей
     */
    public function additionalUserPurchases()
    {
        return $this->hasMany(AdditionalUserPurchase::class);
    }

    /**
     * Получить максимальное количество пользователей с учетом доп. покупок
     */
    public function getMaxUsersAttribute(): int
    {
        if ($this->license_type !== 'premium') {
            return 5;
        }

        $subscription = $this->subscription;
        if (!$subscription) {
            return 15;
        }

        $baseSlots = $subscription->base_user_slots;
        $additionalSlots = $this->additionalUserPurchases()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->sum('user_count');

        return $baseSlots + $additionalSlots;
    }

    /**
     * Проверить, можно ли добавить пользователя
     */
    public function canAddUser(): bool
    {
        return $this->getActiveUsersCount() < $this->max_users;
    }

    /**
     * Получить информацию о текущей подписке
     */
    public function getCurrentSubscriptionInfo(): ?array
    {
        $subscription = $this->subscription;

        if (!$subscription) {
            return null;
        }

        return [
            'type' => $subscription->type,
            'expires_at' => $subscription->expires_at,
            'days_remaining' => $subscription->expires_at->diffInDays(now()),
            'is_expired' => $subscription->expires_at->isPast(),
            'base_user_slots' => $subscription->base_user_slots,
            'additional_users' => $this->additionalUserPurchases()
                ->where('is_active', true)
                ->where('expires_at', '>', now())
                ->sum('user_count'),
            'total_user_slots' => $this->max_users,
            'storage_limit_formatted' => $this->getFormattedStorageLimit(),
            'used_storage_formatted' => $this->getFormattedUsedStorage(),
            'storage_percentage' => $this->storageUsage?->getUsagePercentage() ?? 0
        ];
    }

    /**
     * Понизить тариф до базового
     */
    public function downgradeToBasic(): bool
    {
        $this->license_type = 'basic';
        $this->save();

        // Обновляем лимит хранилища
        if ($this->storageUsage) {
            $this->storageUsage->update([
                'total_storage_limit' => 1073741824, // 1GB
                'license_type' => 'basic'
            ]);
        }

        return true;
    }
}
