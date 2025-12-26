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
        Schema::create('smart_search_matches', function (Blueprint $table) {
            $table->id();

            // Core relationships
            $table->foreignId('smart_search_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Match info
            $table->decimal('match_score', 5, 2); // 0.00 - 100.00
            $table->string('tier'); // tier at time of match (for cascade logic)

            // Status tracking for First Dibs cascade
            $table->string('status')->default('pending');
            // pending - waiting in queue
            // queued - scheduled for notification
            // notified - notification sent
            // claimed - user viewed + contacted (VIP only)
            // expired - exclusive window passed without claim
            // skipped - cascade ended (property unavailable)
            // completed - all notifications sent

            // Notification timestamps
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('notified_at')->nullable();

            // VIP exclusive window (3 hours)
            $table->timestamp('exclusive_until')->nullable();

            // Claim tracking (VIP only)
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('claim_expires_at')->nullable(); // 24hr pause end

            // User action tracking
            $table->boolean('property_viewed')->default(false);
            $table->timestamp('property_viewed_at')->nullable();
            $table->boolean('agent_contacted')->default(false);
            $table->timestamp('agent_contacted_at')->nullable();

            // Notification delivery info
            $table->json('notification_channels_used')->nullable();
            $table->json('match_reasons')->nullable();

            // Cascade position (for ordering VIP notifications)
            $table->unsignedInteger('cascade_position')->nullable();

            $table->timestamps();

            // Unique constraint - one match per search per property
            $table->unique(['smart_search_id', 'property_id'], 'smart_search_property_unique');

            // Indexes for cascade processing
            $table->index(['property_id', 'tier', 'status'], 'matches_property_tier_status_idx');
            $table->index(['status', 'exclusive_until'], 'matches_status_exclusive_idx');
            $table->index(['status', 'claim_expires_at'], 'matches_status_claim_idx');
            $table->index(['user_id', 'property_id'], 'matches_user_property_idx');
            $table->index(['property_id', 'status', 'cascade_position'], 'matches_cascade_order_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_search_matches');
    }
};
