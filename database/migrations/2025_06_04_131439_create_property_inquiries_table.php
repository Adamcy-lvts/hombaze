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
        Schema::create('property_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inquirer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('inquirer_name');
            $table->string('inquirer_email');
            $table->string('inquirer_phone')->nullable();
            $table->text('message');
            $table->date('preferred_viewing_date')->nullable();
            $table->enum('status', ['new', 'contacted', 'scheduled', 'viewed', 'closed'])->default('new');
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('response_message')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['property_id', 'status']);
            $table->index(['inquirer_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_inquiries');
    }
};
