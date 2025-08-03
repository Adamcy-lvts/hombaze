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
        Schema::create('lease_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('terms_and_conditions');
            $table->string('default_payment_frequency')->default('annually');
            $table->decimal('default_security_deposit', 10, 2)->nullable();
            $table->decimal('default_service_charge', 10, 2)->nullable();
            $table->decimal('default_legal_fee', 10, 2)->nullable();
            $table->decimal('default_agency_fee', 10, 2)->nullable();
            $table->decimal('default_caution_deposit', 10, 2)->nullable();
            $table->integer('default_grace_period_days')->default(0);
            $table->boolean('default_renewal_option')->default(true);
            $table->json('available_variables')->nullable(); // Store list of variables used in template
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false); // Mark if this is the default template
            $table->timestamps();

            $table->index(['landlord_id', 'is_active']);
            $table->index(['landlord_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_templates');
    }
};
