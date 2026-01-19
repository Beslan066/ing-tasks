<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'permissions',
        'department_id',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    // === СВЯЗИ ===

    /**
     * Отдел, к которому принадлежит роль
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Пользователи с этой ролью
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // === МЕТОДЫ ===

    /**
     * Проверяет, есть ли у роли указанное разрешение
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Добавляет разрешение к роли
     */
    public function addPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->permissions = $permissions;
            return $this->save();
        }
        return false;
    }

    /**
     * Удаляет разрешение из роли
     */
    public function removePermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];
        $key = array_search($permission, $permissions);
        if ($key !== false) {
            unset($permissions[$key]);
            $this->permissions = array_values($permissions);
            return $this->save();
        }
        return false;
    }

    /**
     * Список всех доступных разрешений в системе
     */
    public static function availablePermissions(): array
    {
        return [
            // Почта
            'access_email',
            'send_emails',
            'view_all_emails',
            'edit_own_emails',
            'delete_own_emails',
            'delete_all_emails',
            'archive_emails',

            // Задачи
            'create_tasks',
            'edit_tasks',
            'delete_tasks',
            'assign_tasks',
            'view_all_tasks',

            // Файлы
            'upload_files',
            'download_files',
            'delete_files',
            'view_all_files',

            // Пользователи
            'manage_users',
            'invite_users',
            'edit_users',
            'delete_users',

            // Отделы
            'manage_departments',
            'create_departments',
            'edit_departments',
            'delete_departments',

            // Роли и разрешения
            'manage_roles',
            'manage_permissions',

            // Шаблоны писем
            'create_templates',
            'edit_templates',
            'delete_templates',
            'view_global_templates',
            'edit_global_templates',

            // SMTP настройки
            'manage_smtp',
            'manage_department_smtp',
            'manage_company_smtp',

            // Уведомления
            'email_notifications',
            'system_notifications',
        ];
    }
}
