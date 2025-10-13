<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'company_id',
        'phone',
        'department_id',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // === СВЯЗИ ===

    /**
     * Связь с ролью пользователя
     * @return BelongsTo - возвращает роль, к которой принадлежит пользователь
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Связь с компанией пользователя
     * @return BelongsTo - возвращает компанию, в которой работает пользователь
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Задачи, которые пользователь создал (автор задач)
     * @return HasMany - возвращает коллекцию задач, где пользователь является автором
     */
    public function authoredTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'author_id');
    }

    /**
     * Задачи, которые назначены пользователю (исполнитель)
     * @return HasMany - возвращает коллекцию задач, где пользователь является исполнителем
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'user_id');
    }

    /**
     * Отделы, в которых состоит пользователь (многие ко многим)
     * @return BelongsToMany - возвращает коллекцию отделов пользователя
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_user')
            ->withTimestamps();
    }

    /**
     * Отделы, которыми пользователь руководит (супервизор)
     * @return HasMany - возвращает коллекцию отделов, где пользователь является руководителем
     */
    public function supervisedDepartments(): HasMany
    {
        return $this->hasMany(Department::class, 'supervisor_id');
    }

    // === МЕТОДЫ ===

    /**
     * Проверяет, является ли пользователь руководителем отдела
     * @return bool - true если пользователь руководит хотя бы одним отделом
     */
    public function isSupervisor(): bool
    {
        return $this->supervisedDepartments()->exists();
    }

    /**
     * Проверяет, является ли пользователь владельцем/руководителем компании
     * @return bool - true если пользователь является владельцем хотя бы одной компании
     */
    public function isCompanyOwner(): bool
    {
        return $this->ownedCompanies()->exists();
    }

    /**
     * Проверяет, является ли пользователь руководителем (отдела или компании)
     * @return bool - true если пользователь руководит отделом или является владельцем компании
     */
    public function isManager(): bool
    {
        return $this->isSupervisor() || $this->isCompanyOwner();
    }

    // === НОВЫЕ СВЯЗИ ===

    /**
     * Компании, которыми пользователь владеет/руководит
     * @return HasMany - возвращает коллекцию компаний, где пользователь является владельцем
     */
    public function ownedCompanies(): HasMany
    {
        return $this->hasMany(Company::class, 'user_id');
    }

    /**
     * Получает количество задач пользователя по определенному статусу
     * @param string $status - статус задачи ('не назначена', 'в работе', 'просрочена', 'выполнена')
     * @return int - количество задач с указанным статусом
     */
    public function getTaskCountByStatus(string $status): int
    {
        return $this->assignedTasks()->where('status', $status)->count();
    }

    /**
     * Получает просроченные задачи пользователя
     * @return \Illuminate\Database\Eloquent\Collection - коллекция просроченных задач
     */
    public function getOverdueTasks(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->assignedTasks()
            ->where('status', '!=', 'выполнена')
            ->where('deadline', '<', now())
            ->get();
    }

    /**
     * Получает все активные задачи пользователя (не выполненные)
     * @return \Illuminate\Database\Eloquent\Collection - коллекция активных задач
     */
    public function getActiveTasks(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->assignedTasks()
            ->where('status', '!=', 'выполнена')
            ->get();
    }
}
