<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Проверяем существует ли колонка
            if (Schema::hasColumn('users', 'department_id')) {
                // Сначала переносим существующие данные в таблицу department_user
                $users = DB::table('users')->whereNotNull('department_id')->get();

                foreach ($users as $user) {
                    // Проверяем, нет ли уже такой записи
                    $exists = DB::table('department_user')
                        ->where('department_id', $user->department_id)
                        ->where('user_id', $user->id)
                        ->exists();

                    if (!$exists) {
                        DB::table('department_user')->insert([
                            'department_id' => $user->department_id,
                            'user_id' => $user->id,
                            'is_primary' => true, // Старый отдел делаем основным
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Удаляем внешний ключ и колонку
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')
                ->nullable()
                ->after('company_id')
                ->constrained()
                ->nullOnDelete();
        });
    }
};
