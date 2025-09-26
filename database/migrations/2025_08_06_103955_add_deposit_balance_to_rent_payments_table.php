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
            $table->decimal('deposit', 10, 2)->default(0)->after('discount');
            $table->decimal('balance_due', 10, 2)->default(0)->after('deposit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rent_payments', function (Blueprint $table) {
            $table->dropColumn(['deposit', 'balance_due']);
        });
    }
};
