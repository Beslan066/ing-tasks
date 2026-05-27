<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'attachment_path',
        'attachment_original_name',
        'attachment_size',
        'status',
        'user_ip',
        'user_agent'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Статусы тикетов
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_ANSWERED = 'answered';
    const STATUS_CLOSED = 'closed';

    public static function getStatuses()
    {
        return [
            self::STATUS_NEW => '🟡 Новое',
            self::STATUS_IN_PROGRESS => '🔵 В работе',
            self::STATUS_ANSWERED => '🟢 Отвечено',
            self::STATUS_CLOSED => '⚫ Закрыто',
        ];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_NEW => 'bg-yellow-100 text-yellow-800',
            self::STATUS_IN_PROGRESS => 'bg-blue-100 text-blue-800',
            self::STATUS_ANSWERED => 'bg-green-100 text-green-800',
            self::STATUS_CLOSED => 'bg-gray-100 text-gray-800',
        ];

        $labels = self::getStatuses();

        return '<span class="px-2 py-1 text-xs font-semibold rounded-full ' . $badges[$this->status] . '">' . $labels[$this->status] . '</span>';
    }


    // Проверка, есть ли вложения
    public function hasAttachment()
    {
        return $this->attachment_path && file_exists(storage_path('app/' . $this->attachment_path));
    }

    // Получение пути к файлу
    public function getAttachmentUrl()
    {
        if ($this->hasAttachment()) {
            return route('admin.support.download', $this->id);
        }
        return null;
    }

    // Связь с ответами - указываем правильное имя внешнего ключа
    public function replies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }
}
