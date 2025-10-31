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
        Schema::create('property_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->enum('interaction_type', [
                'view', 'inquiry', 'viewing_scheduled', 'viewing_completed',
                'viewing_cancelled', 'contact_agent', 'save_property',
                'share_property', 'report_property'
            ]);
            $table->decimal('interaction_score', 5, 2)->default(0); // Calculated score for this interaction
            $table->json('metadata')->nullable(); // Additional context data
            $table->string('source')->nullable(); // web, mobile, email, etc.
            $table->string('session_id')->nullable(); // To track session-based interactions
            $table->timestamp('interaction_date')->useCurrent();
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'interaction_date']);
            $table->index(['property_id', 'interaction_type']);
            $table->index(['interaction_type', 'interaction_date']);
            $table->unique(['user_id', 'property_id', 'interaction_type', 'interaction_date'], 'unique_user_property_interaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_interactions');
    }
};
