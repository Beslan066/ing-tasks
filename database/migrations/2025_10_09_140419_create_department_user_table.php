<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Добавляет дополнительные индексы для оптимизации запросов
     */
    public function up(): void
    {
        // Добавляем индексы для часто используемых запросов
        Schema::table('tasks', function (Blueprint $table) {
            // Индекс для поиска задач по статусу и приоритету
            $table->index(['status', 'priority']);

            // Индекс для поиска задач по дедлайну и статусу
            $table->index(['deadline', 'status']);

            // Индекс для поиска задач автора
            $table->index(['author_id', 'created_at']);
        });

        Schema::table('departments', function (Blueprint $table) {
            // Индекс для поиска отделов по компании и статусу
            $table->index(['company_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     * Удаляет добавленные индексы
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['status', 'priority']);
            $table->dropIndex(['deadline', 'status']);
            $table->dropIndex(['author_id', 'created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'is_active']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'status']);
        });
    }
};
