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
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('comment');
            $table->boolean('is_featured')->default(false)->after('is_approved');
            $table->boolean('is_anonymous')->default(false)->after('is_featured');
            $table->unsignedInteger('not_helpful_count')->default(0)->after('helpful_count');
            $table->unsignedInteger('response_count')->default(0)->after('not_helpful_count');
            $table->foreignId('moderated_by')->nullable()->after('reviewer_id')->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable()->after('moderated_by');
            $table->text('moderation_notes')->nullable()->after('moderated_at');
            $table->timestamp('last_activity_at')->nullable()->after('response_count');

            $table->index(['status', 'is_approved']);
            $table->index(['is_featured', 'is_verified']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['moderated_by']);
            $table->dropIndex(['status', 'is_approved']);
            $table->dropIndex(['is_featured', 'is_verified']);
            $table->dropColumn([
                'status',
                'is_featured',
                'is_anonymous',
                'not_helpful_count',
                'response_count',
                'moderated_by',
                'moderated_at',
                'moderation_notes',
                'last_activity_at',
            ]);
        });
    }
};
