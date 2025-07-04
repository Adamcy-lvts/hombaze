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
        Schema::table('properties', function (Blueprint $table) {
            // Add fulltext index for better search performance
            $table->fullText(['title', 'description', 'address'], 'properties_search_fulltext');
            
            // Add compound indexes for common search patterns
            $table->index(['title', 'status', 'is_published'], 'properties_title_status_published_idx');
            $table->index(['city_id', 'property_type_id', 'listing_type'], 'properties_location_type_listing_idx');
            $table->index(['price', 'listing_type', 'status'], 'properties_price_listing_status_idx');
            $table->index(['bedrooms', 'bathrooms', 'listing_type', 'status'], 'properties_rooms_listing_status_idx');
            
            // Location-based search optimization
            $table->index(['state_id', 'city_id', 'area_id'], 'properties_location_hierarchy_idx');
            
            // Feature and status indexes
            $table->index(['is_featured', 'featured_until', 'status'], 'properties_featured_status_idx');
            $table->index(['is_verified', 'verified_at', 'status'], 'properties_verified_status_idx');
            
            // View count for popularity sorting
            $table->index(['view_count', 'is_featured'], 'properties_popularity_idx');
        });

        // Add indexes to related tables for better join performance
        Schema::table('cities', function (Blueprint $table) {
            $table->index(['name', 'state_id'], 'cities_name_state_idx');
        });

        Schema::table('areas', function (Blueprint $table) {
            $table->index(['name', 'city_id'], 'areas_name_city_idx');
        });

        Schema::table('property_types', function (Blueprint $table) {
            $table->index(['name', 'is_active'], 'property_types_name_active_idx');
        });

        Schema::table('property_features', function (Blueprint $table) {
            $table->index(['name', 'is_active'], 'property_features_name_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Drop fulltext index
            $table->dropFullText('properties_search_fulltext');
            
            // Drop compound indexes
            $table->dropIndex('properties_title_status_published_idx');
            $table->dropIndex('properties_location_type_listing_idx');
            $table->dropIndex('properties_price_listing_status_idx');
            $table->dropIndex('properties_rooms_listing_status_idx');
            $table->dropIndex('properties_location_hierarchy_idx');
            $table->dropIndex('properties_featured_status_idx');
            $table->dropIndex('properties_verified_status_idx');
            $table->dropIndex('properties_popularity_idx');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropIndex('cities_name_state_idx');
        });

        Schema::table('areas', function (Blueprint $table) {
            $table->dropIndex('areas_name_city_idx');
        });

        Schema::table('property_types', function (Blueprint $table) {
            $table->dropIndex('property_types_name_active_idx');
        });

        Schema::table('property_features', function (Blueprint $table) {
            $table->dropIndex('property_features_name_active_idx');
        });
    }
};
