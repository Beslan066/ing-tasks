<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'company_id',
        'type',
        'status',
        'starts_at',
        'expires_at',
        'base_user_slots',
        'storage_limit',
        'features',
        'payment_method',
        'payment_provider_id',
        'auto_renew'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'features' => 'array',
        'auto_renew' => 'boolean'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function additionalUserPurchases(): HasMany
    {
        return $this->hasMany(AdditionalUserPurchase::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast() || $this->status === 'expired';
    }

    public function getDaysRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    public function renew(int $months, string $paymentId = null): void
    {
        $newExpiryDate = $this->expires_at->isPast()
            ? now()->addMonths($months)
            : $this->expires_at->addMonths($months);

        $this->update([
            'expires_at' => $newExpiryDate,
            'status' => 'active',
            'payment_provider_id' => $paymentId ?? $this->payment_provider_id
        ]);
    }

    public function getTotalUserSlots(): int
    {
        $additionalSlots = $this->additionalUserPurchases()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->sum('user_count');

        \Log::info('getTotalUserSlots', [
            'subscription_id' => $this->id,
            'base_slots' => $this->base_user_slots,
            'additional_slots' => $additionalSlots,
            'total' => $this->base_user_slots + $additionalSlots
        ]);

        return $this->base_user_slots + $additionalSlots;
    }
}
