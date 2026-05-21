<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_visits', function (Blueprint $table) {
            $table->string('country')->nullable()->after('total_time_seconds');
            $table->string('city')->nullable()->after('country');
            $table->string('device_type')->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('user_visits', function (Blueprint $table) {
            $table->dropColumn(['country', 'city', 'device_type']);
        });
    }
};
