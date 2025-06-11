<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\PropertyInquiry;
use App\Models\PropertyViewing;
use App\Models\Review;
use App\Models\SavedProperty;
use App\Models\SavedSearch;
use App\Models\User;
use App\Models\Agency;
use App\Models\Agent;

class Phase7EngagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Phase 7: Engagement & Communication Seeding...');

        // Get existing data
        $properties = Property::take(20)->get();
        $tenantUsers = User::where('user_type', 'tenant')->take(15)->get();
        $agents = User::where('user_type', 'agent')->take(10)->get();
        $agencies = Agency::take(5)->get();

        // If no tenant users exist, create some sample tenants
        if ($tenantUsers->isEmpty()) {
            $this->command->info('📝 Creating sample tenant users...');
            for ($i = 1; $i <= 15; $i++) {
                User::create([
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => bcrypt('password'),
                    'user_type' => 'tenant',
                    'phone' => '0' . rand(700, 999) . rand(1000000, 9999999),
                    'is_active' => true,
                ]);
            }
            $tenantUsers = User::where('user_type', 'tenant')->get();
            $this->command->info('✅ Created ' . $tenantUsers->count() . ' tenant users');
        }

        if ($properties->isEmpty()) {
            $this->command->warn('⚠️ Properties not found. Please run Phase 5 seeder first.');
            return;
        }

        // 1. Property Inquiries
        $this->seedPropertyInquiries($properties, $tenantUsers, $agents);

        // 2. Property Viewings
        $this->seedPropertyViewings($properties, $tenantUsers, $agents);

        // 3. Reviews (Properties, Agencies, Agents)
        $this->seedReviews($properties, $tenantUsers, $agencies, $agents);

        // 4. Saved Properties
        $this->seedSavedProperties($properties, $tenantUsers);

        // 5. Saved Searches
        $this->seedSavedSearches($tenantUsers);

        $this->command->info('✅ Phase 7 Engagement & Communication seeding completed successfully!');
    }

    private function seedPropertyInquiries($properties, $users, $agents): void
    {
        $this->command->info('📧 Seeding Property Inquiries...');

        $inquiryMessages = [
            'Hi, I\'m interested in viewing this property. When would be a good time?',
            'Is this property still available for rent? I would like to schedule a viewing.',
            'Could you provide more details about the amenities and neighborhood?',
            'I\'m looking to move in next month. Is the property available from then?',
            'What are the lease terms and conditions? Any additional fees?',
            'Is negotiation possible on the rent? I\'m a serious tenant.',
            'Could you arrange a viewing this weekend? I\'m available both days.',
            'What utilities are included in the rent? Electricity, water, internet?',
            'Is parking included? I have two cars.',
            'What\'s the security deposit required for this property?',
        ];

        $responses = [
            'Thank you for your inquiry. The property is available for viewing. Please let me know your preferred time.',
            'Yes, the property is still available. I can arrange a viewing for you tomorrow or this weekend.',
            'The property comes with 24/7 security, gym, swimming pool, and backup generator. Great neighborhood with easy access to major roads.',
            'The property will be available from next month. Would you like to schedule a viewing to see it?',
            'Lease terms are flexible - 1 or 2 years. Security deposit is equivalent to 2 months rent plus legal fees.',
            'The rent is slightly negotiable for serious tenants. Let\'s discuss during the viewing.',
        ];

        foreach ($properties as $property) {
            // 1-5 inquiries per property
            $inquiryCount = rand(1, 5);
            
            for ($i = 0; $i < $inquiryCount; $i++) {
                $inquirer = $users->random();
                $status = collect(['new', 'contacted', 'scheduled', 'viewed', 'closed'])->random();
                
                $inquiry = PropertyInquiry::create([
                    'property_id' => $property->id,
                    'inquirer_id' => $inquirer->id,
                    'inquirer_name' => $inquirer->name,
                    'inquirer_email' => $inquirer->email,
                    'inquirer_phone' => '0' . rand(700, 999) . rand(1000000, 9999999),
                    'message' => fake()->randomElement($inquiryMessages),
                    'preferred_viewing_date' => rand(0, 1) ? now()->addDays(rand(1, 14)) : null,
                    'status' => $status,
                ]);

                // Add response for contacted/closed inquiries
                if (in_array($status, ['contacted', 'scheduled', 'viewed', 'closed'])) {
                    $inquiry->update([
                        'responded_at' => now()->subDays(rand(0, 5)),
                        'responded_by' => $agents->random()->id,
                        'response_message' => fake()->randomElement($responses),
                    ]);
                }
            }
        }

        $this->command->info('✅ Property Inquiries seeded: ' . PropertyInquiry::count());
    }

    private function seedPropertyViewings($properties, $users, $agents): void
    {
        $this->command->info('👁️ Seeding Property Viewings...');

        foreach ($properties as $property) {
            // 0-3 viewings per property
            $viewingCount = rand(0, 3);
            
            for ($i = 0; $i < $viewingCount; $i++) {
                $scheduledDate = now()->addDays(rand(-30, 30));
                $status = collect(['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'])->random();
                
                // Adjust status based on date
                if ($scheduledDate->isPast()) {
                    $status = collect(['completed', 'cancelled', 'no_show'])->random();
                } else {
                    $status = collect(['scheduled', 'confirmed'])->random();
                }

                $viewing = PropertyViewing::create([
                    'property_id' => $property->id,
                    'inquirer_id' => $users->random()->id,
                    'agent_id' => $agents->random()->id,
                    'scheduled_date' => $scheduledDate->toDateString(),
                    'scheduled_time' => $scheduledDate->setTime(rand(9, 17), rand(0, 1) * 30),
                    'status' => $status,
                    'notes' => $status === 'completed' ? 'Client showed interest and may proceed with application.' : null,
                ]);

                // Add completion/cancellation details
                if ($status === 'completed') {
                    $viewing->update([
                        'completed_at' => $scheduledDate->addHours(1),
                        'notes' => collect([
                            'Client was impressed with the property and location.',
                            'Viewing went well, client asking about lease terms.',
                            'Client loved the amenities, especially the gym and pool.',
                            'Property meets client requirements, may submit application.',
                        ])->random(),
                    ]);
                } elseif ($status === 'cancelled') {
                    $viewing->update([
                        'cancelled_at' => $scheduledDate->subHours(2),
                        'cancellation_reason' => collect([
                            'Client schedule conflict',
                            'Client found another property',
                            'Agent unavailable',
                            'Property no longer available',
                        ])->random(),
                    ]);
                }
            }
        }

        $this->command->info('✅ Property Viewings seeded: ' . PropertyViewing::count());
    }

    private function seedReviews($properties, $users, $agencies, $agents): void
    {
        $this->command->info('⭐ Seeding Reviews...');

        $propertyReviewTitles = [
            'Great property in excellent location',
            'Spacious and well-maintained',
            'Perfect for families',
            'Value for money',
            'Modern amenities and security',
            'Quiet neighborhood',
            'Could be better',
            'Disappointed with maintenance',
        ];

        $propertyReviewComments = [
            'This property exceeded my expectations. The location is perfect with easy access to schools and shopping centers.',
            'Very spacious rooms and well-maintained facilities. The security is top-notch.',
            'Great value for the price. The amenities are modern and well-maintained.',
            'The neighborhood is quiet and family-friendly. Perfect for raising children.',
            'Property management is responsive and professional.',
            'Some maintenance issues that took time to resolve, but overall good experience.',
            'The property needs some updates, especially the kitchen and bathrooms.',
            'Location is good but the building needs better maintenance.',
        ];

        $agencyReviewTitles = [
            'Professional and reliable service',
            'Excellent customer service',
            'Quick response time',
            'Helped find perfect property',
            'Smooth rental process',
            'Could improve communication',
        ];

        $agencyReviewComments = [
            'The agency was very professional throughout the entire process. Highly recommended.',
            'Quick to respond to inquiries and very helpful in finding the right property.',
            'Made the rental process smooth and hassle-free.',
            'Good selection of properties and transparent pricing.',
            'Professional team that understands client needs.',
            'Sometimes slow to respond but overall good service.',
        ];

        // Property Reviews
        foreach ($properties->take(15) as $property) {
            $reviewCount = rand(0, 4);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                Review::create([
                    'reviewable_type' => Property::class,
                    'reviewable_id' => $property->id,
                    'reviewer_id' => $users->random()->id,
                    'rating' => rand(2, 5),
                    'title' => fake()->randomElement($propertyReviewTitles),
                    'comment' => fake()->randomElement($propertyReviewComments),
                    'is_verified' => rand(0, 1),
                    'is_approved' => rand(0, 10) > 1, // 90% approval rate
                    'helpful_count' => rand(0, 15),
                ]);
            }
        }

        // Agency Reviews
        foreach ($agencies as $agency) {
            $reviewCount = rand(1, 6);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                Review::create([
                    'reviewable_type' => Agency::class,
                    'reviewable_id' => $agency->id,
                    'reviewer_id' => $users->random()->id,
                    'rating' => rand(3, 5),
                    'title' => fake()->randomElement($agencyReviewTitles),
                    'comment' => fake()->randomElement($agencyReviewComments),
                    'is_verified' => rand(0, 1),
                    'is_approved' => rand(0, 10) > 1, // 90% approval rate
                    'helpful_count' => rand(0, 20),
                ]);
            }
        }

        // Agent Reviews
        foreach ($agents->take(8) as $agent) {
            $reviewCount = rand(0, 3);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                Review::create([
                    'reviewable_type' => User::class,
                    'reviewable_id' => $agent->id,
                    'reviewer_id' => $users->random()->id,
                    'rating' => rand(3, 5),
                    'title' => 'Professional and knowledgeable agent',
                    'comment' => 'Very helpful agent who understood my requirements and showed me suitable properties.',
                    'is_verified' => rand(0, 1),
                    'is_approved' => rand(0, 10) > 1, // 90% approval rate
                    'helpful_count' => rand(0, 10),
                ]);
            }
        }

        $this->command->info('✅ Reviews seeded: ' . Review::count());
    }

    private function seedSavedProperties($properties, $users): void
    {
        $this->command->info('💾 Seeding Saved Properties...');

        $saveNotes = [
            'Potential backup option',
            'Good location, need to consider budget',
            'Perfect size, checking neighborhood',
            'Interested but waiting for salary confirmation',
            'Comparing with other options',
            'Need to visit in person',
            'Shortlisted for final decision',
        ];

        foreach ($users as $user) {
            // Each user saves 2-8 properties
            $savedCount = rand(2, 8);
            $userProperties = $properties->random($savedCount);
            
            foreach ($userProperties as $property) {
                SavedProperty::create([
                    'user_id' => $user->id,
                    'property_id' => $property->id,
                    'notes' => rand(0, 1) ? fake()->randomElement($saveNotes) : null,
                ]);
            }
        }

        $this->command->info('✅ Saved Properties seeded: ' . SavedProperty::count());
    }

    private function seedSavedSearches($users): void
    {
        $this->command->info('🔍 Seeding Saved Searches...');

        $searchNames = [
            '2-Bedroom Apartments in Lekki',
            'Houses under ₦2M in Ikeja',
            'Luxury Properties in Victoria Island',
            'Student Housing near UNILAG',
            'Family Homes in Magodo',
            'Office Spaces in Marina',
            'Shortlet Apartments',
            'Penthouses in Ikoyi',
        ];

        foreach ($users as $user) {
            // Each user has 1-4 saved searches
            $searchCount = rand(1, 4);
            
            for ($i = 0; $i < $searchCount; $i++) {
                SavedSearch::create([
                    'user_id' => $user->id,
                    'name' => fake()->randomElement($searchNames),
                    'search_criteria' => [
                        'property_type_id' => rand(1, 5),
                        'min_price' => rand(50000, 500000),
                        'max_price' => rand(1000000, 5000000),
                        'bedrooms' => rand(1, 4),
                        'state_id' => rand(1, 3),
                        'city_id' => rand(1, 10),
                        'listing_type' => collect(['rent', 'sale', 'shortlet'])->random(),
                    ],
                    'alert_frequency' => collect(['daily', 'weekly', 'monthly'])->random(),
                    'is_active' => rand(0, 10) > 2, // 80% active
                    'last_alerted_at' => rand(0, 1) ? now()->subDays(rand(1, 7)) : null,
                ]);
            }
        }

        $this->command->info('✅ Saved Searches seeded: ' . SavedSearch::count());
    }
}
