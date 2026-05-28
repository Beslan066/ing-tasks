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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'premium', 'basic'
            $table->string('status')->default('active'); // 'active', 'expired', 'cancelled', 'pending'
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->integer('base_user_slots')->default(15);
            $table->bigInteger('storage_limit')->default(1073741824000);
            $table->json('features')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_provider_id')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->timestamps();

            $table->index(['company_id', 'status', 'expires_at']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->string('payment_type'); // 'subscription', 'additional_users'
            $table->string('status')->default('pending'); // 'pending', 'completed', 'failed', 'refunded'
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('RUB');
            $table->string('period')->nullable(); // 'month', '6months', 'year'
            $table->integer('user_count')->nullable();
            $table->string('payment_provider'); // 'yookassa', 'stripe', 'tinkoff'
            $table->string('provider_payment_id')->unique();
            $table->string('provider_payment_url')->nullable();
            $table->json('provider_data')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index('provider_payment_id');
        });

        Schema::create('additional_user_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->integer('user_count');
            $table->string('period'); // 'month', '6months', 'year'
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['company_id', 'is_active', 'expires_at']);
        });

        Schema::create('payment_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('event_type');
            $table->string('payment_id')->nullable();
            $table->json('payload');
            $table->string('ip_address')->nullable();
            $table->boolean('processed')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['provider', 'processed', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_logs');
        Schema::dropIfExists('additional_user_purchases');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('subscriptions');
    }
};
