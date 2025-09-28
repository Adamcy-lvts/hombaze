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
        Schema::table('users', function (Blueprint $table) {
            // Make email nullable for phone-based registration
            $table->string('email')->nullable()->change();

            // Drop unique constraint on email temporarily
            $table->dropUnique(['email']);
        });

        // Add unique constraint that allows nulls but ensures unique non-null emails
        DB::statement('ALTER TABLE users ADD CONSTRAINT users_email_unique UNIQUE (email)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the custom unique constraint
            $table->dropIndex('users_email_unique');

            // Make email required again
            $table->string('email')->nullable(false)->change();

            // Re-add standard unique constraint
            $table->unique('email');
        });
    }
};
