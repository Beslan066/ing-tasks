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
        Schema::create('storage_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->bigInteger('total_storage_limit')->default(1073741824); // 1GB в байтах по умолчанию
            $table->bigInteger('used_storage')->default(0);
            $table->integer('file_count')->default(0);
            $table->enum('license_type', ['basic', 'optimal', 'premium'])->default('basic');
            $table->timestamps();

            $table->unique('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_usages');
    }
};
