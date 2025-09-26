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
        Schema::create('lease_renewal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('leases')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('landlord_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->date('requested_start_date');
            $table->date('requested_end_date');
            $table->decimal('requested_monthly_rent', 10, 2)->nullable();
            $table->text('tenant_message')->nullable();
            $table->text('response_message')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_renewal_requests');
    }
};
