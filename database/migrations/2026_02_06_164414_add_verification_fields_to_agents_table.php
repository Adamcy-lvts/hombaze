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
        Schema::table('agents', function (Blueprint $table) {
            $table->string('nin_number')->nullable()->after('license_expiry_date');
            $table->enum('verification_status', ['pending', 'submitted', 'verified', 'rejected'])
                ->default('pending')
                ->after('nin_number');
            $table->timestamp('verification_submitted_at')->nullable()->after('verification_status');
            $table->text('verification_notes')->nullable()->after('verification_submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn([
                'nin_number',
                'verification_status',
                'verification_submitted_at',
                'verification_notes',
            ]);
        });
    }
};
