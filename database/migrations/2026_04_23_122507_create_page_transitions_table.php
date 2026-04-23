<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_visit_id')->nullable()->constrained()->onDelete('set null');
            $table->text('from_url')->nullable();
            $table->text('to_url');
            $table->timestamp('transition_at');
            $table->timestamps();

            $table->index('user_id');
            $table->index('transition_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_transitions');
    }
};
