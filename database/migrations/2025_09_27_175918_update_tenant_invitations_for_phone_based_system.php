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
        Schema::table('tenant_invitations', function (Blueprint $table) {
            // Replace email with phone
            $table->string('phone')->after('id')->comment('Tenant phone number in format +234xxxxxxxxxx');
            $table->dropColumn('email');

            // Add communication tracking
            $table->timestamp('link_copied_at')->nullable()->comment('When invitation link was last copied');
            $table->integer('link_copy_count')->default(0)->comment('Number of times link was copied');
            $table->json('sent_via')->nullable()->comment('Track where invitation was sent: whatsapp, sms, other');

            // Update indexes for phone-based queries
            $table->dropIndex(['email', 'status']);
            $table->index(['phone', 'status']);
            $table->index(['phone', 'landlord_id']);
            $table->index(['phone', 'agent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_invitations', function (Blueprint $table) {
            // Restore email field
            $table->string('email')->after('id');
            $table->dropColumn(['phone', 'link_copied_at', 'link_copy_count', 'sent_via']);

            // Restore email indexes
            $table->dropIndex(['phone', 'status']);
            $table->dropIndex(['phone', 'landlord_id']);
            $table->dropIndex(['phone', 'agent_id']);
            $table->index(['email', 'status']);
        });
    }
};
