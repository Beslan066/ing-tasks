<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'creator_id',
        'department_id',
        'title',
        'description',
        'location',
        'color',
        'type',
        'priority',
        'status',
        'start_date',
        'end_date',
        'all_day',
        'is_recurring',
        'recurrence_type',
        'recurrence_end_date',
        'parent_event_id',
        'is_public',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'all_day' => 'boolean',
        'is_recurring' => 'boolean',
        'is_public' => 'boolean',
        'metadata' => 'array',
        'recurrence_end_date' => 'datetime',
    ];

    // === СВЯЗИ ===

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_participants')
            ->withPivot('status', 'responded_at', 'comment')
            ->withTimestamps();
    }

    public function parentEvent()
    {
        return $this->belongsTo(Event::class, 'parent_event_id');
    }

    public function childEvents()
    {
        return $this->hasMany(Event::class, 'parent_event_id');
    }

    // === СКОПЫ ===

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('creator_id', $userId)
            ->orWhereHas('participants', function($q) use ($userId) {
                $q->where('user_id', $userId);
            });
    }

    public function scopeInDateRange($query, $start, $end)
    {
        return $query->where(function($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start, $end])
                ->orWhereBetween('end_date', [$start, $end])
                ->orWhere(function($sub) use ($start, $end) {
                    $sub->where('start_date', '<=', $start)
                        ->where('end_date', '>=', $end);
                });
        });
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addDays($days))
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // === МЕТОДЫ ===

    public function isOwner(User $user): bool
    {
        return $this->creator_id === $user->id;
    }

    public function isParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    public function canEdit(User $user): bool
    {
        return $this->isOwner($user) ||
            $user->isManager() ||
            $user->isSupervisor();
    }

    public function canDelete(User $user): bool
    {
        return $this->isOwner($user) ||
            $user->isManager() ||
            $user->isSupervisor();
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'planned' => 'Запланировано',
            'ongoing' => 'В процессе',
            'completed' => 'Завершено',
            'cancelled' => 'Отменено',
            default => 'Неизвестно'
        };
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'meeting' => 'Встреча',
            'deadline' => 'Дедлайн',
            'reminder' => 'Напоминание',
            'other' => 'Другое',
            default => 'Другое'
        };
    }

    public function getPriorityLabel(): string
    {
        return match($this->priority) {
            'low' => 'Низкий',
            'medium' => 'Средний',
            'high' => 'Высокий',
            default => 'Средний'
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'planned' => 'blue',
            'ongoing' => 'orange',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'low' => 'blue',
            'medium' => 'orange',
            'high' => 'red',
            default => 'gray'
        };
    }

    public function getDuration(): string
    {
        if ($this->all_day) {
            return 'Весь день';
        }

        $diff = $this->start_date->diff($this->end_date);
        $hours = $diff->h;
        $minutes = $diff->i;

        if ($hours === 0 && $minutes === 0) {
            return 'Мгновенно';
        }

        $parts = [];
        if ($hours > 0) {
            $parts[] = $hours . 'ч';
        }
        if ($minutes > 0) {
            $parts[] = $minutes . 'м';
        }

        return implode(' ', $parts);
    }

    public function getParticipantsWithStatus(): array
    {
        return $this->participants->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->pivot->status,
                'status_label' => $this->getParticipantStatusLabel($user->pivot->status),
                'responded_at' => $user->pivot->responded_at,
                'comment' => $user->pivot->comment,
            ];
        })->toArray();
    }

    private function getParticipantStatusLabel(string $status): string
    {
        return match($status) {
            'invited' => 'Приглашен',
            'confirmed' => 'Подтвержден',
            'declined' => 'Отказался',
            'maybe' => 'Возможно',
            default => 'Неизвестно'
        };
    }

    public function getParticipantStatus(User $user): ?string
    {
        $participant = $this->participants()->where('user_id', $user->id)->first();
        return $participant ? $participant->pivot->status : null;
    }

    public function isUserConfirmed(User $user): bool
    {
        return $this->getParticipantStatus($user) === 'confirmed';
    }

    public function getEventColor(): string
    {
        if ($this->color) {
            return $this->color;
        }

        $colors = [
            'meeting' => '#3B82F6',
            'deadline' => '#EF4444',
            'reminder' => '#F59E0B',
            'other' => '#6B7280',
        ];

        return $colors[$this->type] ?? '#6B7280';
    }

    // Для FullCalendar
    public function toCalendarEvent(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->all_day ? $this->start_date->format('Y-m-d') : $this->start_date->toISOString(),
            'end' => $this->all_day ? $this->end_date->format('Y-m-d') : $this->end_date->toISOString(),
            'allDay' => (bool)$this->all_day,
            'color' => $this->getEventColor(),
            'extendedProps' => [
                'description' => $this->description,
                'location' => $this->location,
                'creator_id' => $this->creator_id,
                'creator_name' => $this->creator?->name,
                'department_id' => $this->department_id,
                'department_name' => $this->department?->name,
                'participants' => $this->getParticipantsWithStatus(),
                'participants_count' => $this->participants->count(),
                'status' => $this->status,
                'status_label' => $this->getStatusLabel(),
                'type' => $this->type,
                'type_label' => $this->getTypeLabel(),
                'priority' => $this->priority,
                'priority_label' => $this->getPriorityLabel(),
            ],
        ];
    }
}
