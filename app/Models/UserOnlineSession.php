<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOnlineSession extends Model
{
    use HasFactory;

    protected $table = 'user_online_sessions';

    protected $fillable = [
        'user_id',
        'login_at',
        'logout_at',
        'session_id',
        'date',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Проверка, онлайн ли пользователь
    public function isOnline(): bool
    {
        return $this->logout_at === null &&
            $this->last_activity_at &&
            $this->last_activity_at->diffInMinutes(now()) < 5;
    }

    // Scope для активных сессий
    public function scopeActive($query)
    {
        return $query->whereNull('logout_at')
            ->where('last_activity_at', '>=', now()->subMinutes(5));
    }

    public function endSession(): void
    {
        $this->update([
            'logout_at' => now(),
            'last_activity_at' => now(),
        ]);
    }
}
