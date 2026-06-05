<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'deleted_by',
        'is_personal',
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
        return $this->belongsTo(Department::class)->withDefault([
            'name' => 'Без отдела'
        ]);
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
    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class, 'task_files')
            ->withTimestamps();
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

        $now = now();
        $deadline = clone $this->deadline;

        // Если дедлайн уже прошёл
        if ($deadline->isPast()) {
            $diff = $deadline->diff($now);

            if ($diff->d > 0) {
                $days = $diff->d;
                return "Просрочено на {$days} " . $this->pluralForm($days, 'день', 'дня', 'дней');
            } elseif ($diff->h > 0) {
                $hours = $diff->h;
                return "Просрочено на {$hours} " . $this->pluralForm($hours, 'час', 'часа', 'часов');
            } elseif ($diff->i > 0) {
                $minutes = $diff->i;
                return "Просрочено на {$minutes} " . $this->pluralForm($minutes, 'минуту', 'минуты', 'минут');
            } else {
                return 'Просрочено менее минуты назад';
            }
        }

        // Если дедлайн в будущем
        $diff = $now->diff($deadline);

        if ($diff->d > 0) {
            $days = $diff->d;
            return "Осталось: {$days} " . $this->pluralForm($days, 'день', 'дня', 'дней');
        } elseif ($diff->h > 0) {
            $hours = $diff->h;
            return "Осталось: {$hours} " . $this->pluralForm($hours, 'час', 'часа', 'часов');
        } elseif ($diff->i > 0) {
            $minutes = $diff->i;
            return "Осталось: {$minutes} " . $this->pluralForm($minutes, 'минуту', 'минуты', 'минут');
        } else {
            return 'Осталось: менее минуты';
        }
    }

    /**
     * Вспомогательный метод для склонения слов
     */
    private function pluralForm($number, $one, $two, $many): string
    {
        $number = abs($number) % 100;
        if ($number > 10 && $number < 20) {
            return $many;
        }
        $number %= 10;
        if ($number == 1) {
            return $one;
        }
        if ($number >= 2 && $number <= 4) {
            return $two;
        }
        return $many;
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

    public function scopePersonal($query, $userId = null)
    {
        if ($userId) {
            return $query->where('author_id', $userId)
                ->where('user_id', $userId)
                ->where('is_personal', true);
        }
        return $query->where('is_personal', true);
    }

    // Переопределяем генерацию описания для кастомных действий
    public function logAssignment(User $assignedTo, User $assignedBy)
    {
        return \App\Services\ActivityLogger::taskAssigned($this, $assignedTo, $assignedBy);
    }

    public function logCompletion(User $user)
    {
        return \App\Services\ActivityLogger::taskCompleted($this, $user);
    }

    /**
     * Комментарии к задаче
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->whereNull('parent_id')->with('user', 'replies.user')->orderBy('created_at', 'desc');
    }

    /**
     * Все комментарии (включая ответы)
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->with('user')->orderBy('created_at', 'asc');
    }

    /**
     * Проверить, может ли пользователь видеть комментарии к задаче
     */
    public function canUserViewComments(User $user): bool
    {
        if ($this->is_personal) {
            // Личная задача - только автор и исполнитель
            return $this->author_id === $user->id || $this->user_id === $user->id;
        }

        // Задача отдела - все, кто в отделе
        if ($this->department_id) {
            return $user->isInDepartment($this->department_id);
        }

        // Общая задача компании
        return $this->company_id === $user->company_id;
    }

    /**
     * Проверить, может ли пользователь писать комментарии
     */
    private function canUserComment(Task $task, User $user): bool
    {
        \Log::info('canUserComment check for task ' . $task->id . ', status: ' . $task->status);

        if ($task->is_personal) {
            \Log::info('Task is personal - cannot comment');
            return false;
        }

        if ($task->department_id) {
            $isInDepartment = $user->isInDepartment($task->department_id);
            \Log::info('Department check: user in department ' . $task->department_id . '? ' . ($isInDepartment ? 'yes' : 'no'));
            return $isInDepartment;
        }

        $result = $task->company_id === $user->company_id;
        \Log::info('Company check: ' . $task->company_id . ' === ' . $user->company_id . '? ' . ($result ? 'yes' : 'no'));
        return $result;
    }
}
