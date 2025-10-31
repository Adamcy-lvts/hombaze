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
        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->json('house_types')->nullable()->after('preferred_property_types');
            $table->json('land_sizes')->nullable()->after('house_types');
            $table->boolean('shop_selected')->default(false)->after('land_sizes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->dropColumn(['house_types', 'land_sizes', 'shop_selected']);
        });
    }
};
