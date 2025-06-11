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
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('search_criteria');
            $table->enum('alert_frequency', ['instant', 'daily', 'weekly', 'monthly'])->default('daily');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_alerted_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index(['is_active', 'last_alerted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};
