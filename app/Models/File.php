<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    protected $fillable = [
        'name',
        'file',
        'file_path',
        'file_size',
        'mime_type',
        'department_id',
        'task_id',
        'user_id',
    ];

    // === СВЯЗИ ===

    /**
     * Отдел, к которому прикреплен файл
     * @return BelongsTo - возвращает отдел владелец файла
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Задача, к которой прикреплен файл
     * @return BelongsTo - возвращает задачу, к которой прикреплен файл
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Пользователь, загрузивший файл
     * @return BelongsTo - возвращает пользователя, который загрузил файл
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // === МЕТОДЫ ===

    /**
     * Получает размер файла в читаемом формате
     * @return string - размер файла в формате (например, "2.5 MB")
     */
    public function getFormattedSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Проверяет, является ли файл изображением
     * @return bool - true если файл является изображением
     */
    public function isImage(): bool
    {
        return strpos($this->mime_type, 'image/') === 0;
    }

    /**
     * Проверяет, является ли файл документом
     * @return bool - true если файл является документом
     */
    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
    }
}
