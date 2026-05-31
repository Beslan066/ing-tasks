<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Проверяем существование таблицы
        if (!Schema::hasTable('activity_log')) {
            // Таблицы нет - создаем с нуля
            Schema::create('activity_log', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('subject_type')->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->string('action');
                $table->text('description')->nullable();
                $table->json('properties')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->timestamps();

                // Создаем индексы
                $table->index(['subject_type', 'subject_id']);
                $table->index(['company_id', 'created_at']);
                $table->index('action');
                $table->index('user_id');
            });
        } else {
            // Таблица существует - добавляем только то, чего нет

            // Проверяем и добавляем колонки
            Schema::table('activity_log', function (Blueprint $table) {
                if (!Schema::hasColumn('activity_log', 'description')) {
                    $table->text('description')->nullable();
                }

                if (!Schema::hasColumn('activity_log', 'old_values')) {
                    $table->json('old_values')->nullable();
                }

                if (!Schema::hasColumn('activity_log', 'new_values')) {
                    $table->json('new_values')->nullable();
                }
            });

            // Добавляем индексы через raw SQL для PostgreSQL
            $this->addIndexIfNotExists('activity_log', ['subject_type', 'subject_id'], 'activity_log_subject_type_subject_id_index');
            $this->addIndexIfNotExists('activity_log', ['company_id', 'created_at'], 'activity_log_company_id_created_at_index');
            $this->addIndexIfNotExists('activity_log', ['action'], 'activity_log_action_index');
            $this->addIndexIfNotExists('activity_log', ['user_id'], 'activity_log_user_id_index');
        }
    }

    /**
     * Добавить индекс если он не существует для PostgreSQL
     */
    private function addIndexIfNotExists($table, $columns, $indexName)
    {
        // Проверяем существование индекса в PostgreSQL
        $result = DB::select("
            SELECT 1
            FROM pg_class c
            JOIN pg_namespace n ON n.oid = c.relnamespace
            WHERE c.relname = ?
            AND c.relkind = 'i'
            AND n.nspname = 'public'
        ", [$indexName]);

        if (empty($result)) {
            // Индекс не существует - создаем
            $columnsStr = is_array($columns) ? implode('", "', $columns) : $columns;
            $columnsStr = is_array($columns) ? '"' . implode('", "', $columns) . '"' : $columns;

            DB::statement("CREATE INDEX \"{$indexName}\" ON \"{$table}\" ({$columnsStr})");
        }
    }

    public function down()
    {
        Schema::dropIfExists('activity_log');
    }
};
