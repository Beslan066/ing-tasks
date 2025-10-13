<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{


    const STATUS_NOT_ASSIGNED = 'не назначена';
    const STATUS_IN_PROGRESS = 'в работе';
    const STATUS_OVERDUE = 'просрочена';
    const STATUS_REVIEW = 'на проверке';
    const STATUS_COMPLETED = 'выполнена';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NOT_ASSIGNED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_OVERDUE,
            self::STATUS_REVIEW,
            self::STATUS_COMPLETED,
        ];
    }
    protected $fillable = [
        'name',
        'author_id',
        'description',
        'company_id',
        'department_id',
        'user_id',
        'status',
        'category_id',
        'priority',
        'deadline',
        'completed_at',
        'estimated_hours',
        'actual_hours'
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // === СВЯЗИ ===

    /**
     * Автор задачи (пользователь, который создал задачу)
     * @return BelongsTo - возвращает пользователя-автора задачи
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Исполнитель задачи (пользователь, которому назначена задача)
     * @return BelongsTo - возвращает пользователя-исполнителя задачи
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Отдел, к которому принадлежит задача
     * @return BelongsTo - возвращает отдел задачи
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Категория задачи
     * @return BelongsTo - возвращает категорию задачи
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Файлы, прикрепленные к задаче
     * @return HasMany - возвращает коллекцию файлов задачи
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * Подзадачи (дочерние задачи)
     * @return HasMany - возвращает коллекцию подзадач
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    /**
     * Родительская задача (для подзадач)
     * @return BelongsTo - возвращает родительскую задачу
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    // === МЕТОДЫ ===

    /**
     * Проверяет, просрочена ли задача
     * @return bool - true если задача просрочена и не выполнена
     */
    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast() && $this->status !== 'выполнена';
    }

    /**
     * Отмечает задачу как выполненную
     * @return bool - true если операция успешна
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => 'выполнена',
            'completed_at' => now()
        ]);
    }

    /**
     * Назначает задачу пользователю
     * @param User $user - пользователь для назначения
     * @return bool - true если операция успешна
     */
    public function assignTo(User $user): bool
    {
        return $this->update([
            'user_id' => $user->id,
            'status' => 'в работе'
        ]);
    }

    /**
     * Изменяет статус задачи
     * @param string $status - новый статус задачи
     * @return bool - true если операция успешна
     */
    public function changeStatus(string $status): bool
    {
        $allowedStatuses = ['не назначена', 'в работе', 'просрочена', 'выполнена'];

        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        $data = ['status' => $status];

        // Если задача выполнена, добавляем время завершения
        if ($status === 'выполнена') {
            $data['completed_at'] = now();
        }

        return $this->update($data);
    }

    /**
     * Получает прогресс выполнения задачи в процентах
     * @return int - прогресс выполнения от 0 до 100
     */
    public function getProgress(): int
    {
        if ($this->status === 'выполнена') {
            return 100;
        }

        $completedSubtasks = $this->subtasks()->where('status', 'выполнена')->count();
        $totalSubtasks = $this->subtasks()->count();

        if ($totalSubtasks > 0) {
            return (int) (($completedSubtasks / $totalSubtasks) * 100);
        }

        // Базовый прогресс по статусу
        return match($this->status) {
            'не назначена' => 0,
            'в работе' => 50,
            'просрочена' => 50,
            default => 0
        };
    }

    /**
     * Проверяет, можно ли удалить задачу
     * @return bool - true если задачу можно удалить
     */
    public function canBeDeleted(): bool
    {
        return $this->status !== 'выполнена' && $this->subtasks()->count() === 0;
    }

    /**
     * Получает оставшееся время до дедлайна
     * @return string - оставшееся время в читаемом формате
     */
    public function getTimeRemaining(): string
    {
        if (!$this->deadline) {
            return 'Без дедлайна';
        }

        if ($this->isOverdue()) {
            return 'Просрочено: ' . $this->deadline->diffForHumans();
        }

        return 'Осталось: ' . $this->deadline->diffForHumans();
    }
}
