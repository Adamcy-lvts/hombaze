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
        Schema::table('cities', function (Blueprint $table) {
            // Drop the unique constraint on slug
            $table->dropUnique(['slug']);

            // Add composite unique constraint for slug + state_id
            // This allows same city names in different states
            $table->unique(['slug', 'state_id'], 'cities_slug_state_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('cities_slug_state_unique');

            // Restore the original unique constraint on slug
            $table->unique('slug');
        });
    }
};