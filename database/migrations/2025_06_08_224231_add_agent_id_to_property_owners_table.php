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
            // Add agent relationship for independent agents
            // This will allow property owners to be linked to either an agency OR an independent agent
            $table->foreignId('agent_id')->nullable()->after('agency_id')->constrained('agents')->onDelete('cascade');
            
            // Add index for performance
            $table->index(['agent_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_owners', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropIndex(['agent_id', 'is_active']);
            $table->dropColumn('agent_id');
        });
    }
};
