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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название роли
            $table->boolean('is_active')->default(true); // Активна ли роль

            // Внешний ключ для отдела
            $table->foreignId('department_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade'); // Каскадное удаление при удалении отдела

            $table->timestamps();
            $table->softDeletes();

            // Индексы
            $table->index('department_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
