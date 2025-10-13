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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название задачи
            $table->text('description')->nullable(); // Описание задачи
            $table->string('status')->default('не назначена'); // Статус задачи
            $table->string('priority')->default('medium'); // Приоритет задачи (low, medium, high, urgent)
            $table->string('files')->nullable();
            $table->dateTime('deadline')->nullable(); // Дедлайн задачи
            $table->dateTime('completed_at')->nullable(); // Дата завершения задачи
            $table->decimal('estimated_hours', 8, 2)->nullable(); // Оценочное время выполнения
            $table->decimal('actual_hours', 8, 2)->nullable(); // Фактическое время выполнения

            // Внешние ключи
            $table->foreignId('author_id') // Автор задачи
            ->constrained('users')
                ->onDelete('cascade'); // Каскадное удаление при удалении автора

            $table->foreignId('user_id') // Исполнитель задачи
            ->nullable()
                ->constrained('users')
                ->onDelete('set null'); // Установить NULL при удалении исполнителя

            $table->foreignId('department_id') // Отдел задачи
            ->constrained()
                ->onDelete('cascade'); // Каскадное удаление при удалении отдела

            $table->foreignId('category_id') // Категория задачи
            ->nullable()
                ->constrained()
                ->onDelete('set null'); // Установить NULL при удалении категории

            $table->foreignId('parent_id') // Родительская задача (для подзадач)
            ->nullable()
                ->constrained('tasks')
                ->onDelete('cascade'); // Каскадное удаление при удалении родительской задачи

            $table->timestamps();
            $table->softDeletes();

            // Индексы для оптимизации запросов
            $table->index('status');
            $table->index('priority');
            $table->index('deadline');
            $table->index('author_id');
            $table->index('user_id');
            $table->index('department_id');
            $table->index('category_id');
            $table->index('parent_id');
            $table->index('completed_at');
            $table->index(['status', 'deadline']); // Составной индекс для поиска просроченных задач
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
