<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'company_id',
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
    public function role(): BelongsTo {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * Связь с компанией пользователя
     * @return BelongsTo - возвращает компанию, в которой работает пользователь
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
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
     * ИЛИ является владельцем своей текущей компании
     */
    public function isCompanyOwner(): bool
    {
        // Проверяем владение компаниями через связь ownedCompanies
        if ($this->ownedCompanies()->exists()) {
            return true;
        }

        // Проверяем, является ли пользователь владельцем своей текущей компании
        if ($this->company_id && $this->company) {
            return $this->company->user_id === $this->id;
        }

        return false;
    }

    /**
     * Получает ВСЕ компании, которыми пользователь владеет/руководит
     * включая компанию, где он указан как user_id
     */
    public function getAllOwnedCompanies()
    {
        $companies = $this->ownedCompanies;

        // Добавляем компанию, где пользователь является владельцем через user_id
        if ($this->company && $this->company->user_id === $this->id) {
            $companies->push($this->company);
        }

        return $companies->unique('id');
    }

    /**
     * Проверяет, является ли пользователь руководителем ЛЮБОГО уровня
     */
    public function isLeader(): bool
    {
        return $this->isCompanyOwner() ||
            $this->isSupervisor() ||
            $this->hasRole('руководитель') ||
            $this->hasRole('администратор');
    }

    /**
     * Обновленный метод - руководитель любого уровня
     */
    public function isManager(): bool
    {
        return $this->isLeader();
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

    // В модель User добавьте:
    /**
     * Получает категории компании пользователя
     * @return \Illuminate\Database\Eloquent\Collection - коллекция категорий
     */
    public function getCompanyCategories()
    {
        if (!$this->company_id) {
            return collect();
        }

        return Category::whereHas('tasks.department', function($query) {
            $query->where('company_id', $this->company_id);
        })->distinct()->get();
    }

    /**
     * Получает все отделы компании пользователя
     * @return \Illuminate\Database\Eloquent\Collection - коллекция отделов
     */
    public function getCompanyDepartments()
    {
        if (!$this->company_id) {
            return collect();
        }

        return Department::where('company_id', $this->company_id)
            ->with('supervisor')
            ->get();
    }

    /**
     * Проверяет, имеет ли пользователь доступ к просмотру всех задач компании
     */
    public function canViewAllCompanyTasks(): bool
    {
        // Руководитель компании или руководитель отдела
        return $this->isCompanyOwner() || $this->isSupervisor();
    }

    /**
     * Получает задачи, которые пользователь может видеть
     */
    public function getAccessibleTasks()
    {
        if ($this->canViewAllCompanyTasks()) {
            return Task::where('company_id', $this->company_id);
        }

        // Обычный сотрудник - только свои задачи и задачи своего отдела
        return Task::where('company_id', $this->company_id)
            ->where(function($query) {
                $query->where('user_id', $this->id)
                    ->orWhere('department_id', $this->department_id);
            });
    }

    /**
     * Проверяет, имеет ли пользователь указанную роль
     */
    public function hasRole(string $roleName): bool
    {
        if (!$this->role) {
            return false;
        }

        // Предполагаем, что у модели Role есть поле 'name'
        return $this->role->name === $roleName;
    }

    /**
     * Получает последние задачи пользователя
     */
    public function getRecentTasks($limit = 5)
    {
        return $this->assignedTasks()
            ->with(['category', 'department'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Альтернативный метод - расчет на основе времени выполнения
     */
    public function getTimeBasedCompletionRate($period = null): float
    {
        $query = $this->assignedTasks()->where('status', 'выполнена');

        if ($period) {
            switch ($period) {
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->startOfMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', Carbon::now()->startOfYear());
                    break;
            }
        }

        $completedTasks = $query->count();
        $totalTasks = $this->assignedTasks()->when($period, function($q) use ($period) {
            switch ($period) {
                case 'week':
                    $q->where('created_at', '>=', Carbon::now()->startOfWeek());
                    break;
                case 'month':
                    $q->where('created_at', '>=', Carbon::now()->startOfMonth());
                    break;
                case 'year':
                    $q->where('created_at', '>=', Carbon::now()->startOfYear());
                    break;
            }
        })->count();

        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
    }

    /**
     * Рассчитывает средний процент выполнения задач пользователя
     */
    public function getAverageCompletionRate($period = null): float
    {
        $query = $this->assignedTasks();

        if ($period && $period !== 'all') {
            switch ($period) {
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->startOfMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', Carbon::now()->startOfYear());
                    break;
            }
        }

        $tasks = $query->get();

        if ($tasks->isEmpty()) {
            return 0;
        }

        $totalCompletion = 0;
        $count = 0;

        foreach ($tasks as $task) {
            // Если у задачи есть поле progress, используем его
            if (isset($task->progress) && is_numeric($task->progress)) {
                $totalCompletion += min(100, max(0, $task->progress)); // Ограничиваем от 0 до 100
                $count++;
            } else {
                // Если нет поля progress, вычисляем на основе статуса
                $completion = match($task->status) {
                    'выполнена' => 100,
                    'в работе' => 50,
                    'не назначена' => 0,
                    'просрочена' => 25, // начали, но просрочили
                    default => 0
                };
                $totalCompletion += $completion;
                $count++;
            }
        }

        return $count > 0 ? round($totalCompletion / $count, 1) : 0;
    }

    /**
     * Получает статистику выполнения задач пользователя (для таблицы)
     */
    public function getTaskCompletionStats($period = null)
    {
        $total = $this->assignedTasks()->when($period && $period !== 'all', function($query) use ($period) {
            switch ($period) {
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->startOfMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', Carbon::now()->startOfYear());
                    break;
            }
        })->count();

        $completed = $this->assignedTasks()->where('status', 'выполнена')
            ->when($period && $period !== 'all', function($query) use ($period) {
                switch ($period) {
                    case 'week':
                        $query->where('created_at', '>=', Carbon::now()->startOfWeek());
                        break;
                    case 'month':
                        $query->where('created_at', '>=', Carbon::now()->startOfMonth());
                        break;
                    case 'year':
                        $query->where('created_at', '>=', Carbon::now()->startOfYear());
                        break;
                }
            })->count();

        // Используем средний процент выполнения
        $completionRate = $this->getAverageCompletionRate($period);

        return [
            'total' => $total,
            'completed' => $completed,
            'completion_rate' => $completionRate
        ];
    }
}
