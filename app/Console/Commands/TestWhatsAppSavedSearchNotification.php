<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SavedSearch;
use App\Models\Property;
use App\Notifications\SavedSearchMatch;
use Illuminate\Support\Facades\Notification;

class TestWhatsAppSavedSearchNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:whatsapp-saved-search {phone} {--user-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WhatsApp saved search notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');
        $userId = $this->option('user-id');

        $this->info("Testing WhatsApp saved search notification to: {$phone}");

        // Find or create a test user
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }
        } else {
            $user = User::where('phone', $phone)->first();
            if (!$user) {
                $this->info("Creating test user with phone: {$phone}");
                $user = User::factory()->create([
                    'name' => 'Test User',
                    'email' => 'test+' . time() . '@homebaze.com',
                    'phone' => $phone,
                ]);
            }
        }

        // Update user phone if different
        if ($user->phone !== $phone) {
            $user->update(['phone' => $phone]);
            $this->info("Updated user phone to: {$phone}");
        }

        // Create a test saved search with WhatsApp alerts enabled
        $savedSearch = SavedSearch::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Test WhatsApp Search'],
            [
                'description' => 'Test search for WhatsApp notifications',
                'search_type' => 'rent',
                'search_criteria' => [],
                'notification_settings' => [
                    'email_alerts' => false,
                    'whatsapp_alerts' => true,
                    'sms_alerts' => false
                ],
                'is_active' => true
            ]
        );

        $this->info("Using saved search: {$savedSearch->name} (ID: {$savedSearch->id})");

        // Get some sample properties
        $properties = Property::with(['area.city', 'propertySubtype'])
            ->take(2)
            ->get();

        if ($properties->isEmpty()) {
            $this->error('No properties found for testing.');
            return 1;
        }

        $this->info("Found {$properties->count()} properties for testing");

        try {
            // Send the notification
            $user->notify(new SavedSearchMatch($savedSearch, $properties, 0.85));

            $this->info("✅ WhatsApp notification sent successfully!");
            $this->info("Check the phone {$phone} for the WhatsApp message.");

        } catch (Exception $e) {
            $this->error("❌ Failed to send WhatsApp notification:");
            $this->error($e->getMessage());
            return 1;
        }

        return 0;
    }
}
