<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventParticipant extends Model
{
    protected $table = 'event_participants';

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'responded_at',
        'comment',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    // === СВЯЗИ ===

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // === МЕТОДЫ ===

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'invited' => 'Приглашен',
            'confirmed' => 'Подтвержден',
            'declined' => 'Отказался',
            'maybe' => 'Возможно',
            default => 'Неизвестно'
        };
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }

    public function isMaybe(): bool
    {
        return $this->status === 'maybe';
    }

    public function isInvited(): bool
    {
        return $this->status === 'invited';
    }

    public function hasResponded(): bool
    {
        return !is_null($this->responded_at);
    }
}
