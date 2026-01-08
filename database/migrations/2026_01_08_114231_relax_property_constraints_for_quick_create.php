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
            $table->text('description')->nullable()->change();
            $table->integer('bathrooms')->nullable()->change();
            $table->enum('furnishing_status', ['furnished', 'semi_furnished', 'unfurnished'])->nullable()->change();
            $table->foreignId('property_subtype_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // We can't easily revert nullable to not-nullable without knowing data is safe,
            // but we will define the strict schema back.
            // Note: This might fail if there are null values.
            
            // For Safety in dev development, we can attempt it, or leave it. 
            // Better to attempt proper rollback definition.
             $table->text('description')->nullable(false)->change();
             $table->integer('bathrooms')->nullable(false)->change();
             // For enums/foreign keys, it's tricker to revert exact state without potential data issues.
        });
    }
};
