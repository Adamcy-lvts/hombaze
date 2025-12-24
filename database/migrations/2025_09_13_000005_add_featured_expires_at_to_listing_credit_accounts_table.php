<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listing_credit_accounts', function (Blueprint $table) {
            $table->timestamp('featured_expires_at')->nullable()->after('featured_balance');
        });
    }

    public function down(): void
    {
        Schema::table('listing_credit_accounts', function (Blueprint $table) {
            $table->dropColumn('featured_expires_at');
        });
    }
};
