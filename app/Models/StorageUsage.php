<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorageUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'total_storage_limit', // в байтах
        'used_storage', // в байтах
        'file_count',
        'license_type', // 'basic', 'optimal', 'premium'
        'updated_at'
    ];

    protected $casts = [
        'total_storage_limit' => 'integer',
        'used_storage' => 'integer',
        'file_count' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getUsagePercentage(): float
    {
        if ($this->total_storage_limit <= 0) {
            return 0;
        }

        return round(($this->used_storage / $this->total_storage_limit) * 100, 2);
    }

    public function getFormattedUsedStorage(): string
    {
        return $this->formatBytes($this->used_storage);
    }

    public function getFormattedTotalStorage(): string
    {
        return $this->formatBytes($this->total_storage_limit);
    }

    public function getFreeStorage(): int
    {
        return max(0, $this->total_storage_limit - $this->used_storage);
    }

    public function getFormattedFreeStorage(): string
    {
        return $this->formatBytes($this->getFreeStorage());
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function isStorageLimitExceeded(): bool
    {
        return $this->used_storage >= $this->total_storage_limit;
    }

    public function canUploadFile($fileSize): bool
    {
        return ($this->used_storage + $fileSize) <= $this->total_storage_limit;
    }
}
