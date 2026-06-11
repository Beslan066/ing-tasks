<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subtask extends Model
{
    use SoftDeletes;

    const STATUS_ASSIGNED = 'назначена';
    const STATUS_IN_PROGRESS = 'в работе';
    const STATUS_COMPLETED = 'выполнена';
    const STATUS_OVERDUE = 'просрочена';

    protected $fillable = [
        'task_id',
        'name',
        'description',
        'user_id',
        'status',
        'priority',
        'deadline',
        'estimated_hours',
        'actual_hours',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // === СВЯЗИ ===

    /**
     * Родительская задача
     */
    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * Исполнитель
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Создатель
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // === МЕТОДЫ ===

    /**
     * Проверяет, просрочена ли подзадача
     */
    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast() && $this->status !== self::STATUS_COMPLETED;
    }

    /**
     * Отмечает подзадачу как выполненную
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now()
        ]);
    }

    /**
     * Обновляет статус родительской задачи на основе подзадач
     */
    public static function updateParentTaskProgress($taskId)
    {
        $task = Task::find($taskId);
        if (!$task) return;

        $subtasks = self::where('task_id', $taskId)->get();
        $total = $subtasks->count();

        if ($total === 0) return;

        $completed = $subtasks->where('status', self::STATUS_COMPLETED)->count();

        if ($completed === $total) {
            // Все подзадачи выполнены
            if ($task->status !== Task::STATUS_COMPLETED) {
                $task->update([
                    'status' => Task::STATUS_COMPLETED,
                    'completed_at' => now()
                ]);
            }
        } elseif ($completed > 0 && $task->status !== Task::STATUS_IN_PROGRESS) {
            // Часть подзадач выполнена
            $task->update(['status' => Task::STATUS_IN_PROGRESS]);
        }
    }

    /**
     * Получает прогресс выполнения подзадач для задачи
     */
    public static function getProgress($taskId): array
    {
        $subtasks = self::where('task_id', $taskId)->get();
        $total = $subtasks->count();
        $completed = $subtasks->where('status', self::STATUS_COMPLETED)->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'percent' => $total > 0 ? round(($completed / $total) * 100) : 0,
        ];
    }

    /**
     * Автор подзадачи (тот же что и creator, для совместимости с шаблонами)
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
