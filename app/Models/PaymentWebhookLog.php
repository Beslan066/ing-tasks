<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentWebhookLog extends Model
{
    use HasFactory;

    protected $table = 'payment_webhook_logs';

    protected $fillable = [
        'provider',
        'event_type',
        'payment_id',
        'payload',
        'ip_address',
        'processed',
        'error_message'
    ];

    protected $casts = [
        'payload' => 'array',
        'processed' => 'boolean'
    ];

    public function markAsProcessed(): void
    {
        $this->update(['processed' => true]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'processed' => false,
            'error_message' => $error
        ]);
    }
}
