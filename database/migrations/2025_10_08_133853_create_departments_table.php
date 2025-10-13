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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название отдела
            $table->string('status')->default('active'); // Статус отдела (active/inactive)

            // Внешние ключи
            $table->foreignId('company_id') // Связь с компанией
            ->constrained()
                ->onDelete('cascade'); // Каскадное удаление при удалении компании

            $table->foreignId('supervisor_id') // Связь с руководителем отдела
            ->nullable()
                ->constrained('users')
                ->onDelete('set null'); // Установить NULL при удалении пользователя

            $table->timestamps();
            $table->softDeletes();

            // Индексы
            $table->index('company_id');
            $table->index('supervisor_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
