<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'color',
    ];
    // === СВЯЗИ ===

    /**
     * Задачи, принадлежащие этой категории
     * @return HasMany - возвращает все задачи данной категории
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // === МЕТОДЫ ===

    /**
     * Получает количество активных задач в категории
     * @return int - количество невыполненных задач категории
     */
    public function getActiveTasksCount(): int
    {
        return $this->tasks()->where('status', '!=', 'выполнена')->count();
    }
}
