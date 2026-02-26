<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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
        'mime_type',
        'metadata',
        'is_edited',
        'edited_at',
        'sent_at',
        'delivered_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_edited' => 'boolean',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'edited_at' => 'datetime'
    ];

    /**
     * Получить чат сообщения
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Получить автора сообщения
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить статусы сообщения
     */
    public function statuses()
    {
        return $this->hasMany(MessageStatus::class);
    }

    /**
     * Получить статус для конкретного пользователя
     */
    public function getStatusForUser($userId)
    {
        return $this->statuses()
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Отметить как доставленное для пользователя
     */
    public function markAsDelivered($userId)
    {
        $status = $this->statuses()->firstOrCreate(
            ['user_id' => $userId],
            ['status' => 'delivered']
        );

        if ($status->status === 'sent') {
            $status->update([
                'status' => 'delivered'
            ]);
        }

        // Если все получили, обновляем delivered_at
        if ($this->statuses()->where('status', 'sent')->count() === 0) {
            $this->update(['delivered_at' => now()]);
        }

        return $status;
    }

    /**
     * Отметить как прочитанное для пользователя
     */
    public function markAsRead($userId)
    {
        $status = $this->statuses()->updateOrCreate(
            ['user_id' => $userId],
            [
                'status' => 'read',
                'read_at' => now()
            ]
        );

        return $status;
    }

    /**
     * Проверить, прочитано ли сообщение всеми
     */
    public function isReadByAll()
    {
        $totalUsers = $this->chat->users()->count();
        $readCount = $this->statuses()
            ->where('status', 'read')
            ->count();

        return $readCount === $totalUsers - 1; // минус автор
    }

    /**
     * Получить URL файла
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }
        return null;
    }

    /**
     * Получить иконку для типа файла
     */
    public function getFileIconAttribute()
    {
        if (!$this->mime_type) {
            return 'fa-file';
        }

        $icons = [
            'image' => 'fa-file-image',
            'pdf' => 'fa-file-pdf',
            'word' => 'fa-file-word',
            'excel' => 'fa-file-excel',
            'archive' => 'fa-file-archive',
            'audio' => 'fa-file-audio',
            'video' => 'fa-file-video',
        ];

        foreach ($icons as $type => $icon) {
            if (str_contains($this->mime_type, $type)) {
                return $icon;
            }
        }

        return 'fa-file';
    }

    /**
     * Получить форматированный размер файла
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Редактировать сообщение
     */
    public function edit($newContent)
    {
        $this->update([
            'content' => $newContent,
            'is_edited' => true,
            'edited_at' => now()
        ]);
    }

    /**
     * Создать системное сообщение
     */
    public static function createSystemMessage($chatId, $content)
    {
        return self::create([
            'chat_id' => $chatId,
            'user_id' => null,
            'content' => $content,
            'type' => 'system'
        ]);
    }
}
