<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_visits', function (Blueprint $table) {
            if (!Schema::hasColumn('user_visits', 'total_work_seconds')) {
                $table->integer('total_work_seconds')->default(0)->after('total_time_seconds');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_visits', function (Blueprint $table) {
            $table->dropColumn('total_work_seconds');
        });
    }
};
