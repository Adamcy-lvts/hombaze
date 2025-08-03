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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('profile_completed')->default(false)->after('is_active');
            $table->timestamp('profile_completed_at')->nullable()->after('profile_completed');
            $table->json('profile_completion_steps')->nullable()->after('profile_completed_at');
            $table->integer('profile_completion_percentage')->default(0)->after('profile_completion_steps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_completed',
                'profile_completed_at',
                'profile_completion_steps',
                'profile_completion_percentage'
            ]);
        });
    }
};
