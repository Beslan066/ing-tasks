<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public const SUPERADMIN = 1;
    public const ADMIN = 2;
    public const HEAD = 3;
    public const MANAGER = 4;
    public const EMPLOYEE = 5;

    public function run(): void
    {
        $now = now();

        $roles = [
            self::SUPERADMIN => [
                'name' => 'Суперадмин',
                'permissions' => ['*'],
            ],
            self::ADMIN => [
                'name' => 'Админ',
                'permissions' => ['users.*', 'departments.*', 'roles.*', 'categories.*', 'tasks.*', 'reports.*'],
            ],
            self::HEAD => [
                'name' => 'Руководитель',
                'permissions' => ['tasks.*', 'departments.view', 'users.view', 'reports.view'],
            ],
            self::MANAGER => [
                'name' => 'Менеджер',
                'permissions' => ['tasks.view', 'tasks.create', 'tasks.update', 'users.view'],
            ],
            self::EMPLOYEE => [
                'name' => 'Сотрудник',
                'permissions' => ['tasks.*'],
            ],
        ];

        $rows = [];
        foreach ($roles as $id => $role) {
            $rows[] = [
                'id' => $id,
                'name' => $role['name'],
                'permissions' => json_encode($role['permissions'], JSON_UNESCAPED_UNICODE),
                'is_active' => true,
                'company_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('roles')->upsert(
            $rows,
            ['id'],
            ['name', 'permissions', 'is_active', 'company_id', 'updated_at']
        );

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('roles', 'id'), (SELECT MAX(id) FROM roles))");
        }
    }
}
