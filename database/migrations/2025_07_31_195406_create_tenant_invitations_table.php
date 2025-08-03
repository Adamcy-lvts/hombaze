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
        Schema::create('tenant_invitations', function (Blueprint $table) {
            $table->id();
            
            // Invitation details
            $table->string('email');
            $table->string('token')->unique();
            $table->enum('status', ['pending', 'accepted', 'expired', 'cancelled'])->default('pending');
            
            // Landlord information
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('set null');
            
            // Optional message from landlord
            $table->text('message')->nullable();
            
            // Invitation metadata
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->ipAddress('invited_from_ip')->nullable();
            $table->ipAddress('accepted_from_ip')->nullable();
            
            // Tenant information (populated when accepted)
            $table->foreignId('tenant_user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['email', 'status']);
            $table->index(['landlord_id', 'status']);
            $table->index(['token']);
            $table->index(['expires_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_invitations');
    }
};
