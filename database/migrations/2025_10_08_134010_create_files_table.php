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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Оригинальное имя файла
            $table->string('file'); // Путь к файлу в хранилище
            $table->string('file_path')->nullable(); // Дополнительный путь (если нужен)
            $table->bigInteger('file_size')->nullable(); // Размер файла в байтах
            $table->string('mime_type')->nullable(); // MIME-тип файла

            // Внешние ключи
            $table->foreignId('department_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade'); // Каскадное удаление при удалении отдела

            $table->foreignId('task_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade'); // Каскадное удаление при удалении задачи

            $table->foreignId('user_id') // Пользователь, загрузивший файл
            ->constrained()
                ->onDelete('cascade'); // Каскадное удаление при удалении пользователя

            $table->timestamps();
            $table->softDeletes();

            // Индексы
            $table->index('department_id');
            $table->index('task_id');
            $table->index('user_id');
            $table->index('mime_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
