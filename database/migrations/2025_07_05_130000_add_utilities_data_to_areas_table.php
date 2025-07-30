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
            // Add specific utilities fields if not already present
            if (!Schema::hasColumn('areas', 'electricity_supply')) {
                $table->json('electricity_supply')->nullable(); // quality, availability, reliability
                $table->json('water_supply')->nullable(); // quality, availability, source
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            if (Schema::hasColumn('areas', 'electricity_supply')) {
                $table->dropColumn(['electricity_supply', 'water_supply']);
            }
        });
    }
};