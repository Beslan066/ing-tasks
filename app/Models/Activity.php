<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

class Activity extends Model
{
    protected $table = 'activity_log';

    protected $fillable = [
        'user_id',
        'company_id',
        'subject_type',
        'subject_id',
        'action',
        'description',
        'properties',
        'old_values',
        'new_values'
    ];

    protected $casts = [
        'properties' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime'
    ];

    // === СВЯЗИ ===

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    // === СКОПЫ ===

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfType($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    // === МЕТОДЫ ===

    /**
     * Получить иконку для события
     */
    public function getIcon(): string
    {
        return match($this->action) {
            'task_created' => '📝',
            'task_assigned' => '🎯',
            'task_completed' => '✅',
            'task_updated' => '✏️',
            'task_deleted' => '🗑️',
            'task_rejected' => '❌',
            'user_invited' => '📧',
            'user_joined' => '👋',
            'user_left' => '🚪',
            'user_removed' => '🔴',
            'invitation_cancelled' => '🚫',
            'file_uploaded' => '📎',
            'file_deleted' => '🗑️',
            'comment_added' => '💬',
            default => '📌'
        };
    }

    public function getColorClass(): string
    {
        return match($this->action) {
            'task_created' => 'bg-blue-100 text-blue-800',
            'task_assigned' => 'bg-purple-100 text-purple-800',
            'task_completed' => 'bg-green-100 text-green-800',
            'task_updated' => 'bg-yellow-100 text-yellow-800',
            'task_deleted' => 'bg-red-100 text-red-800',
            'task_rejected' => 'bg-orange-100 text-orange-800',
            'user_invited' => 'bg-indigo-100 text-indigo-800',
            'user_joined' => 'bg-emerald-100 text-emerald-800',
            'user_left' => 'bg-gray-100 text-gray-800',
            'user_removed' => 'bg-red-100 text-red-800',
            'invitation_cancelled' => 'bg-gray-100 text-gray-800',
            'file_uploaded' => 'bg-cyan-100 text-cyan-800',
            'file_deleted' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Форматированная дата
     */
    public function getFormattedDate(): string
    {
        $now = Carbon::now();
        $created = $this->created_at;

        if ($created->diffInMinutes($now) < 1) {
            return 'Только что';
        }

        if ($created->diffInHours($now) < 24) {
            return $created->diffForHumans();
        }

        if ($created->diffInDays($now) < 7) {
            return $created->format('l, H:i');
        }

        return $created->format('d.m.Y H:i');
    }
}
