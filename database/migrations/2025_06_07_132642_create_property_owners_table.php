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
        Schema::create('property_owners', function (Blueprint $table) {
            $table->id();
            
            // Owner type and basic info
            $table->enum('type', ['individual', 'company', 'trust', 'government'])->default('individual');
            $table->string('first_name')->nullable(); // For individuals
            $table->string('last_name')->nullable(); // For individuals
            $table->string('company_name')->nullable(); // For companies/organizations
            
            // Contact information
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('Nigeria');
            
            // Business/Legal information
            $table->string('tax_id')->nullable(); // Tax identification number
            
            // Optional link to platform user (if owner has an account)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Additional information
            $table->text('notes')->nullable(); // For agents to add internal notes
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['type', 'is_active']);
            $table->index(['email']);
            $table->index(['first_name', 'last_name']);
            $table->index(['company_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_owners');
    }
};
