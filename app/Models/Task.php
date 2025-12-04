<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    const STATUS_ASSIGNED = 'назначена';
    const STATUS_NOT_ASSIGNED = 'не назначена';
    const STATUS_IN_PROGRESS = 'в работе';
    const STATUS_OVERDUE = 'просрочена';
    const STATUS_REVIEW = 'на проверке';
    const STATUS_COMPLETED = 'выполнена';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ASSIGNED,
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
        'actual_hours',
        'deleted_by', // Добавляем поле для отслеживания кто удалил
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // === СВЯЗИ ===

    /**
     * Автор задачи
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Пользователь, который удалил задачу
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Исполнитель задачи
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Компания задачи
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Отдел задачи
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Категория задачи
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Файлы, прикрепленные к задаче
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * Подзадачи
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    /**
     * Родительская задача
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    // === МЕТОДЫ ===

    /**
     * Проверяет, может ли пользователь удалить задачу
     */
    public function canBeDeletedBy(User $user): bool
    {
        // Только автор задачи может её удалить
        return $this->author_id === $user->id;
    }

    /**
     * Проверяет, просрочена ли задача
     */
    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast() && $this->status !== 'выполнена';
    }

    /**
     * Отмечает задачу как выполненную
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
     */
    public function changeStatus(string $status): bool
    {
        $allowedStatuses = ['не назначена', 'в работе', 'просрочена', 'выполнена'];

        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        $data = ['status' => $status];

        if ($status === 'выполнена') {
            $data['completed_at'] = now();
        }

        return $this->update($data);
    }

    /**
     * Получает прогресс выполнения задачи в процентах
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

        return match($this->status) {
            'не назначена' => 0,
            'в работе' => 50,
            'просрочена' => 50,
            default => 0
        };
    }

    /**
     * Проверяет, можно ли удалить задачу
     */
    public function canBeDeleted(): bool
    {
        return $this->status !== 'выполнена' && $this->subtasks()->count() === 0;
    }

    /**
     * Получает оставшееся время до дедлайна
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

    /**
     * Получает файлы задачи с информацией о пользователе
     */
    public function getFilesWithUsers()
    {
        return $this->files()->with('user')->get();
    }

    /**
     * Получает количество файлов в задаче
     */
    public function getFilesCount(): int
    {
        return $this->files()->count();
    }

    /**
     * Получает цвет статуса для отображения
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'не назначена' => 'yellow',
            'в работе' => 'blue',
            'на проверке' => 'orange',
            'просрочена' => 'red',
            'выполнена' => 'green',
            default => 'gray'
        };
    }

    /**
     * Получает иконку статуса
     */
    public function getStatusIcon(): string
    {
        return match($this->status) {
            'не назначена' => '⏳',
            'в работе' => '🔄',
            'на проверке' => '👀',
            'просрочена' => '⚠️',
            'выполнена' => '✅',
            default => '📝'
        };
    }

    /**
     * Отказы от задачи
     */
    public function rejections(): HasMany
    {
        return $this->hasMany(TaskRejection::class);
    }

    /**
     * Получить последний отказ
     */
    public function getLastRejection()
    {
        return $this->rejections()->latest()->first();
    }

    /**
     * Получить количество отказов
     */
    public function getRejectionsCount(): int
    {
        return $this->rejections()->count();
    }

    /**
     * Автоматически обновляет статус на "просрочена" если дедлайн прошел
     */
    public function updateOverdueStatus(): bool
    {
        if ($this->deadline &&
            $this->deadline->isPast() &&
            !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_OVERDUE])) {
            return $this->update([
                'status' => self::STATUS_OVERDUE
            ]);
        }

        return false;
    }
}
