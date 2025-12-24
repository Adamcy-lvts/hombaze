<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('listing_credit_transactions')) {
            return;
        }

        Schema::create('listing_credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_credit_account_id')
                ->constrained('listing_credit_accounts')
                ->cascadeOnDelete();
            $table->foreignId('property_id')
                ->nullable()
                ->constrained('properties')
                ->nullOnDelete();
            $table->string('package')->nullable();
            $table->string('credit_type');
            $table->integer('credits');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['listing_credit_account_id', 'created_at'], 'listing_credit_acct_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_credit_transactions');
    }
};
