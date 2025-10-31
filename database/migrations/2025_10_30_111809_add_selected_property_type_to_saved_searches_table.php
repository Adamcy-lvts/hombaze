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
            // Add selected_property_type field after search_type
            $table->unsignedBigInteger('selected_property_type')->nullable()->after('search_type');

            // Add foreign key constraint to property_types table
            $table->foreign('selected_property_type')->references('id')->on('property_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saved_searches', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['selected_property_type']);

            // Then drop the column
            $table->dropColumn('selected_property_type');
        });
    }
};
