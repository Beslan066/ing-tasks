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
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'company_id',
        'is_active',
        'last_login_at',
        'avatar',
        'provider',
        'provider_id',
        'provider_avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    const ROLE_SUPERVISOR = "Руководитель";

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'last_activity_at' => 'datetime',
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
     * Получить основной отдел пользователя (первый или помеченный is_primary)
     */
    public function primaryDepartment(): ?Department
    {
        return $this->departments()
            ->wherePivot('is_primary', true)
            ->first();
    }

    /**
     * Получить названия всех отделов пользователя в виде строки
     */
    public function getDepartmentsListAttribute(): string
    {
        return $this->departments->pluck('name')->implode(', ');
    }

    /**
     * Проверить, состоит ли пользователь в указанном отделе
     */
    public function isInDepartment(int $departmentId): bool
    {
        return $this->departments()->where('department_id', $departmentId)->exists();
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
        return $this->role_id === self::ROLE_SUPERVISOR
            && $this->supervisedDepartments()->exists();
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
        // Проверяем стандартные признаки руководителя
        if ($this->isCompanyOwner() || $this->isSupervisor()) {
            return true;
        }

        // Проверяем по роли - используем точное название
        if (!$this->role) {
            return false;
        }

        $roleName = trim($this->role->name);
        return $roleName === 'Руководитель' || $roleName === 'Администратор';
    }
    /**
     * Обновленный метод - руководитель любого уровня
     */
    public function isManager(): bool
    {
        return $this->isLeader() || $this->isManagerRole();
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
        // Владелец компании, руководитель, менеджер - все могут создавать задачи
        return $this->isCompanyOwner() || $this->isSupervisor() || $this->isLeader() || $this->isManagerRole();
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
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        if (!$this->role) {
            return false;
        }

        // Упрощаем: сравниваем как есть, без приведения к нижнему регистру
        return trim($this->role->name) === trim($roleName);
    }


    /**
     * Проверяет, имеет ли пользователь одну из указанных ролей
     */
    public function hasAnyRole(array $roleNames): bool
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        if (!$this->role) {
            return false;
        }

        foreach ($roleNames as $roleName) {
            if (strtolower(trim($this->role->name)) === strtolower(trim($roleName))) {
                return true;
            }
        }

        return false;
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

    /**
     * Проверяет, является ли пользователь менеджером (имеет роль менеджера)
     */
    public function isManagerRole(): bool
    {
        if (!$this->role) {
            return false;
        }

        return trim($this->role->name) === 'Менеджер';
    }

    /**
     * Проверяет, может ли пользователь создавать задачи для других
     */
    public function canCreateTasksForOthers(): bool
    {
        return $this->isManager() || $this->isManagerRole();
    }

    /**
     * Получает отделы, которыми может управлять пользователь
     */
    public function getManageableDepartments()
    {
        if ($this->isManager()) {
            // Руководитель управляет всеми отделами компании
            return Department::where('company_id', $this->company_id)->get();
        }

        if ($this->isManagerRole()) {
            // Менеджер управляет своим отделом и подчиненными отделами
            $departmentIds = [$this->department_id];

            // Если менеджер руководит какими-то отделами
            if ($this->supervisedDepartments()->exists()) {
                $departmentIds = array_merge(
                    $departmentIds,
                    $this->supervisedDepartments()->pluck('id')->toArray()
                );
            }

            return Department::whereIn('id', $departmentIds)->get();
        }

        return collect();
    }

    /**
     * Получает пользователей, которым можно назначать задачи
     */
    public function getAssignableUsers()
    {
        if ($this->isLeader()) {
            // РУКОВОДИТЕЛЬ: все активные пользователи компании
            return User::where('company_id', $this->company_id)
                ->where('is_active', true)
                ->get();
        }

        if ($this->isManagerRole()) {
            // МЕНЕДЖЕР: только пользователи из доступных отделов
            $departmentIds = $this->getManageableDepartments()->pluck('id')->toArray();

            return User::where('company_id', $this->company_id)
                ->whereIn('department_id', $departmentIds)
                ->where('is_active', true)
                ->get();
        }

        return collect();
    }

    /**
     * Получает всех пользователей компании (для руководителя)
     */
    public function getAllCompanyUsers()
    {
        if (!$this->company_id) {
            return collect();
        }

        return User::where('company_id', $this->company_id)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Получает пользователей для назначения задач в зависимости от роли
     */
    public function getUsersForTaskAssignment()
    {
        if ($this->isLeader()) {
            // Руководитель видит всех пользователей компании
            return $this->getAllCompanyUsers();
        }

        // Менеджер видит только пользователей из своих отделов
        return $this->getAssignableUsers();
    }
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }

        // Если есть аватар из соц. сети, используем его
        if ($this->provider_avatar) {
            return $this->provider_avatar;
        }

        return $this->defaultAvatarUrl();
    }

    private function defaultAvatarUrl(): string
    {
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF";
    }

    /**
     * Пометить пользователя как вышедшего
     */
    public function markAsOffline(): void
    {
        $this->update([
            'last_activity_at' => now()->subMinutes(10), // Устанавливаем время 10 минут назад
            'is_active' => false // Или оставляем true, но время показывает оффлайн
        ]);
    }

    /**
     * Получает точное время последней активности
     */

    /**
     * Получает время последней активности в удобном формате
     */
    public function getLastActivityText(): string
    {
        if (!$this->last_activity_at) {
            return 'Никогда не был активен';
        }

        $diffInMinutes = $this->last_activity_at->diffInMinutes(now());

        if ($diffInMinutes < 1) {
            return 'Только что';
        }

        if ($diffInMinutes < 60) {
            return $diffInMinutes . ' мин. назад';
        }

        if ($diffInMinutes < 1440) {
            $hours = floor($diffInMinutes / 60);
            return $hours . ' ' . trans_choice('час|часа|часов', $hours) . ' назад';
        }

        return $this->last_activity_at->format('d.m.Y H:i');
    }


    /**
     * Получает иконку статуса онлайн
     */
    public function getOnlineStatusIcon(): string
    {
        if ($this->isOnline()) {
            return 'fa-circle';
        }

        if ($this->isOnline(60)) {
            return 'fa-clock';
        }

        return 'fa-circle-o';
    }

    public function getInitials(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8');
            }

            if (mb_strlen($initials, 'UTF-8') >= 2) {
                break;
            }
        }

        return $initials ?: '??';
    }

    /**
     * Получает цвет для аватара
     */
    public function getAvatarColor(): string
    {
        $colors = [
            'bg-blue-500',
            'bg-purple-500',
            'bg-red-500',
            'bg-yellow-500',
            'bg-green-500',
            'bg-indigo-500',
            'bg-pink-500',
            'bg-teal-500',
            'bg-orange-500',
            'bg-cyan-500',
        ];

        $hash = crc32($this->name);
        $index = abs($hash) % count($colors);

        return $colors[$index];
    }

    public function markAsLoggedIn(): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_activity_at' => now(),
            'is_active' => true
        ]);
    }

    /**
     * Обновляет время последней активности пользователя
     */
    public function updateLastActivity(): void
    {
        $this->timestamps = false; // Отключаем автоматическое обновление updated_at
        $this->update([
            'last_activity_at' => now()
        ]);
        $this->timestamps = true; // Включаем обратно
    }

    /**
     * Проверяет наличие разрешения у пользователя
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->role) {
            return false;
        }

        // Если у роли есть поле permissions (массив)
        if ($this->role->permissions && is_array($this->role->permissions)) {
            return in_array($permission, $this->role->permissions);
        }

        // Fallback: проверка по названию роли
        return $this->checkPermissionByRoleName($permission);
    }

    /**
     * Проверяет разрешение на основе названия роли (fallback)
     */
    private function checkPermissionByRoleName(string $permission): bool
    {
        if (!$this->role) {
            return false;
        }

        $roleName = trim($this->role->name);

        // Базовая логика прав доступа по ролям
        switch ($permission) {
            case 'access_email':
                // Доступ к почте имеют все пользователи с ролью
                return in_array($roleName, ['Администратор', 'Руководитель', 'Менеджер', 'Сотрудник']);

            case 'send_emails':
                return in_array($roleName, ['Администратор', 'Руководитель', 'Менеджер']);

            case 'view_all_emails':
                return in_array($roleName, ['Администратор', 'Руководитель']);

            case 'create_tasks':
                return in_array($roleName, ['Администратор', 'Руководитель', 'Менеджер']);

            case 'manage_users':
                return in_array($roleName, ['Администратор', 'Руководитель']);

            case 'manage_departments':
                return in_array($roleName, ['Администратор', 'Руководитель']);

            default:
                // По умолчанию администраторы имеют все права
                return $roleName === 'Администратор';
        }
    }

    /**
     * Проверяет наличие хотя бы одного из переданных разрешений
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Проверяет наличие всех переданных разрешений
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    public function emails()
    {
        return $this->morphMany(Email::class, 'recipient');
    }

    /**
     * Получить отправленные письма
     */
    public function sentEmails()
    {
        return $this->hasMany(Email::class, 'sender_id');
    }

    /**
     * Получить непрочитанные письма
     */
    public function getUnreadEmails()
    {
        return $this->emails()->where('is_read', false)->count();
    }

    /**
     * Получить количество непрочитанных писем во всех отделах пользователя
     */
    public function getDepartmentUnreadEmails(): int
    {
        $total = 0;
        foreach ($this->departments as $department) {
            if ($department->email) {
                $total += $department->getUnreadEmails();
            }
        }
        return $total;
    }

    /**
     * Получить общее количество непрочитанных писем
     */
    public function getTotalUnreadEmails(): int
    {
        return $this->getUnreadEmails() + $this->getDepartmentUnreadEmails();
    }

    /**
     * Чаты пользователя
     */
    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_user')
            ->withPivot(['role', 'last_read_at', 'is_muted', 'joined_at', 'left_at'])
            ->withTimestamps()
            ->wherePivotNull('left_at'); // Только активные чаты
    }

    /**
     * Сообщения пользователя
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Статусы сообщений пользователя
     */
    public function messageStatuses()
    {
        return $this->hasMany(MessageStatus::class);
    }

    /**
     * Получить непрочитанные сообщения
     */
    public function getUnreadMessagesCount()
    {
        $count = 0;

        foreach ($this->chats as $chat) {
            $lastRead = $chat->pivot->last_read_at;

            $unread = Message::where('chat_id', $chat->id)
                ->where('user_id', '!=', $this->id)
                ->when($lastRead, function ($query) use ($lastRead) {
                    $query->where('created_at', '>', $lastRead);
                })
                ->count();

            $count += $unread;
        }

        return $count;
    }

    /**
     * Получить коллег по компании
     */
    public function getColleagues()
    {
        return User::where('company_id', $this->company_id)
            ->where('id', '!=', $this->id)
            ->where('is_active', true)
            ->get();
    }

    public static function getPrivateChat($user1Id, $user2Id, $companyId)
    {
        // Ищем чат где есть оба пользователя
        $chats = self::where('company_id', $companyId)
            ->where('type', 'private')
            ->whereHas('users', function ($query) use ($user1Id) {
                $query->where('user_id', $user1Id);
            })
            ->whereHas('users', function ($query) use ($user2Id) {
                $query->where('user_id', $user2Id);
            })
            ->get();

        // Проверяем каждый чат вручную
        foreach ($chats as $chat) {
            $userIds = $chat->users()->pluck('user_id')->toArray();
            if (in_array($user1Id, $userIds) && in_array($user2Id, $userIds) && count($userIds) == 2) {
                return $chat;
            }
        }

        // Создаем новый чат
        DB::beginTransaction();
        try {
            $chat = self::create([
                'company_id' => $companyId,
                'created_by' => $user1Id,
                'type' => 'private'
            ]);

            $chat->users()->attach($user1Id, ['role' => 'admin', 'joined_at' => now()]);
            $chat->users()->attach($user2Id, ['role' => 'member', 'joined_at' => now()]);

            DB::commit();
            return $chat;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Связь с визитами
     */
    public function visits()
    {
        return $this->hasMany(UserVisit::class);
    }

    /**
     * Связь с сессиями
     */
    public function onlineSessions()
    {
        return $this->hasMany(UserOnlineSession::class);
    }

    /**
     * Получить статистику посещений за период
     */
    public function getVisitStats($period = 'month')
    {
        $query = $this->visits();

        switch ($period) {
            case 'week':
                $query->where('date', '>=', Carbon::now()->startOfWeek());
                break;
            case 'month':
                $query->where('date', '>=', Carbon::now()->startOfMonth());
                break;
            case 'year':
                $query->where('date', '>=', Carbon::now()->startOfYear());
                break;
        }

        $visits = $query->orderBy('date')->get();

        return [
            'total_visits' => $visits->count(),
            'total_page_views' => $visits->sum('page_views'),
            'total_time_seconds' => $visits->sum('total_time_seconds'),
            'average_time_per_visit' => $visits->avg('total_time_seconds') ?: 0,
            'average_pages_per_visit' => $visits->avg('page_views') ?: 0,
            'daily_stats' => $visits->map(function($visit) {
                return [
                    'date' => $visit->formatted_date,
                    'page_views' => $visit->page_views,
                    'time' => $visit->formatted_total_time,
                ];
            }),
        ];
    }

    /**
     * Получить общее время на сайте (всего)
     */
    public function getTotalTimeOnSite(): int
    {
        return $this->visits()->sum('total_time_seconds');
    }

    /**
     * Получить форматированное общее время
     */
    public function getFormattedTotalTimeAttribute(): string
    {
        $totalSeconds = $this->getTotalTimeOnSite();
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);

        if ($hours > 0) {
            return sprintf('%d ч. %d мин.', $hours, $minutes);
        }
        return sprintf('%d мин.', $minutes);
    }

    /**
     * Проверяет, онлайн ли пользователь
     */
    public function isOnline(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->last_activity_at) {
            return false;
        }

        return $this->last_activity_at->diffInMinutes(now()) < 5;
    }

    /**
     * Получает текст статуса онлайн
     */
    public function getOnlineStatusText(): string
    {
        if ($this->isOnline()) {
            return 'Онлайн';
        }

        if ($this->last_activity_at) {
            $minutes = $this->last_activity_at->diffInMinutes(now());
            if ($minutes < 60) {
                return "Был(а) {$minutes} мин. назад";
            }
            $hours = floor($minutes / 60);
            return "Был(а) {$hours} ч. назад";
        }

        return 'Не в сети';
    }

    /**
     * Получает цвет статуса онлайн
     */
    public function getOnlineStatusColor(): string
    {
        if ($this->isOnline()) {
            return 'green';
        }

        if ($this->last_activity_at && $this->last_activity_at->diffInMinutes(now()) < 30) {
            return 'yellow';
        }

        return 'gray';
    }

}
