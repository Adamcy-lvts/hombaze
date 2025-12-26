<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add SmartSearch-related fields to property_views table
     */
    public function up(): void
    {
        Schema::table('property_views', function (Blueprint $table) {
            // Add source field to track where the view came from
            $table->string('source')->nullable()->after('platform');

            // Link to SmartSearch match for claim tracking
            $table->foreignId('smart_search_match_id')
                ->nullable()
                ->after('source')
                ->constrained('smart_search_matches')
                ->nullOnDelete();

            // Index for SmartSearch claim detection
            $table->index(['user_id', 'property_id', 'viewed_at'], 'property_views_smartsearch_claim_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_views', function (Blueprint $table) {
            $table->dropIndex('property_views_smartsearch_claim_idx');
            $table->dropConstrainedForeignId('smart_search_match_id');
            $table->dropColumn('source');
        });
    }
};
