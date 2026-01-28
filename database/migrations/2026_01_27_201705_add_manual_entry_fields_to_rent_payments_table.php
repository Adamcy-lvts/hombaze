<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if manual columns exist, if not add them
        if (!Schema::hasColumn('rent_payments', 'manual_tenant_name')) {
            Schema::table('rent_payments', function (Blueprint $table) {
                $table->string('manual_tenant_name')->nullable()->after('tenant_id');
                $table->string('manual_tenant_email')->nullable()->after('manual_tenant_name');
                $table->string('manual_tenant_phone')->nullable()->after('manual_tenant_email');
                $table->string('manual_property_title')->nullable()->after('property_id');
                $table->string('manual_property_address')->nullable()->after('manual_property_title');
                $table->boolean('is_manual_entry')->default(false)->after('notes');
            });
        }

        // Clean up orphaned records BEFORE adding foreign keys
        // Set orphaned tenant_id to NULL
        DB::statement('UPDATE rent_payments rp LEFT JOIN tenants t ON rp.tenant_id = t.id SET rp.tenant_id = NULL WHERE rp.tenant_id IS NOT NULL AND t.id IS NULL');
        
        // Set orphaned lease_id to NULL
        DB::statement('UPDATE rent_payments rp LEFT JOIN leases l ON rp.lease_id = l.id SET rp.lease_id = NULL WHERE rp.lease_id IS NOT NULL AND l.id IS NULL');
        
        // Set orphaned property_id to NULL
        DB::statement('UPDATE rent_payments rp LEFT JOIN properties p ON rp.property_id = p.id SET rp.property_id = NULL WHERE rp.property_id IS NOT NULL AND p.id IS NULL');

        // Drop existing foreign key constraints (if they exist)
        try {
            Schema::table('rent_payments', function (Blueprint $table) {
                $table->dropForeign(['lease_id']);
            });
        } catch (\Exception $e) {
            // Foreign key might not exist
        }

        try {
            Schema::table('rent_payments', function (Blueprint $table) {
                $table->dropForeign(['tenant_id']);
            });
        } catch (\Exception $e) {
            // Foreign key might not exist
        }

        try {
            Schema::table('rent_payments', function (Blueprint $table) {
                $table->dropForeign(['property_id']);
            });
        } catch (\Exception $e) {
            // Foreign key might not exist
        }

        // Modify columns to allow NULL using raw SQL
        DB::statement('ALTER TABLE rent_payments MODIFY lease_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE rent_payments MODIFY tenant_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE rent_payments MODIFY property_id BIGINT UNSIGNED NULL');

        // Re-add foreign keys
        Schema::table('rent_payments', function (Blueprint $table) {
            $table->foreign('lease_id')->references('id')->on('leases')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rent_payments', function (Blueprint $table) {
            if (Schema::hasColumn('rent_payments', 'manual_tenant_name')) {
                $table->dropColumn([
                    'manual_tenant_name',
                    'manual_tenant_email',
                    'manual_tenant_phone',
                    'manual_property_title',
                    'manual_property_address',
                    'is_manual_entry',
                ]);
            }
        });
    }
};
