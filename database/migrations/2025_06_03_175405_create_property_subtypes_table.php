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
        Schema::create('property_subtypes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('property_type_id')->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('typical_features')->nullable(); // common features for this subtype
            $table->decimal('typical_price_min', 15, 2)->nullable(); // typical price range
            $table->decimal('typical_price_max', 15, 2)->nullable();
            $table->timestamps();

            $table->index(['property_type_id', 'is_active']);
            $table->index(['is_active', 'sort_order']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_subtypes');
    }
};
