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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->longText('body');
            $table->string('from_email');
            $table->string('from_name');
            $table->json('to_emails'); // массив получателей
            $table->json('cc_emails')->nullable();
            $table->json('bcc_emails')->nullable();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('sent_by')->constrained('users')->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('emails')->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_draft')->default(false);
            $table->boolean('is_important')->default(false);
            $table->boolean('has_attachments')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['department_id', 'sent_at']);
            $table->index(['department_id', 'is_read']);
            $table->index(['department_id', 'is_archived']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
