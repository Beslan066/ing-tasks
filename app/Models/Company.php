<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'verified',
        'phone',
        'user_id',
    ];

    protected $casts = [
        'verified' => 'boolean',
    ];

    // === СВЯЗИ ===

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // === МЕТОДЫ ===

    public function getActiveUsersCount(): int
    {
        return $this->users()->where('is_active', true)->count();
    }

    public function getTasksCount(): int
    {
        return Task::whereIn('department_id', $this->departments()->pluck('id'))->count();
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * Создает приглашение для нового пользователя
     */
    public function inviteUser(string $email, User $inviter, ?Role $role = null, ?Department $department = null, ?array $permissions = null): Invitation
    {
        // Отменяем предыдущие приглашения для этого email
        $this->invitations()
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->update(['expires_at' => now()]);

        return $this->invitations()->create([
            'email' => $email,
            'invited_by' => $inviter->id,
            'role_id' => $role?->id,
            'department_id' => $department?->id,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Получает активные приглашения
     */
    public function getActiveInvitations()
    {
        return $this->invitations()
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->with(['role', 'department', 'inviter'])
            ->get();
    }
}

