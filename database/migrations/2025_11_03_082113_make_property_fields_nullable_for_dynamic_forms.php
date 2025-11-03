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
        // First, add the new fields
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('price_negotiable')->default(false)->after('price_period');
            $table->string('contact_phone')->nullable()->after('virtual_tour_url');
            $table->string('contact_email')->nullable()->after('contact_phone');
            $table->text('viewing_instructions')->nullable()->after('contact_email');
            $table->boolean('is_active')->default(true)->after('is_published');
        });

        // Then, modify existing columns to be nullable in separate schema calls
        Schema::table('properties', function (Blueprint $table) {
            $table->integer('bedrooms')->nullable()->change();
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->integer('bathrooms')->nullable()->change();
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->enum('furnishing_status', ['furnished', 'semi_furnished', 'unfurnished'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Revert the nullable changes (note: this may cause data loss if nulls exist)
            $table->integer('bedrooms')->nullable(false)->change();
            $table->integer('bathrooms')->nullable(false)->change();
            $table->enum('furnishing_status', ['furnished', 'semi_furnished', 'unfurnished'])->nullable(false)->change();

            // Remove the added fields
            $table->dropColumn(['price_negotiable', 'contact_phone', 'contact_email', 'viewing_instructions', 'is_active']);
        });
    }
};