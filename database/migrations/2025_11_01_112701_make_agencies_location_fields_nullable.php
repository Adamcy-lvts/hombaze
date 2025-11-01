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
        Schema::table('agencies', function (Blueprint $table) {
            // Make state_id and city_id nullable to allow basic agency creation
            // Validation can be enforced at application level when needed
            $table->foreignId('state_id')->nullable()->change();
            $table->foreignId('city_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            // Revert back to required fields
            $table->foreignId('state_id')->nullable(false)->change();
            $table->foreignId('city_id')->nullable(false)->change();
        });
    }
};