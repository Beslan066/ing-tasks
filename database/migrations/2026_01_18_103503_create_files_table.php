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
            $table->string('name');
            $table->string('path');
            $table->string('disk')->default('public');
            $table->string('folder')->nullable();
            $table->bigInteger('size');
            $table->string('mime_type');
            $table->string('extension');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('set null');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_public')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'department_id']);
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
