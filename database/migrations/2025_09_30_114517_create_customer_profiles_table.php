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
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Customer preferences
            $table->json('interested_in')->nullable()->comment('Array of interests: buying, renting, shortlet');
            $table->decimal('budget_min', 15, 2)->nullable()->comment('Minimum budget in Naira');
            $table->decimal('budget_max', 15, 2)->nullable()->comment('Maximum budget in Naira');
            $table->json('preferred_property_types')->nullable()->comment('Array of preferred property type IDs');
            $table->json('preferred_locations')->nullable()->comment('Array of preferred city/area IDs');

            // Notification settings
            $table->json('notification_preferences')->nullable()->comment('Notification settings object');
            $table->boolean('email_alerts')->default(true)->comment('Receive email alerts for new properties');
            $table->boolean('sms_alerts')->default(false)->comment('Receive SMS alerts for new properties');
            $table->boolean('whatsapp_alerts')->default(false)->comment('Receive WhatsApp alerts for new properties');

            // Search and recommendation data
            $table->json('search_history')->nullable()->comment('Recent search terms and filters');
            $table->json('viewed_properties')->nullable()->comment('Recently viewed property IDs');
            $table->timestamp('last_search_at')->nullable()->comment('Last property search timestamp');

            $table->timestamps();

            // Indexes for performance
            $table->unique('user_id');
            $table->index(['budget_min', 'budget_max']);
            $table->index(['email_alerts', 'sms_alerts']);
            $table->index('last_search_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
