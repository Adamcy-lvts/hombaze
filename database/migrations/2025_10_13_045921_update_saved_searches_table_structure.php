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
        Schema::table('saved_searches', function (Blueprint $table) {
            // Add new fields for our enhanced saved searches
            $table->text('description')->nullable()->after('name');
            $table->enum('search_type', ['rent', 'buy', 'shortlet'])->nullable()->after('description');
            $table->json('property_categories')->nullable()->after('search_type');
            $table->json('location_preferences')->nullable()->after('property_categories');
            $table->json('property_subtypes')->nullable()->after('location_preferences');
            $table->decimal('budget_min', 15, 2)->nullable()->after('property_subtypes');
            $table->decimal('budget_max', 15, 2)->nullable()->after('budget_min');
            $table->json('additional_filters')->nullable()->after('budget_max');
            $table->json('notification_settings')->nullable()->after('additional_filters');
            $table->boolean('is_default')->default(false)->after('is_active');

            // Add indexes for new fields
            $table->index(['search_type', 'is_active']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saved_searches', function (Blueprint $table) {
            // Remove the new fields
            $table->dropColumn([
                'description',
                'search_type',
                'property_categories',
                'location_preferences',
                'property_subtypes',
                'budget_min',
                'budget_max',
                'additional_filters',
                'notification_settings',
                'is_default'
            ]);

            // Drop the new indexes
            $table->dropIndex(['search_type', 'is_active']);
            $table->dropIndex(['user_id', 'created_at']);
        });
    }
};
