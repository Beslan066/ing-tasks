<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'first_visit_at',
        'last_visit_at',
        'page_views',
        'total_time_seconds',
    ];

    protected $casts = [
        'date' => 'date',
        'first_visit_at' => 'datetime',
        'last_visit_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pageTransitions()
    {
        return $this->hasMany(PageTransition::class);
    }

    public static function createPageTransition($userId, $fromUrl, $toUrl)
    {
        return PageTransition::create([
            'user_id' => $userId,
            'from_url' => $fromUrl,
            'to_url' => $toUrl,
            'transition_at' => now(),
        ]);
    }

    public function updateTotalTime($seconds)
    {
        $this->increment('total_time_seconds', $seconds);
    }

    public function getFormattedTotalTimeAttribute(): string
    {
        $hours = floor($this->total_time_seconds / 3600);
        $minutes = floor(($this->total_time_seconds % 3600) / 60);

        if ($hours > 0) {
            return sprintf('%d ч. %d мин.', $hours, $minutes);
        } elseif ($minutes > 0) {
            return sprintf('%d мин.', $minutes);
        } else {
            return 'Менее минуты';
        }
    }

    public function getAverageTimePerPageAttribute(): float
    {
        if ($this->page_views === 0) {
            return 0;
        }
        return round($this->total_time_seconds / $this->page_views, 1);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('d.m.Y');
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
