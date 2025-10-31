<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Property;
use App\Notifications\PropertyRecommendation;
use App\Services\UserBehaviorAnalyzer;
use Carbon\Carbon;

class SendPropertyRecommendations extends Command
{
    protected $signature = 'properties:send-recommendations
                            {--force : Send recommendations even if recently sent}
                            {--user= : Send to specific user ID}
                            {--algorithm=enhanced : Algorithm to use (legacy, enhanced)}';

    protected $description = 'Send property recommendations to customers based on their preferences and behavior';

    public function handle()
    {
        $this->info('Starting property recommendation process...');

        $query = User::whereHas('customerProfile', function ($q) {
            $q->whereNotNull('preferred_property_types')
                ->orWhereNotNull('preferred_locations')
                ->orWhereNotNull('interested_in');
        })
        ->where('user_type', 'customer')
        ->whereHas('customerProfile', function ($q) {
            $q->where('email_alerts', true);
        });

        // Filter by specific user if provided
        if ($this->option('user')) {
            $query->where('id', $this->option('user'));
        }

        // Don't send if recommendations were sent in the last 24 hours (unless forced)
        if (!$this->option('force')) {
            $query->whereHas('customerProfile', function ($q) {
                $q->where(function ($subQuery) {
                    $subQuery->whereNull('last_recommendation_sent_at')
                        ->orWhere('last_recommendation_sent_at', '<', Carbon::now()->subDay());
                });
            });
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->info('No users eligible for recommendations.');
            return Command::SUCCESS;
        }

        $this->info("Found {$users->count()} users eligible for recommendations.");

        $sentCount = 0;
        $errorCount = 0;

        $algorithm = $this->option('algorithm');
        $this->info("Using {$algorithm} algorithm for recommendations.");

        foreach ($users as $user) {
            try {
                $recommendations = $this->getRecommendationsForUser($user, $algorithm);

                if ($recommendations->count() > 0) {
                    // Send notification
                    $user->notify(new PropertyRecommendation($recommendations));

                    // Update last recommendation sent timestamp
                    $user->customerProfile->update([
                        'last_recommendation_sent_at' => Carbon::now()
                    ]);

                    $sentCount++;
                    $this->info("✓ Sent {$recommendations->count()} recommendations to {$user->name}");

                    // Log recommendation details in enhanced mode
                    if ($algorithm === 'enhanced' && $this->option('verbose')) {
                        foreach ($recommendations as $property) {
                            $score = $property->recommendation_score ?? 'N/A';
                            $this->line("  - {$property->title} (Score: {$score})");
                        }
                    }
                } else {
                    $this->warn("- No new recommendations for {$user->name}");
                }

            } catch (\Exception $e) {
                $errorCount++;
                $this->error("✗ Failed to send recommendations to {$user->name}: {$e->getMessage()}");

                if ($this->option('verbose')) {
                    $this->error("  Error details: " . $e->getTraceAsString());
                }
            }
        }

        $this->info("\nRecommendation process completed:");
        $this->info("- Successfully sent: {$sentCount}");
        $this->info("- Errors: {$errorCount}");

        return Command::SUCCESS;
    }

    protected function getRecommendationsForUser(User $user, string $algorithm = 'enhanced')
    {
        if ($algorithm === 'enhanced') {
            return $this->getEnhancedRecommendations($user);
        }

        return $this->getLegacyRecommendations($user);
    }

    protected function getEnhancedRecommendations(User $user)
    {
        $analyzer = new UserBehaviorAnalyzer();
        return $analyzer->getRecommendationsForUser($user, 5);
    }

    protected function getLegacyRecommendations(User $user)
    {
        $profile = $user->customerProfile;

        $query = Property::with(['propertyType', 'area.city.state', 'media'])
            ->where('status', 'active')
            ->where('created_at', '>=', Carbon::now()->subDays(7)); // Only properties from last 7 days

        // Exclude properties the user has already viewed
        $viewedProperties = $profile->viewed_properties ?? [];
        if (!empty($viewedProperties)) {
            $query->whereNotIn('id', $viewedProperties);
        }

        // Apply location filtering
        $locations = $profile->preferred_locations ?? [];
        if (isset($locations['area'])) {
            $query->where('area_id', $locations['area']);
        } elseif (isset($locations['city'])) {
            $query->whereHas('area', function($q) use ($locations) {
                $q->where('city_id', $locations['city']);
            });
        } elseif (isset($locations['state'])) {
            $query->whereHas('area.city', function($q) use ($locations) {
                $q->where('state_id', $locations['state']);
            });
        }

        // Apply interest filtering
        $interests = $profile->interested_in ?? [];
        if (!empty($interests)) {
            $query->where(function($q) use ($interests) {
                if (in_array('renting', $interests)) {
                    $q->orWhere('listing_type', 'rent');
                }
                if (in_array('buying', $interests)) {
                    $q->orWhere('listing_type', 'sale');
                }
                if (in_array('shortlet', $interests)) {
                    $q->orWhere('listing_type', 'shortlet');
                }
            });
        }

        // Apply property type filtering
        $preferredTypes = $profile->preferred_property_types;
        if (!empty($preferredTypes) && is_array($preferredTypes)) {
            $query->whereIn('property_type_id', $preferredTypes);
        }

        // Apply budget filtering
        if ($profile->budget_min) {
            $query->where('price', '>=', $profile->budget_min);
        }
        if ($profile->budget_max) {
            $query->where('price', '<=', $profile->budget_max);
        }

        return $query->orderBy('created_at', 'desc')->take(5)->get();
    }
}