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
            // Drop old columns
            $table->dropColumn(['house_types']);

            // Add new columns
            $table->json('apartment_subtypes')->nullable()->after('preferred_property_types');
            $table->json('house_subtypes')->nullable()->after('apartment_subtypes');
            $table->json('budgets')->nullable()->after('shop_selected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->dropColumn(['apartment_subtypes', 'house_subtypes', 'budgets']);
            $table->json('house_types')->nullable()->after('preferred_property_types');
        });
    }
};
