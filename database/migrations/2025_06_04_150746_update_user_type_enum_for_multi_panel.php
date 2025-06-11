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
        // Update user_type enum to include super_admin for multi-panel access
        
        // First, drop any existing index
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'user_type')) {
                // Drop index if it exists
                try {
                    $table->dropIndex(['user_type', 'is_active']);
                } catch (Exception $e) {
                    // Index doesn't exist, ignore
                }
                $table->dropColumn('user_type');
            }
        });
        
        // Add the updated user_type column
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['super_admin', 'admin', 'agency_owner', 'agent', 'property_owner', 'tenant'])
                  ->default('tenant')->after('phone');
            $table->index(['user_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'user_type')) {
                try {
                    $table->dropIndex(['user_type', 'is_active']);
                } catch (Exception $e) {
                    // Index doesn't exist, ignore
                }
                $table->dropColumn('user_type');
            }
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['admin', 'agency_owner', 'agent', 'property_owner', 'tenant'])
                  ->default('tenant')->after('phone');
            $table->index(['user_type', 'is_active']);
        });
    }
};
