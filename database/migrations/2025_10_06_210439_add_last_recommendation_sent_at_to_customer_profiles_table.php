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
            $table->timestamp('last_recommendation_sent_at')->nullable()->comment('Last time property recommendations were sent');
            $table->index('last_recommendation_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->dropIndex(['last_recommendation_sent_at']);
            $table->dropColumn('last_recommendation_sent_at');
        });
    }
};
