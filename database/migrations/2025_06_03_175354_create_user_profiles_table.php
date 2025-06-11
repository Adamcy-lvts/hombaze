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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('bio')->nullable();
            $table->string('occupation')->nullable();
            $table->decimal('annual_income', 15, 2)->nullable();
            
            // Location Information
            $table->foreignId('state_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            
            // Contact Information
            $table->string('alternate_phone')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            
            // Property Preferences (for tenants/buyers)
            $table->decimal('budget_min', 15, 2)->nullable();
            $table->decimal('budget_max', 15, 2)->nullable();
            $table->json('preferred_property_types')->nullable(); // array of property type IDs
            $table->json('preferred_locations')->nullable(); // array of area/city IDs
            $table->json('preferred_features')->nullable(); // array of feature IDs
            $table->integer('preferred_bedrooms_min')->nullable();
            $table->integer('preferred_bedrooms_max')->nullable();
            
            // Identity Verification
            $table->enum('id_type', ['nin', 'bvn', 'drivers_license', 'voters_card', 'passport'])->nullable();
            $table->string('id_number')->nullable();
            $table->boolean('is_id_verified')->default(false);
            $table->timestamp('id_verified_at')->nullable();
            
            // Social Media Links
            $table->string('linkedin_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('website_url')->nullable();
            
            $table->boolean('is_complete')->default(false);
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['state_id', 'city_id', 'area_id']);
            $table->index(['budget_min', 'budget_max']);
            $table->index('is_id_verified');
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
