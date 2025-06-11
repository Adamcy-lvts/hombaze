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
            $table->string('phone')->nullable()->after('email');
            $table->enum('user_type', ['admin', 'agency_owner', 'agent', 'property_owner', 'tenant'])
                  ->default('tenant')->after('phone');
            $table->boolean('is_verified')->default(false)->after('user_type');
            $table->timestamp('phone_verified_at')->nullable()->after('is_verified');
            $table->string('avatar')->nullable()->after('phone_verified_at');
            $table->boolean('is_active')->default(true)->after('avatar');
            $table->json('preferences')->nullable()->after('is_active'); // search preferences, notifications etc.
            $table->timestamp('last_login_at')->nullable()->after('preferences');
            
            $table->index(['user_type', 'is_active']);
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['user_type', 'is_active']);
            $table->dropIndex(['phone']);
            $table->dropColumn([
                'phone', 'user_type', 'is_verified', 'phone_verified_at', 
                'avatar', 'is_active', 'preferences', 'last_login_at'
            ]);
        });
    }
};
