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
     * Пользователи отдела (ВСЕ пользователи - и через department_id, и через многие-ко-многим)
     * @return \Illuminate\Database\Eloquent\Collection - возвращает всех пользователей отдела
     */
    public function getAllUsersAttribute()
    {
        // Получаем пользователей через поле department_id
        $directUsers = User::where('department_id', $this->id)
            ->where('company_id', $this->company_id)
            ->get();

        // Получаем пользователей через связь многие-ко-многим
        $manyToManyUsers = $this->users()->get();

        // Объединяем и убираем дубликаты
        return $directUsers->merge($manyToManyUsers)->unique('id');
    }

    /**
     * Пользователи отдела через многие-ко-многим
     * @return BelongsToMany - возвращает пользователей через промежуточную таблицу
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_user')
            ->withTimestamps();
    }

    /**
     * Пользователи отдела через прямое отношение (department_id)
     * @return HasMany - возвращает пользователей с указанным department_id
     */
    public function directUsers(): HasMany
    {
        return $this->hasMany(User::class, 'department_id')
            ->where('company_id', $this->company_id);
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
     * Получает количество ВСЕХ пользователей в отделе
     * @return int - количество пользователей отдела (всех типов)
     */
    public function getUsersCount(): int
    {
        return $this->getAllUsersAttribute()->count();
    }

    /**
     * Получает количество пользователей через прямое отношение
     * @return int - количество пользователей с department_id
     */
    public function getDirectUsersCount(): int
    {
        return $this->directUsers()->count();
    }

    /**
     * Получает количество пользователей через многие-ко-многим
     * @return int - количество пользователей через department_user
     */
    public function getManyToManyUsersCount(): int
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
     * Добавляет пользователя в отдел (оба способа)
     * @param User $user - пользователь для добавления
     * @param bool $useDirectRelation - использовать прямое отношение (department_id) вместо многие-ко-многим
     * @return bool - true если операция успешна
     */
    public function addUser(User $user, bool $useDirectRelation = false): bool
    {
        // Проверяем, что пользователь принадлежит компании отдела
        if ($user->company_id !== $this->company_id) {
            return false;
        }

        if ($useDirectRelation) {
            // Используем прямое отношение через department_id
            if ($user->department_id !== $this->id) {
                $user->department_id = $this->id;
                return $user->save();
            }
        } else {
            // Используем отношение многие-ко-многим
            if (!$this->users()->where('user_id', $user->id)->exists()) {
                $this->users()->attach($user->id);
                return true;
            }
        }

        return false;
    }

    /**
     * Удаляет пользователя из отдела (оба способа)
     * @param User $user - пользователь для удаления
     * @return bool - true если операция успешна
     */
    public function removeUser(User $user): bool
    {
        $removed = false;

        // Удаляем из связи многие-ко-многим
        if ($this->users()->where('user_id', $user->id)->exists()) {
            $removed = $this->users()->detach($user->id) > 0;
        }

        // Если пользователь был добавлен через department_id, сбрасываем его
        if ($user->department_id === $this->id) {
            $user->department_id = null;
            $user->save();
            $removed = true;
        }

        return $removed;
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

    /**
     * Методы для работы почты
     */

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function getEmailCount(): int
    {
        return $this->emails()->count();
    }

    public function getUnreadEmailCount(): int
    {
        return $this->emails()->where('is_read', false)->count();
    }

    public function incrementUnreadCount(): void
    {
        $this->increment('unread_emails_count');
    }

    public function decrementUnreadCount(): void
    {
        if ($this->unread_emails_count > 0) {
            $this->decrement('unread_emails_count');
        }
    }

    public function resetUnreadCount(): void
    {
        $this->update(['unread_emails_count' => 0]);
    }
}
