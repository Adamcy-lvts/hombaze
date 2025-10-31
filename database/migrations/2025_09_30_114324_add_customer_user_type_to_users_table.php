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
            // Drop existing index first
            try {
                $table->dropIndex(['user_type', 'is_active']);
            } catch (\Exception $e) {
                // Index might not exist, continue
            }

            // Drop the existing user_type column
            $table->dropColumn('user_type');
        });

        Schema::table('users', function (Blueprint $table) {
            // Add the updated user_type enum with 'customer' included
            $table->enum('user_type', [
                'super_admin',
                'admin',
                'agency_owner',
                'agent',
                'property_owner',
                'tenant',
                'customer'
            ])->default('customer')->after('phone');

            // Re-add the index
            $table->index(['user_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the index and column
            $table->dropIndex(['user_type', 'is_active']);
            $table->dropColumn('user_type');
        });

        Schema::table('users', function (Blueprint $table) {
            // Restore the original user_type enum without 'customer'
            $table->enum('user_type', [
                'super_admin',
                'admin',
                'agency_owner',
                'agent',
                'property_owner',
                'tenant'
            ])->default('tenant')->after('phone');

            // Re-add the index
            $table->index(['user_type', 'is_active']);
        });
    }
};
