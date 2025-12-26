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
        // Step 1: Rename the table
        Schema::rename('saved_searches', 'smart_searches');

        // Step 2: Add tier and subscription fields
        Schema::table('smart_searches', function (Blueprint $table) {
            // Tier information
            $table->string('tier')->default('starter')->after('is_default');

            // Subscription & expiry tracking
            $table->timestamp('expires_at')->nullable()->after('tier');
            $table->timestamp('purchased_at')->nullable()->after('expires_at');
            $table->string('purchase_reference')->nullable()->after('purchased_at');

            // Match tracking
            $table->unsignedInteger('matches_sent')->default(0)->after('purchase_reference');
            $table->timestamp('last_match_at')->nullable()->after('matches_sent');

            // Status flags
            $table->boolean('is_expired')->default(false)->after('is_active');
            $table->boolean('is_paused')->default(false)->after('is_expired');

            // Indexes for performance
            $table->index(['tier', 'is_active', 'expires_at'], 'smart_searches_tier_active_expires_idx');
            $table->index(['user_id', 'tier'], 'smart_searches_user_tier_idx');
            $table->index(['is_expired', 'expires_at'], 'smart_searches_expired_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smart_searches', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('smart_searches_tier_active_expires_idx');
            $table->dropIndex('smart_searches_user_tier_idx');
            $table->dropIndex('smart_searches_expired_idx');

            // Remove new columns
            $table->dropColumn([
                'tier',
                'expires_at',
                'purchased_at',
                'purchase_reference',
                'matches_sent',
                'last_match_at',
                'is_expired',
                'is_paused',
            ]);
        });

        // Rename table back
        Schema::rename('smart_searches', 'saved_searches');
    }
};
