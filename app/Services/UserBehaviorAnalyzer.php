<?php

namespace App\Services;

use App\Models\User;
use App\Models\Property;
use App\Models\PropertyInteraction;
use App\Models\CustomerProfile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class UserBehaviorAnalyzer
{
    private const CACHE_TTL = 3600; // 1 hour cache

    /**
     * Analyze user behavior and generate property recommendations
     */
    public function getRecommendationsForUser(User $user, int $limit = 5): Collection
    {
        $cacheKey = "user_recommendations_{$user->id}_{$limit}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user, $limit) {
            return $this->calculateRecommendations($user, $limit);
        });
    }

    /**
     * Calculate recommendations based on user behavior
     */
    private function calculateRecommendations(User $user, int $limit): Collection
    {
        $profile = $user->customerProfile;
        if (!$profile) {
            return collect();
        }

        // Get user's interaction patterns
        $userPreferences = $this->analyzeUserPreferences($user);

        // Find similar users
        $similarUsers = $this->findSimilarUsers($user);

        // Get candidate properties
        $candidateProperties = $this->getCandidateProperties($user, $userPreferences);

        // Score and rank properties
        $scoredProperties = $this->scoreProperties($user, $candidateProperties, $userPreferences, $similarUsers);

        return $scoredProperties->take($limit);
    }

    /**
     * Analyze user preferences based on interaction history
     */
    public function analyzeUserPreferences(User $user): array
    {
        $profile = $user->customerProfile;

        return [
            'engagement_score' => $profile->getEngagementScore(),
            'preferred_locations' => $profile->getPreferredLocationsFromBehavior(),
            'preferred_price_range' => $profile->getPreferredPriceRange(),
            'preferred_features' => $profile->getPreferredFeatures(),
            'interaction_patterns' => $this->getInteractionPatterns($user),
            'property_type_preferences' => $this->getPropertyTypePreferences($user),
        ];
    }

    /**
     * Get user's interaction patterns
     */
    private function getInteractionPatterns(User $user): array
    {
        $interactions = PropertyInteraction::forUser($user->id)
            ->recent(60)
            ->with('property')
            ->get();

        $patterns = [
            'total_interactions' => $interactions->count(),
            'avg_daily_activity' => $interactions->count() / 60,
            'interaction_types' => [],
            'time_patterns' => [],
            'price_sensitivity' => $this->calculatePriceSensitivity($interactions),
        ];

        // Analyze interaction types
        foreach ($interactions->groupBy('interaction_type') as $type => $typeInteractions) {
            $patterns['interaction_types'][$type] = [
                'count' => $typeInteractions->count(),
                'avg_score' => $typeInteractions->avg('interaction_score'),
            ];
        }

        // Analyze time patterns
        foreach ($interactions as $interaction) {
            $hour = $interaction->interaction_date->hour;
            $dayOfWeek = $interaction->interaction_date->dayOfWeek;

            $patterns['time_patterns']['hours'][$hour] =
                ($patterns['time_patterns']['hours'][$hour] ?? 0) + 1;
            $patterns['time_patterns']['days'][$dayOfWeek] =
                ($patterns['time_patterns']['days'][$dayOfWeek] ?? 0) + 1;
        }

        return $patterns;
    }

    /**
     * Calculate user's price sensitivity
     */
    private function calculatePriceSensitivity(Collection $interactions): array
    {
        $priceInteractions = [];

        foreach ($interactions as $interaction) {
            $price = $interaction->property->price;
            $score = $interaction->getTimeDecayedScore();

            $priceInteractions[] = [
                'price' => $price,
                'score' => $score,
                'type' => $interaction->interaction_type,
            ];
        }

        if (empty($priceInteractions)) {
            return ['sensitivity' => 'unknown', 'preferred_range' => null];
        }

        // Sort by interaction score to find most engaged price ranges
        usort($priceInteractions, fn($a, $b) => $b['score'] <=> $a['score']);

        $topInteractions = array_slice($priceInteractions, 0, 10);
        $topPrices = array_column($topInteractions, 'price');

        return [
            'sensitivity' => $this->determinePriceSensitivity($priceInteractions),
            'preferred_range' => [
                'min' => min($topPrices),
                'max' => max($topPrices),
                'avg' => array_sum($topPrices) / count($topPrices),
            ],
        ];
    }

    /**
     * Determine price sensitivity level
     */
    private function determinePriceSensitivity(array $priceInteractions): string
    {
        $priceRanges = array_column($priceInteractions, 'price');
        $coefficient = $this->calculateCoefficientOfVariation($priceRanges);

        if ($coefficient < 0.3) return 'low'; // Consistent price range
        if ($coefficient < 0.6) return 'medium'; // Moderate variation
        return 'high'; // Wide price variation
    }

    /**
     * Calculate coefficient of variation
     */
    private function calculateCoefficientOfVariation(array $values): float
    {
        if (empty($values)) return 0;

        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(fn($x) => pow($x - $mean, 2), $values)) / count($values);
        $stdDev = sqrt($variance);

        return $mean > 0 ? $stdDev / $mean : 0;
    }

    /**
     * Get property type preferences from interactions
     */
    private function getPropertyTypePreferences(User $user): array
    {
        $interactions = PropertyInteraction::forUser($user->id)
            ->highEngagement()
            ->with('property.propertyType')
            ->get();

        $typeScores = [];
        foreach ($interactions as $interaction) {
            $typeId = $interaction->property->property_type_id;
            $score = $interaction->getTimeDecayedScore();

            $typeScores[$typeId] = ($typeScores[$typeId] ?? 0) + $score;
        }

        arsort($typeScores);
        return $typeScores;
    }

    /**
     * Find users with similar behavior patterns
     */
    private function findSimilarUsers(User $user, int $limit = 20): Collection
    {
        $profile = $user->customerProfile;
        $similarUserIds = $profile->getSimilarUsers($limit);

        return User::whereIn('id', array_column($similarUserIds, 'user_id'))
            ->with('customerProfile')
            ->get();
    }

    /**
     * Get candidate properties for recommendation
     */
    private function getCandidateProperties(User $user, array $preferences): Collection
    {
        $query = Property::with(['propertyType', 'area.city.state', 'media', 'features'])
            ->where('status', 'available')
            ->where('created_at', '>=', now()->subDays(30)); // Recent properties

        // Exclude properties user has already interacted with
        $interactedPropertyIds = PropertyInteraction::forUser($user->id)
            ->pluck('property_id')
            ->toArray();

        if (!empty($interactedPropertyIds)) {
            $query->whereNotIn('id', $interactedPropertyIds);
        }

        // Apply basic filters based on user preferences
        if (!empty($preferences['preferred_locations']['areas'])) {
            $preferredAreas = array_keys($preferences['preferred_locations']['areas']);
            $query->whereIn('area_id', $preferredAreas);
        }

        // Apply price range if available
        $priceRange = $preferences['preferred_price_range'];
        if ($priceRange['min'] && $priceRange['max']) {
            // Expand range by 20% to include slightly out-of-budget options
            $minPrice = $priceRange['min'] * 0.8;
            $maxPrice = $priceRange['max'] * 1.2;

            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }

        return $query->limit(100)->get(); // Limit candidates for performance
    }

    /**
     * Score properties based on user preferences and similar users
     */
    private function scoreProperties(User $user, Collection $properties, array $preferences, Collection $similarUsers): Collection
    {
        $scoredProperties = [];

        foreach ($properties as $property) {
            $score = $this->calculatePropertyScore($user, $property, $preferences, $similarUsers);

            if ($score > 0) {
                $scoredProperties[] = [
                    'property' => $property,
                    'score' => $score,
                    'reasons' => $this->getRecommendationReasons($user, $property, $preferences),
                ];
            }
        }

        // Sort by score descending
        usort($scoredProperties, fn($a, $b) => $b['score'] <=> $a['score']);

        return collect($scoredProperties)->map(function ($item) {
            $item['property']->recommendation_score = $item['score'];
            $item['property']->recommendation_reasons = $item['reasons'];
            return $item['property'];
        });
    }

    /**
     * Calculate score for a specific property
     */
    private function calculatePropertyScore(User $user, Property $property, array $preferences, Collection $similarUsers): float
    {
        $score = 0;

        // Base score from user's direct affinity
        $affinityScore = $user->customerProfile->getPropertyAffinityScore($property);
        $score += $affinityScore * 0.4; // 40% weight

        // Location preference score
        $locationScore = $this->calculateLocationScore($property, $preferences['preferred_locations']);
        $score += $locationScore * 0.25; // 25% weight

        // Price compatibility score
        $priceScore = $this->calculatePriceScore($property, $preferences['preferred_price_range']);
        $score += $priceScore * 0.15; // 15% weight

        // Similar users score
        $similarUsersScore = $this->calculateSimilarUsersScore($property, $similarUsers);
        $score += $similarUsersScore * 0.15; // 15% weight

        // Property popularity score
        $popularityScore = PropertyInteraction::getPropertyPopularityScore($property->id);
        $score += min($popularityScore * 0.05, 2); // 5% weight, max 2 points

        return round($score, 2);
    }

    /**
     * Calculate location-based score
     */
    private function calculateLocationScore(Property $property, array $locationPreferences): float
    {
        $score = 0;

        // Area match (highest priority)
        if (isset($locationPreferences['areas'][$property->area_id])) {
            $score += $locationPreferences['areas'][$property->area_id] * 0.6;
        }

        // City match
        if ($property->area && isset($locationPreferences['cities'][$property->area->city_id])) {
            $score += $locationPreferences['cities'][$property->area->city_id] * 0.3;
        }

        // State match
        if ($property->area?->city && isset($locationPreferences['states'][$property->area->city->state_id])) {
            $score += $locationPreferences['states'][$property->area->city->state_id] * 0.1;
        }

        return min($score, 10); // Cap at 10 points
    }

    /**
     * Calculate price compatibility score
     */
    private function calculatePriceScore(Property $property, array $priceRange): float
    {
        if (!$priceRange['min'] || !$priceRange['max']) {
            return 2; // Neutral score if no price preference
        }

        $min = $priceRange['min'];
        $max = $priceRange['max'];
        $price = $property->price;

        // Perfect score if within range
        if ($price >= $min && $price <= $max) {
            return 5;
        }

        // Partial score if within 20% of range
        $lowerBound = $min * 0.8;
        $upperBound = $max * 1.2;

        if ($price >= $lowerBound && $price <= $upperBound) {
            return 3;
        }

        // Lower score if within 40% of range
        $lowerBound = $min * 0.6;
        $upperBound = $max * 1.4;

        if ($price >= $lowerBound && $price <= $upperBound) {
            return 1;
        }

        return 0; // No score if too far from preferred range
    }

    /**
     * Calculate score based on similar users' interactions
     */
    private function calculateSimilarUsersScore(Property $property, Collection $similarUsers): float
    {
        $score = 0;
        $userCount = 0;

        foreach ($similarUsers as $similarUser) {
            $userScore = PropertyInteraction::getUserPropertyAffinity($similarUser->id, $property->id);
            if ($userScore > 0) {
                $score += $userScore;
                $userCount++;
            }
        }

        return $userCount > 0 ? ($score / $userCount) : 0;
    }

    /**
     * Get explanation for why property was recommended
     */
    private function getRecommendationReasons(User $user, Property $property, array $preferences): array
    {
        $reasons = [];

        // Location-based reasons
        if (isset($preferences['preferred_locations']['areas'][$property->area_id])) {
            $reasons[] = "Located in your preferred area: {$property->area->name}";
        }

        // Price-based reasons
        $priceRange = $preferences['preferred_price_range'];
        if ($priceRange['min'] && $priceRange['max']) {
            if ($property->price >= $priceRange['min'] && $property->price <= $priceRange['max']) {
                $reasons[] = "Within your preferred price range";
            }
        }

        // Similar properties reasons
        $affinityScore = $user->customerProfile->getPropertyAffinityScore($property);
        if ($affinityScore > 5) {
            $reasons[] = "Similar to properties you've shown interest in";
        }

        // Popularity reasons
        $popularityScore = PropertyInteraction::getPropertyPopularityScore($property->id);
        if ($popularityScore > 20) {
            $reasons[] = "Popular among other users";
        }

        return $reasons;
    }
}