<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type', // private, group, department, company
        'company_id',
        'department_id', // для чатов отдела
        'created_by',
        'last_message_id',
        'last_message_at',
        'is_active',
        'avatar',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    const TYPE_PRIVATE = 'private';
    const TYPE_GROUP = 'group';
    const TYPE_DEPARTMENT = 'department';
    const TYPE_COMPANY = 'company';

    // === СВЯЗИ ===

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_user')
            ->withPivot(['role', 'last_read_at', 'is_muted', 'joined_at', 'left_at'])
            ->withTimestamps()
            ->wherePivotNull('left_at');
    }

    public function allUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_user')
            ->withPivot(['role', 'last_read_at', 'is_muted', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'desc');
    }

    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    // === МЕТОДЫ ===

    public function addUser(User $user, string $role = 'member'): void
    {
        if (!$this->users()->where('user_id', $user->id)->exists()) {
            $this->users()->attach($user->id, [
                'role' => $role,
                'joined_at' => now(),
            ]);
        }
    }

    public function removeUser(User $user): void
    {
        $this->users()->updateExistingPivot($user->id, [
            'left_at' => now(),
        ]);
    }

    public function isUserInChat(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    public function getUnreadCount(User $user): int
    {
        $pivot = $this->users()->where('user_id', $user->id)->first();
        if (!$pivot || !$pivot->pivot->last_read_at) {
            return $this->messages()->where('user_id', '!=', $user->id)->count();
        }

        return $this->messages()
            ->where('user_id', '!=', $user->id)
            ->where('created_at', '>', $pivot->pivot->last_read_at)
            ->count();
    }

    public function markAsRead(User $user): void
    {
        $this->users()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);
    }

    public function getDisplayName(): string
    {
        if ($this->type === self::TYPE_PRIVATE) {
            $otherUser = $this->users()->where('user_id', '!=', auth()->id())->first();
            return $otherUser ? $otherUser->name : 'Чат';
        }

        return $this->name ?? 'Групповой чат';
    }

    public function getAvatar(): ?string
    {
        if ($this->type === self::TYPE_PRIVATE) {
            $otherUser = $this->users()->where('user_id', '!=', auth()->id())->first();
            return $otherUser ? $otherUser->getAvatarUrlAttribute() : null;
        }

        return $this->avatar;
    }

    // Scope для фильтрации доступных чатов
    public function scopeForUser($query, User $user)
    {
        return $query->whereHas('users', function ($q) use ($user) {
            $q->where('user_id', $user->id)->whereNull('left_at');
        })->where('is_active', true);
    }

    // Scope для чатов компании
    public function scopeForCompany($query, Company $company)
    {
        return $query->where('company_id', $company->id);
    }

    // Scope для чатов отдела
    public function scopeForDepartment($query, Department $department)
    {
        return $query->where('department_id', $department->id);
    }
}
