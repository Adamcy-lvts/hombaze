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
        Schema::table('rent_payments', function (Blueprint $table) {
            // Custom dates for receipts not linked to a lease
            if (!Schema::hasColumn('rent_payments', 'custom_start_date')) {
                $table->date('custom_start_date')->nullable()->after('payment_for_period');
            }
            if (!Schema::hasColumn('rent_payments', 'custom_end_date')) {
                $table->date('custom_end_date')->nullable()->after('custom_start_date');
            }
            // Payment for description (separate from payment_for_period)
            if (!Schema::hasColumn('rent_payments', 'payment_for')) {
                $table->string('payment_for')->nullable()->after('custom_end_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rent_payments', function (Blueprint $table) {
            if (Schema::hasColumn('rent_payments', 'custom_start_date')) {
                $table->dropColumn('custom_start_date');
            }
            if (Schema::hasColumn('rent_payments', 'custom_end_date')) {
                $table->dropColumn('custom_end_date');
            }
            if (Schema::hasColumn('rent_payments', 'payment_for')) {
                $table->dropColumn('payment_for');
            }
        });
    }
};
