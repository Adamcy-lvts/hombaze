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
        Schema::table('properties', function (Blueprint $table) {
            // Add foreign key reference to plot_sizes table
            $table->foreignId('plot_size_id')->nullable()->constrained('plot_sizes')->onDelete('set null');

            // Add custom plot size fields for properties that don't use predefined sizes
            $table->decimal('custom_plot_size', 10, 2)->nullable();
            $table->string('custom_plot_unit')->nullable();

            // Add indexes for better query performance
            $table->index('plot_size_id');
            $table->index(['custom_plot_size', 'custom_plot_unit']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['plot_size_id']);
            $table->dropIndex(['plot_size_id']);
            $table->dropIndex(['custom_plot_size', 'custom_plot_unit']);
            $table->dropColumn(['plot_size_id', 'custom_plot_size', 'custom_plot_unit']);
        });
    }
};
