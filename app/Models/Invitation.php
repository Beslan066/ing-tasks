<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'token',
        'company_id',
        'invited_by',
        'role_id',
        'department_id',
        'permissions',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'permissions' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            $invitation->token = Str::random(32);
            $invitation->expires_at = now()->addDays(7);
        });
    }

    // === СВЯЗИ ===

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // === МЕТОДЫ ===

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return !is_null($this->accepted_at);
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isAccepted();
    }

    public function markAsAccepted()
    {
        $this->update(['accepted_at' => now()]);
    }

    public function getInvitationUrl(): string
    {
        return route('invitation.accept', $this->token);
    }
}
