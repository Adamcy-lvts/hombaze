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
        Schema::table('tenant_invitations', function (Blueprint $table) {
            $table->foreignId('property_owner_id')
                ->nullable()
                ->after('landlord_id')
                ->constrained('property_owners')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_invitations', function (Blueprint $table) {
            $table->dropForeign(['property_owner_id']);
            $table->dropColumn('property_owner_id');
        });
    }
};
