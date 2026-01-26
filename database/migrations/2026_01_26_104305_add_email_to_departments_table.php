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
        Schema::table('departments', function (Blueprint $table) {
            $table->string('email')->unique()->nullable()->after('name');
            $table->timestamp('email_verified_at')->nullable();
        });

        Schema::table('emails', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');

            // Добавляем полимофрные связи
            $table->nullableMorphs('recipient');
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['email', 'email_verified_at']);
        });

        Schema::table('emails', function (Blueprint $table) {
            $table->dropMorphs('recipient');
            $table->dropColumn('sender_id');

            $table->foreignId('department_id')->constrained()->onDelete('cascade');
        });
    }
};
