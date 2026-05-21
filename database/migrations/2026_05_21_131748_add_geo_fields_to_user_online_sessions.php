<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_online_sessions', function (Blueprint $table) {
            $table->string('country')->nullable()->after('user_agent');
            $table->string('city')->nullable()->after('country');
            $table->decimal('latitude', 10, 8)->nullable()->after('city');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('user_online_sessions', function (Blueprint $table) {
            $table->dropColumn(['country', 'city', 'latitude', 'longitude']);
        });
    }
};
