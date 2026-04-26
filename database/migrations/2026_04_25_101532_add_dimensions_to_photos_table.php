<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->integer('width')->nullable()->after('file_size');
            $table->integer('height')->nullable()->after('width');
            $table->string('optimized_path')->nullable()->after('file_path');
            $table->json('variants')->nullable()->after('optimized_path'); // для хранения разных версий
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn(['width', 'height', 'optimized_path', 'variants']);
        });
    }
};
