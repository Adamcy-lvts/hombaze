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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->morphs('reviewable'); // reviewable_type, reviewable_id with automatic index
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->integer('rating')->unsigned()->comment('1-5 star rating');
            $table->string('title')->nullable();
            $table->text('comment');
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->timestamps();

            // Additional indexes for performance (morphs() already creates reviewable index)
            $table->index(['reviewer_id', 'rating']);
            $table->index(['is_approved', 'rating']);
            $table->index(['is_verified', 'is_approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
