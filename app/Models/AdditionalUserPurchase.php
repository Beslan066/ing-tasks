<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AdditionalUserPurchase extends Model
{
    use HasFactory;

    protected $table = 'additional_user_purchases';

    protected $fillable = [
        'company_id',
        'subscription_id',
        'user_count',
        'period',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function getDaysRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}
