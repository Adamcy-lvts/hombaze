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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('license_number')->nullable()->unique();
            $table->date('license_expiry_date')->nullable();
            $table->text('bio')->nullable();
            $table->string('specializations')->nullable(); // comma separated
            $table->integer('years_experience')->default(0);
            $table->decimal('commission_rate', 5, 2)->default(2.50); // percentage
            $table->json('languages')->nullable(); // spoken languages
            $table->json('service_areas')->nullable(); // array of area IDs they serve
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_properties')->default(0);
            $table->integer('active_listings')->default(0);
            $table->integer('properties_sold')->default(0);
            $table->integer('properties_rented')->default(0);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('accepts_new_clients')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['is_available', 'is_verified']);
            $table->index(['agency_id', 'is_verified']);
            $table->index('rating');
            $table->index('is_featured');
            $table->index('last_active_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
