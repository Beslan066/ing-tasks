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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название компании
            $table->string('phone')->nullable(); // Телефон компании
            $table->boolean('verified')->default(false); // Статус верификации компании
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps(); // created_at и updated_at
            $table->softDeletes(); // deleted_at для мягкого удаления

            // Индексы для оптимизации запросов
            $table->index('verified');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
