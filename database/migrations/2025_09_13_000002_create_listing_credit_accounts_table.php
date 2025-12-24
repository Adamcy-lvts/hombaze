<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_credit_accounts', function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->unsignedInteger('listing_balance')->default(0);
            $table->unsignedInteger('featured_balance')->default(0);
            $table->timestamps();

            $table->unique(['owner_type', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_credit_accounts');
    }
};
