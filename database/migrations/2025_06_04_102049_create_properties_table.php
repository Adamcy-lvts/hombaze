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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('listing_type', ['sale', 'rent', 'lease', 'shortlet']);
            $table->enum('status', ['available', 'rented', 'sold', 'off_market', 'under_review'])->default('available');
            
            // Pricing Information
            $table->decimal('price', 15, 2);
            $table->enum('price_period', ['per_month', 'per_year', 'per_night', 'total'])->nullable();
            $table->decimal('service_charge', 10, 2)->nullable();
            $table->decimal('legal_fee', 10, 2)->nullable();
            $table->decimal('agency_fee', 10, 2)->nullable();
            $table->decimal('caution_deposit', 10, 2)->nullable();
            
            // Property Details
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->integer('toilets')->nullable();
            $table->decimal('size_sqm', 8, 2)->nullable();
            $table->integer('parking_spaces')->default(0);
            $table->year('year_built')->nullable();
            $table->enum('furnishing_status', ['furnished', 'semi_furnished', 'unfurnished']);
            
            // Location Information
            $table->text('address');
            $table->string('landmark')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Relationships
            $table->foreignId('property_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_subtype_id')->constrained()->onDelete('cascade');
            $table->foreignId('state_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->foreignId('area_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade'); // Property owner
            $table->foreignId('agent_id')->nullable()->constrained()->onDelete('set null'); // Managing agent
            $table->foreignId('agency_id')->nullable()->constrained()->onDelete('set null'); // Managing agency
            
            // SEO & Marketing
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('virtual_tour_url')->nullable();
            
            // Analytics & Tracking
            $table->integer('view_count')->default(0);
            $table->integer('inquiry_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->timestamp('last_viewed_at')->nullable();
            
            // Management Fields
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamp('featured_until')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('published_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['listing_type', 'status', 'is_published']);
            $table->index(['property_type_id', 'city_id', 'status']);
            $table->index(['price', 'listing_type', 'status']);
            $table->index(['bedrooms', 'bathrooms', 'listing_type']);
            $table->index(['is_featured', 'featured_until']);
            $table->index(['agent_id', 'agency_id']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
