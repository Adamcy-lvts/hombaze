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
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        // Make agency_id nullable in roles table (this one should work since it's not in primary key)
        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->unsignedBigInteger('agency_id')->nullable()->change();
        });

        // For model_has_permissions: Drop primary key, make agency_id nullable, create new primary key without agency_id
        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) use ($columnNames, $pivotPermission) {
            // Drop the existing primary key that includes agency_id
            $table->dropPrimary('model_has_permissions_permission_model_type_primary');
        });

        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) use ($columnNames) {
            // Make agency_id nullable
            $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable()->change();
        });

        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) use ($columnNames, $pivotPermission) {
            // Create new primary key without agency_id (since NULLs can't be in primary keys)
            $table->primary([$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                'model_has_permissions_permission_model_type_primary');
            
            // Add unique constraint that includes agency_id but allows for duplicates when agency_id is NULL
            // This constraint will be enforced at application level for NULL values
            $table->unique([$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                'model_has_permissions_unique_with_agency');
        });

        // For model_has_roles: Drop primary key, make agency_id nullable, create new primary key without agency_id
        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($columnNames, $pivotRole) {
            // Drop the existing primary key that includes agency_id
            $table->dropPrimary('model_has_roles_role_model_type_primary');
        });

        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($columnNames) {
            // Make agency_id nullable
            $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable()->change();
        });

        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($columnNames, $pivotRole) {
            // Create new primary key without agency_id (since NULLs can't be in primary keys)
            $table->primary([$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                'model_has_roles_role_model_type_primary');
            
            // Add unique constraint that includes agency_id but allows for duplicates when agency_id is NULL
            // This constraint will be enforced at application level for NULL values
            $table->unique([$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                'model_has_roles_unique_with_agency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        // Reverse changes for model_has_permissions
        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) {
            $table->dropUnique('model_has_permissions_unique_with_agency');
            $table->dropPrimary('model_has_permissions_permission_model_type_primary');
        });

        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) use ($columnNames) {
            $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable(false)->change();
        });

        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) use ($columnNames, $pivotPermission) {
            $table->primary([$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                'model_has_permissions_permission_model_type_primary');
        });

        // Reverse changes for model_has_roles
        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) {
            $table->dropUnique('model_has_roles_unique_with_agency');
            $table->dropPrimary('model_has_roles_role_model_type_primary');
        });

        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($columnNames) {
            $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable(false)->change();
        });

        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) use ($columnNames, $pivotRole) {
            $table->primary([$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                'model_has_roles_role_model_type_primary');
        });

        // Reverse changes for roles table
        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->unsignedBigInteger('agency_id')->nullable(false)->change();
        });
    }
};
