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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            // Связи
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');

            // Основная информация
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('color')->nullable();

            // Тип и статус
            $table->enum('type', ['meeting', 'deadline', 'reminder', 'other'])->default('other');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['planned', 'ongoing', 'completed', 'cancelled'])->default('planned');

            // Время
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->boolean('all_day')->default(false);

            // Повторение (для будущего расширения)
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence_type', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->timestamp('recurrence_end_date')->nullable();
            $table->foreignId('parent_event_id')->nullable()->constrained('events')->onDelete('cascade');

            // Дополнительно
            $table->boolean('is_public')->default(true);
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Индексы для быстрого поиска
            $table->index(['company_id', 'start_date']);
            $table->index(['company_id', 'end_date']);
            $table->index(['creator_id']);
            $table->index(['department_id']);
            $table->index(['status']);
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
