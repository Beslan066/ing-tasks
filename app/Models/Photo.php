<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'photos';

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'metadata',
        'category_id',
        'user_id',
        'is_approved',
        'width',
        'height',
        'optimized_path',
        'variants'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_approved' => 'boolean',
        'variants' => 'array',
        'deleted_at' => 'datetime'
    ];

    protected $appends = ['file_size_formatted', 'url'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PhotoCategory::class, 'category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'photo_tag', 'photo_id', 'tag_id');
    }

    protected function fileSizeFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatFileSize($this->file_size),
        );
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->file_path ? Storage::url($this->file_path) : null,
        );
    }

    private function formatFileSize($bytes)
    {
        if (!$bytes) return '0 bytes';

        $units = ['bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Получить превью изображения
     */
    public function getPreviewUrl(): string
    {
        if ($this->variants && isset($this->variants['thumb'])) {
            return Storage::url($this->variants['thumb']);
        }

        return $this->url;
    }

    /**
     * Получить среднюю версию
     */
    public function getMediumUrl(): string
    {
        if ($this->variants && isset($this->variants['medium'])) {
            return Storage::url($this->variants['medium']);
        }

        return $this->url;
    }
}
