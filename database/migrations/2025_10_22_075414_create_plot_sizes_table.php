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
        Schema::create('plot_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Quarter Plot", "Half Plot", "1 Acre"
            $table->string('description')->nullable(); // e.g., "15x30m", "Standard Nigerian plot size"
            $table->decimal('size_value', 10, 2); // The numeric value (e.g., 0.25, 1, 2)
            $table->string('unit'); // sqm, acre, hectare, plot
            $table->decimal('size_in_sqm', 12, 2); // Always store equivalent in square meters for consistency
            $table->string('display_text')->nullable(); // e.g., "450 sqm", "1 Acre (4,047 sqm)"
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->index('unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plot_sizes');
    }
};
