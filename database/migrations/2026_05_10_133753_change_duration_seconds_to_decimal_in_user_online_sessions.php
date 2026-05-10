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
        Schema::table('user_online_sessions', function (Blueprint $table) {
            $table->decimal('duration_seconds', 10, 6)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_online_sessions', function (Blueprint $table) {
            $table->integer('duration_seconds')->default(0)->change();
        });
    }
};
