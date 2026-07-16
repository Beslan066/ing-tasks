<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chat_id',
        'user_id',
        'content',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'file_mime_type',
        'parent_id',
        'is_edited',
        'edited_at',
        'delivered_at',
        'read_at',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    const TYPE_TEXT = 'text';
    const TYPE_FILE = 'file';
    const TYPE_IMAGE = 'image';
    const TYPE_SYSTEM = 'system';

    // === СВЯЗИ ===

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'parent_id');
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(MessageStatus::class);
    }

    // === МЕТОДЫ ===

    public function markAsDelivered(): void
    {
        $this->update(['delivered_at' => now()]);
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function isReadByUser(User $user): bool
    {
        return $this->statuses()
            ->where('user_id', $user->id)
            ->where('status', 'read')
            ->exists();
    }

    public function getReadCount(): int
    {
        return $this->statuses()->where('status', 'read')->count();
    }

    public function getDeliveredCount(): int
    {
        return $this->statuses()->where('status', 'delivered')->count();
    }

    public function getFileIcon(): string
    {
        if (!$this->file_mime_type) {
            return 'fa-file';
        }

        $mimeToIcon = [
            'image/' => 'fa-file-image',
            'video/' => 'fa-file-video',
            'audio/' => 'fa-file-audio',
            'application/pdf' => 'fa-file-pdf',
            'application/msword' => 'fa-file-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word',
            'application/vnd.ms-excel' => 'fa-file-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fa-file-excel',
            'application/zip' => 'fa-file-archive',
            'text/' => 'fa-file-alt',
        ];

        foreach ($mimeToIcon as $pattern => $icon) {
            if (strpos($this->file_mime_type, $pattern) === 0) {
                return $icon;
            }
        }

        return 'fa-file';
    }

    public function getFormattedFileSize(): string
    {
        if (!$this->file_size) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $size = $this->file_size;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Получить статус сообщения для текущего пользователя
     */
    public function getStatusForUser(User $user): ?string
    {
        $status = $this->statuses()->where('user_id', $user->id)->first();
        return $status ? $status->status : null;
    }

    /**
     * Проверить, прочитано ли сообщение пользователем
     */
    public function isReadBy(User $user): bool
    {
        return $this->getStatusForUser($user) === 'read';
    }

    /**
     * Проверить, доставлено ли сообщение пользователю
     */
    public function isDeliveredTo(User $user): bool
    {
        $status = $this->getStatusForUser($user);
        return $status === 'delivered' || $status === 'read';
    }
}
