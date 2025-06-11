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
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('monthly_rent', 12, 2);
            $table->decimal('security_deposit', 12, 2)->nullable();
            $table->decimal('service_charge', 12, 2)->nullable();
            $table->decimal('legal_fee', 12, 2)->nullable();
            $table->decimal('agency_fee', 12, 2)->nullable();
            $table->decimal('caution_deposit', 12, 2)->nullable();
            $table->string('lease_type')->default('fixed_term');
            $table->string('payment_frequency')->default('monthly');
            $table->string('payment_method')->nullable();
            $table->decimal('late_fee_amount', 10, 2)->nullable();
            $table->integer('grace_period_days')->default(5);
            $table->boolean('renewal_option')->default(false);
            $table->decimal('early_termination_fee', 12, 2)->nullable();
            $table->longText('terms_and_conditions')->nullable();
            $table->text('special_clauses')->nullable();
            $table->string('status')->default('draft');
            $table->date('signed_date')->nullable();
            $table->date('move_in_date')->nullable();
            $table->date('move_out_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['landlord_id', 'status']);
            $table->index(['property_id', 'status']);
            $table->index(['tenant_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
