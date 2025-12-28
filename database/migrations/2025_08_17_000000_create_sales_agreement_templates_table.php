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
        Schema::create('sales_agreement_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('terms_and_conditions');
            $table->json('available_variables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['landlord_id', 'is_active']);
            $table->index(['agency_id', 'is_active']);
            $table->index(['agent_id', 'is_active']);
            $table->index(['landlord_id', 'is_default']);
            $table->index(['agency_id', 'is_default']);
            $table->index(['agent_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_agreement_templates');
    }
};
