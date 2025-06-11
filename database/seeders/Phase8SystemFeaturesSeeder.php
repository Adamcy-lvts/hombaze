<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\PropertyView;
use App\Models\User;
use Carbon\Carbon;

class Phase8SystemFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Phase 8: System Features Seeding...');

        // Get existing data
        $properties = Property::all();
        $users = User::all();

        if ($properties->isEmpty()) {
            $this->command->warn('⚠️ Properties not found. Please run previous phase seeders first.');
            return;
        }

        // 1. Property Views Analytics
        $this->seedPropertyViews($properties, $users);

        $this->command->info('✅ Phase 8 System Features seeding completed successfully!');
    }

    private function seedPropertyViews($properties, $users): void
    {
        $this->command->info('📊 Seeding Property Views Analytics...');

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Android 10; Mobile; rv:81.0) Gecko/81.0 Firefox/81.0',
            'Mozilla/5.0 (iPad; CPU OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
        ];

        $referrers = [
            'https://google.com/search',
            'https://facebook.com',
            'https://instagram.com',
            'https://twitter.com',
            'https://linkedin.com',
            'https://nairaland.com',
            'https://propertypro.ng',
            'https://jiji.ng',
            null, // Direct traffic
        ];

        $ipAddresses = [
            '192.168.1.1', '10.0.0.1', '172.16.0.1', '203.0.113.1',
            '198.51.100.1', '192.0.2.1', '169.254.1.1', '127.0.0.1',
            '8.8.8.8', '1.1.1.1', '208.67.222.222', '76.76.19.19',
        ];

        $totalViews = 0;

        foreach ($properties as $property) {
            // Each property gets 5-50 views over the last 30 days
            $viewCount = rand(5, 50);
            
            for ($i = 0; $i < $viewCount; $i++) {
                // Random date within last 30 days, with more recent dates having higher probability
                $daysAgo = $this->getWeightedRandomDays();
                $viewedAt = now()->subDays($daysAgo)->subHours(rand(0, 23))->subMinutes(rand(0, 59));
                
                // 60% chance of authenticated user view, 40% anonymous
                $user = (rand(1, 10) <= 6 && $users->isNotEmpty()) ? $users->random() : null;
                
                PropertyView::create([
                    'property_id' => $property->id,
                    'user_id' => $user?->id,
                    'ip_address' => fake()->randomElement($ipAddresses),
                    'user_agent' => fake()->randomElement($userAgents),
                    'session_id' => 'sess_' . fake()->uuid(),
                    'referrer' => fake()->randomElement($referrers),
                    'device_type' => $this->getDeviceTypeFromUserAgent(fake()->randomElement($userAgents)),
                    'browser' => $this->getBrowserFromUserAgent(fake()->randomElement($userAgents)),
                    'platform' => $this->getPlatformFromUserAgent(fake()->randomElement($userAgents)),
                    'country' => fake()->randomElement(['Nigeria', 'Ghana', 'Kenya', 'South Africa', 'Egypt']),
                    'city' => fake()->randomElement(['Lagos', 'Abuja', 'Port Harcourt', 'Kano', 'Ibadan', 'Accra', 'Nairobi', 'Cape Town', 'Cairo']),
                    'viewed_at' => $viewedAt,
                    'created_at' => $viewedAt,
                    'updated_at' => $viewedAt,
                ]);
                
                $totalViews++;
            }
            
            // Update property view count to match analytics
            $property->update(['view_count' => $viewCount]);
        }

        // Create some trending properties with higher view counts
        $trendingProperties = $properties->random(min(5, $properties->count()));
        
        foreach ($trendingProperties as $property) {
            $additionalViews = rand(50, 150);
            
            for ($i = 0; $i < $additionalViews; $i++) {
                $daysAgo = rand(0, 7); // Recent views for trending
                $viewedAt = now()->subDays($daysAgo)->subHours(rand(0, 23))->subMinutes(rand(0, 59));
                
                $user = (rand(1, 10) <= 7 && $users->isNotEmpty()) ? $users->random() : null;
                
                PropertyView::create([
                    'property_id' => $property->id,
                    'user_id' => $user?->id,
                    'ip_address' => fake()->randomElement($ipAddresses),
                    'user_agent' => fake()->randomElement($userAgents),
                    'session_id' => 'sess_' . fake()->uuid(),
                    'referrer' => fake()->randomElement($referrers),
                    'device_type' => $this->getDeviceTypeFromUserAgent(fake()->randomElement($userAgents)),
                    'browser' => $this->getBrowserFromUserAgent(fake()->randomElement($userAgents)),
                    'platform' => $this->getPlatformFromUserAgent(fake()->randomElement($userAgents)),
                    'country' => 'Nigeria',
                    'city' => fake()->randomElement(['Lagos', 'Abuja', 'Port Harcourt', 'Kano', 'Ibadan']),
                    'viewed_at' => $viewedAt,
                    'created_at' => $viewedAt,
                    'updated_at' => $viewedAt,
                ]);
                
                $totalViews++;
            }
            
            // Update property view count
            $currentViews = PropertyView::where('property_id', $property->id)->count();
            $property->update(['view_count' => $currentViews]);
        }

        $this->command->info('✅ Property Views seeded: ' . $totalViews . ' total views');
        $this->command->info('📈 Trending properties created: ' . $trendingProperties->count());
    }

    /**
     * Get weighted random days (more recent dates have higher probability)
     */
    private function getWeightedRandomDays(): int
    {
        $weights = [
            0 => 20,  // Today - 20% chance
            1 => 15,  // Yesterday - 15% chance
            2 => 10,  // 2 days ago - 10% chance
            3 => 10,  // 3 days ago - 10% chance
            4 => 8,   // 4 days ago - 8% chance
            5 => 7,   // 5 days ago - 7% chance
            6 => 6,   // 6 days ago - 6% chance
            7 => 5,   // 1 week ago - 5% chance
        ];
        
        // Fill remaining days with decreasing probability
        for ($i = 8; $i <= 30; $i++) {
            $weights[$i] = max(1, 4 - floor($i / 8));
        }
        
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($weights as $days => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $days;
            }
        }
        
        return 30; // Fallback
    }

    private function getDeviceTypeFromUserAgent(string $userAgent): string
    {
        if (preg_match('/ipad|tablet/i', $userAgent)) {
            return 'tablet';
        }
        if (preg_match('/mobile|android|iphone/i', $userAgent)) {
            return 'mobile';
        }
        return 'desktop';
    }

    private function getBrowserFromUserAgent(string $userAgent): string
    {
        if (preg_match('/chrome/i', $userAgent)) return 'Chrome';
        if (preg_match('/firefox/i', $userAgent)) return 'Firefox';
        if (preg_match('/safari/i', $userAgent)) return 'Safari';
        if (preg_match('/edge/i', $userAgent)) return 'Edge';
        return 'Chrome'; // Default
    }

    private function getPlatformFromUserAgent(string $userAgent): string
    {
        if (preg_match('/windows/i', $userAgent)) return 'Windows';
        if (preg_match('/macintosh|mac os/i', $userAgent)) return 'macOS';
        if (preg_match('/android/i', $userAgent)) return 'Android';
        if (preg_match('/iphone|ipad|ipod/i', $userAgent)) return 'iOS';
        if (preg_match('/linux/i', $userAgent)) return 'Linux';
        return 'Windows'; // Default
    }
}
