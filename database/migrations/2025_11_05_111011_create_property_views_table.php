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
        Schema::table('property_views', function (Blueprint $table) {
            // Add new columns for enhanced tracking
            $table->string('ip_address_hash', 64)->nullable()->after('ip_address');
            $table->string('user_agent_hash', 64)->nullable()->after('user_agent');
            $table->string('fingerprint', 64)->nullable()->after('user_agent_hash');

            // Add index for fingerprint-based duplicate prevention
            $table->index(['fingerprint', 'viewed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_views', function (Blueprint $table) {
            $table->dropIndex(['fingerprint', 'viewed_at']);
            $table->dropColumn(['ip_address_hash', 'user_agent_hash', 'fingerprint']);
        });
    }
};
