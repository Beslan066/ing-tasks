<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'department_id',
    ];

    // === СВЯЗИ ===

    /**
     * Отдел, к которому принадлежит роль
     * @return BelongsTo - возвращает отдел роли
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Пользователи с этой ролью
     * @return HasMany - возвращает всех пользователей с этой ролью
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // === МЕТОДЫ ===


    /**
     * Добавляет разрешение к роли
     * @param string $permission - добавляемое разрешение
     * @return bool - true если операция успешна
     */
}
