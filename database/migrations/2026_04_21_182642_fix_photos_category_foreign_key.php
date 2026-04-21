<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Удаляем старый внешний ключ
        Schema::table('photos', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        // Добавляем новый внешний ключ на правильную таблицу
        Schema::table('photos', function (Blueprint $table) {
            $table->foreign('category_id')
                ->references('id')
                ->on('photo_categories')
                ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('restrict');
        });
    }
};
