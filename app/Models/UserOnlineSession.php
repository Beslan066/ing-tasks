<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserOnlineSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'login_at',
        'logout_at',
        'duration_seconds',
        'date',
        'session_id',
        'ip_address',
        'user_agent',
        'last_activity_at',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function endSession(): void
    {
        if (!$this->logout_at) {
            $logoutTime = now();
            $duration = $this->login_at->diffInSeconds($logoutTime);

            $this->update([
                'logout_at' => $logoutTime,
                'duration_seconds' => $duration,
            ]);
        }
    }

    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        $seconds = $this->duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%d ч. %d мин.', $hours, $minutes);
        } elseif ($minutes > 0) {
            return sprintf('%d мин. %d сек.', $minutes, $seconds);
        } else {
            return sprintf('%d сек.', $seconds);
        }
    }

    public function getSessionInfoAttribute(): array
    {
        return [
            'start' => $this->login_at?->format('d.m.Y H:i') ?? '—',
            'end' => $this->logout_at ? $this->logout_at->format('d.m.Y H:i') : 'Активна',
            'duration' => $this->formatted_duration,
            'ip' => $this->ip_address ?? '—',
        ];
    }
}
