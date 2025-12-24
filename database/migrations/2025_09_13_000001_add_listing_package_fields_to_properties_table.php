<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('listing_package')->default('basic')->after('listing_type');
            $table->decimal('listing_fee_amount', 10, 2)->default(0)->after('listing_package');
            $table->string('listing_fee_status')->default('paid')->after('listing_fee_amount');
            $table->timestamp('listing_paid_at')->nullable()->after('listing_fee_status');
            $table->timestamp('listing_expires_at')->nullable()->after('listing_paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'listing_package',
                'listing_fee_amount',
                'listing_fee_status',
                'listing_paid_at',
                'listing_expires_at',
            ]);
        });
    }
};
