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
        Schema::create('smart_search_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Tier & limits
            $table->string('tier'); // starter, standard, priority, vip
            $table->unsignedInteger('searches_limit'); // 1, 3, 5, or 999 for unlimited
            $table->unsignedInteger('searches_used')->default(0);
            $table->unsignedInteger('duration_days'); // 60, 90, 90, 120

            // Payment info
            $table->decimal('amount_paid', 15, 2);
            $table->string('payment_reference')->nullable();
            $table->string('payment_method')->default('paystack');
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded

            // Dates
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            // Renewal info
            $table->boolean('is_renewal')->default(false);
            $table->decimal('renewal_discount', 5, 2)->default(0); // 50% = 50.00
            $table->foreignId('renewed_from_id')->nullable()->constrained('smart_search_subscriptions')->nullOnDelete();

            // Notification preferences (copied from tier config)
            $table->json('notification_channels')->nullable(); // ['email', 'whatsapp', 'sms']

            // Metadata
            $table->json('payment_metadata')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'tier', 'expires_at'], 'subscriptions_user_tier_expires_idx');
            $table->index(['payment_status', 'expires_at'], 'subscriptions_status_expires_idx');
            $table->index('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_search_subscriptions');
    }
};
