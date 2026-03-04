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
        Schema::table('properties', function (Blueprint $table) {
            // Moderation status: pending (awaiting review), approved, rejected
            // Default to 'approved' so existing properties aren't hidden
            $table->string('moderation_status', 20)->default('approved')->after('is_verified');
            $table->timestamp('moderated_at')->nullable()->after('moderation_status');
            $table->unsignedBigInteger('moderated_by')->nullable()->after('moderated_at');
            $table->text('moderation_notes')->nullable()->after('moderated_by');
            
            // Add index for efficient querying of pending properties
            $table->index('moderation_status');
            
            // Foreign key for moderated_by (admin user who moderated)
            $table->foreign('moderated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['moderated_by']);
            $table->dropIndex(['moderation_status']);
            $table->dropColumn(['moderation_status', 'moderated_at', 'moderated_by', 'moderation_notes']);
        });
    }
};
