<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Department extends Model
{
    protected $fillable = [
        'name',
        'company_id',
        'status',
        'supervisor_id',
    ];

    // === СВЯЗИ ===

    /**
     * Компания, к которой принадлежит отдел
     * @return BelongsTo - возвращает компанию отдела
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Руководитель отдела
     * @return BelongsTo - возвращает пользователя-руководителя отдела
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Задачи отдела
     * @return HasMany - возвращает все задачи отдела
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Роли в отделе
     * @return HasMany - возвращает все роли отдела
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Файлы отдела
     * @return HasMany - возвращает все файлы отдела
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * Пользователи отдела (многие ко многим)
     * @return BelongsToMany - возвращает всех пользователей отдела
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_user')
            ->withTimestamps();
    }

    // === МЕТОДЫ ===

    /**
     * Получает количество активных задач в отделе
     * @return int - количество невыполненных задач
     */
    public function getActiveTasksCount(): int
    {
        return $this->tasks()->where('status', '!=', 'выполнена')->count();
    }

    /**
     * Получает количество пользователей в отделе
     * @return int - количество пользователей отдела
     */
    public function getUsersCount(): int
    {
        return $this->users()->count();
    }

    /**
     * Получает просроченные задачи отдела
     * @return \Illuminate\Database\Eloquent\Collection - коллекция просроченных задач
     */
    public function getOverdueTasks(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->tasks()
            ->where('status', '!=', 'выполнена')
            ->where('deadline', '<', now())
            ->get();
    }

    /**
     * Назначает руководителя отдела
     * @param User $user - пользователь для назначения руководителем
     * @return bool - true если операция успешна
     */
    public function assignSupervisor(User $user): bool
    {
        // Проверяем, что пользователь принадлежит компании отдела
        if ($user->company_id !== $this->company_id) {
            return false;
        }

        return $this->update(['supervisor_id' => $user->id]);
    }

    /**
     * Добавляет пользователя в отдел
     * @param User $user - пользователь для добавления
     * @return bool - true если операция успешна
     */
    public function addUser(User $user): bool
    {
        // Проверяем, что пользователь принадлежит компании отдела
        if ($user->company_id !== $this->company_id) {
            return false;
        }

        if (!$this->users()->where('user_id', $user->id)->exists()) {
            $this->users()->attach($user->id);
            return true;
        }

        return false;
    }

    /**
     * Удаляет пользователя из отдела
     * @param User $user - пользователь для удаления
     * @return bool - true если операция успешна
     */
    public function removeUser(User $user): bool
    {
        return $this->users()->detach($user->id) > 0;
    }

    /**
     * Получает статистику задач по статусам
     * @return array - массив с количеством задач по каждому статусу
     */
    public function getTasksStatistics(): array
    {
        return [
            'not_assigned' => $this->tasks()->where('status', 'не назначена')->count(),
            'in_progress' => $this->tasks()->where('status', 'в работе')->count(),
            'overdue' => $this->tasks()->where('status', 'просрочена')->count(),
            'completed' => $this->tasks()->where('status', 'выполнена')->count(),
            'total' => $this->tasks()->count(),
        ];
    }

    /**
     * Проверяет, активен ли отдел
     * @return bool - true если отдел активен
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
