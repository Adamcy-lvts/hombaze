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

        // Rename team_id to agency_id in roles table
        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->renameColumn('team_id', 'agency_id');
        });

        // Rename team_id to agency_id in model_has_permissions table
        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) {
            $table->renameColumn('team_id', 'agency_id');
        });

        // Rename team_id to agency_id in model_has_roles table  
        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) {
            $table->renameColumn('team_id', 'agency_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        // Rename agency_id back to team_id in roles table
        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->renameColumn('agency_id', 'team_id');
        });

        // Rename agency_id back to team_id in model_has_permissions table
        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) {
            $table->renameColumn('agency_id', 'team_id');
        });

        // Rename agency_id back to team_id in model_has_roles table
        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) {
            $table->renameColumn('agency_id', 'team_id');
        });
    }
};
