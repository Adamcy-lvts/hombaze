<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            UPDATE properties p
            LEFT JOIN areas a ON a.id = p.area_id
            SET p.area_id = NULL
            WHERE p.area_id IS NOT NULL AND a.id IS NULL
        ');
    }

    public function down(): void
    {
        // No-op: data cleanup migration.
    }
};
