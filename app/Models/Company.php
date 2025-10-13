<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
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

    /**
     * Пользователи компании
     * @return HasMany - возвращает всех пользователей, работающих в компании
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Отделы компании
     * @return HasMany - возвращает все отделы компании
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    // === МЕТОДЫ ===

    /**
     * Получает количество активных пользователей в компании
     * @return int - количество активных пользователей
     */
    public function getActiveUsersCount(): int
    {
        return $this->users()->where('is_active', true)->count();
    }

    /**
     * Получает общее количество задач во всех отделах компании
     * @return int - общее количество задач компании
     */
    public function getTasksCount(): int
    {
        return Task::whereIn('department_id', $this->departments()->pluck('id'))->count();
    }

    /**
     * Проверяет, верифицирована ли компания
     * @return bool - true если компания верифицирована
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    public  function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
