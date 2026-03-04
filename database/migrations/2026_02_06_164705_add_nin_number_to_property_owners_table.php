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
            $table->string('nin_number')->nullable()->after('proof_of_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_owners', function (Blueprint $table) {
            $table->dropColumn('nin_number');
        });
    }
};
