<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SavedSearch;
use App\Models\Property;
use App\Notifications\SavedSearchMatch;
use Illuminate\Support\Facades\Notification;

class TestEmailSavedSearchNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-saved-search {email} {--user-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email saved search notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $userId = $this->option('user-id');

        $this->info("Testing email saved search notification to: {$email}");

        // Find or create a test user
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }
        } else {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->info("Creating test user with email: {$email}");
                $user = User::factory()->create([
                    'name' => 'Test User',
                    'email' => $email,
                ]);
            }
        }

        // Update user email if different
        if ($user->email !== $email) {
            $user->update(['email' => $email]);
            $this->info("Updated user email to: {$email}");
        }

        // Create a test saved search with email alerts enabled
        $savedSearch = SavedSearch::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Test Email Search'],
            [
                'description' => 'Test search for email notifications',
                'search_type' => 'rent',
                'search_criteria' => [],
                'notification_settings' => [
                    'email_alerts' => true,
                    'whatsapp_alerts' => false,
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

            $this->info("✅ Email notification sent successfully!");
            $this->info("Check the email {$email} for the notification message.");

        } catch (\Exception $e) {
            $this->error("❌ Failed to send email notification:");
            $this->error($e->getMessage());
            return 1;
        }

        return 0;
    }
}