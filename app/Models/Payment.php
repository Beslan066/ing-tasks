<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'company_id',
        'subscription_id',
        'payment_type',
        'status',
        'amount',
        'currency',
        'period',
        'user_count',
        'payment_provider',
        'provider_payment_id',
        'provider_payment_url',
        'provider_data',
        'metadata',
        'paid_at'
    ];

    protected $casts = [
        'provider_data' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now()
        ]);
    }

    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'metadata' => array_merge($this->metadata ?? [], ['failure_reason' => $reason])
        ]);
    }

    public function getFormattedAmount(): string
    {
        return number_format($this->amount, 0, ',', ' ') . ' ₽';
    }
}
