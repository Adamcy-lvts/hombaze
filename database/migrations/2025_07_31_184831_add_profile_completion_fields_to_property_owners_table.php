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
        Schema::table('property_owners', function (Blueprint $table) {
            // Add missing geographic relationships
            $table->foreignId('state_id')->nullable()->after('country')->constrained()->onDelete('set null');
            $table->foreignId('city_id')->nullable()->after('state_id')->constrained()->onDelete('set null');
            $table->foreignId('area_id')->nullable()->after('city_id')->constrained()->onDelete('set null');
            
            // Add personal information fields
            $table->date('date_of_birth')->nullable()->after('company_name');
            $table->string('preferred_communication')->default('email')->after('phone');
            
            // Add file upload fields for documents and photos
            $table->string('profile_photo')->nullable()->after('preferred_communication');
            $table->string('id_document')->nullable()->after('profile_photo');
            $table->string('proof_of_address')->nullable()->after('id_document');
            
            // Add verification status
            $table->boolean('is_verified')->default(false)->after('is_active');
            $table->timestamp('verified_at')->nullable()->after('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_owners', function (Blueprint $table) {
            $table->dropColumn([
                'state_id',
                'city_id', 
                'area_id',
                'date_of_birth',
                'preferred_communication',
                'profile_photo',
                'id_document',
                'proof_of_address',
                'is_verified',
                'verified_at'
            ]);
        });
    }
};
