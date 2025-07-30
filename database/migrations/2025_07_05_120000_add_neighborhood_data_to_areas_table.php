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
        Schema::table('areas', function (Blueprint $table) {
            // Neighborhood overview data
            $table->json('education_facilities')->nullable(); // schools, universities with distances
            $table->json('healthcare_facilities')->nullable(); // hospitals, clinics with distances  
            $table->json('shopping_facilities')->nullable(); // malls, markets with distances
            $table->json('transport_facilities')->nullable(); // bus stops, stations with distances
            
            // Security and safety
            $table->decimal('security_rating', 3, 1)->nullable(); // 0.0 to 10.0
            $table->json('security_features')->nullable(); // 24/7 security, CCTV, gated, etc
            $table->decimal('crime_rate', 5, 2)->nullable(); // per 1000 residents
            
            // Area statistics
            $table->integer('population')->nullable();
            $table->decimal('average_rent', 10, 2)->nullable(); // average rent in area
            $table->decimal('walkability_score', 3, 1)->nullable(); // 0.0 to 10.0
            $table->json('lifestyle_tags')->nullable(); // quiet, bustling, family-friendly, etc
            
            // Infrastructure
            $table->json('utilities')->nullable(); // power, water, internet reliability
            $table->json('road_condition')->nullable(); // paved, good, needs_work
            
            // Add indexes for better performance
            $table->index('security_rating');
            $table->index('average_rent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropIndex(['security_rating']);
            $table->dropIndex(['average_rent']);
            
            $table->dropColumn([
                'education_facilities',
                'healthcare_facilities', 
                'shopping_facilities',
                'transport_facilities',
                'security_rating',
                'security_features',
                'crime_rate',
                'population',
                'average_rent',
                'walkability_score',
                'lifestyle_tags',
                'utilities',
                'road_condition'
            ]);
        });
    }
};