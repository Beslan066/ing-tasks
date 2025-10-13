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
        Schema::create('department_user', function (Blueprint $table) {
            $table->id();

            // Внешние ключи
            $table->foreignId('department_id')
                ->constrained()
                ->onDelete('cascade'); // Каскадное удаление при удалении отдела

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade'); // Каскадное удаление при удалении пользователя

            $table->timestamps();

            // Уникальный индекс для предотвращения дублирования связей
            $table->unique(['department_id', 'user_id']);

            // Индексы
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_user');
    }
};
