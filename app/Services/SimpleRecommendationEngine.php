<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyInteraction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SimpleRecommendationEngine
{
    private const CACHE_TTL = 1800; // 30 minutes cache
    private const LOCATION_WEIGHT = 0.5; // 50%
    private const PRICE_WEIGHT = 0.3;    // 30%
    private const TYPE_WEIGHT = 0.2;     // 20%

    /**
     * Get personalized recommendations for user dashboard
     */
    public function getRecommendationsForUser(User $user, int $limit = 6): Collection
    {
        $cacheKey = "simple_recommendations_user_{$user->id}_{$limit}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user, $limit) {
            return $this->calculateUserRecommendations($user, $limit);
        });
    }

    /**
     * Get similar property recommendations for property detail page
     */
    public function getRecommendationsForProperty(Property $property, User $user, int $limit = 4): Collection
    {
        $cacheKey = "simple_recommendations_property_{$property->id}_user_{$user->id}_{$limit}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($property, $user, $limit) {
            return $this->calculatePropertySimilarRecommendations($property, $user, $limit);
        });
    }

    /**
     * Calculate personalized recommendations for a user
     */
    private function calculateUserRecommendations(User $user, int $limit): Collection
    {
        // Get user context (preferences from interactions)
        $userContext = $this->getUserContext($user);

        if (empty($userContext['preferred_areas']) && empty($userContext['property_types'])) {
            // New user with no interactions - return popular properties
            return $this->getPopularProperties($limit);
        }

        // Get candidate properties
        $candidates = $this->getCandidateProperties($userContext, $limit);

        // Score and sort candidates
        $scoredProperties = $this->scoreProperties($candidates, $userContext);

        // Return top recommendations
        return $scoredProperties->take($limit);
    }

    /**
     * Calculate property-based recommendations (similar properties)
     */
    private function calculatePropertySimilarRecommendations(Property $property, User $user, int $limit): Collection
    {
        // Base similar properties on current property's attributes
        $candidates = Property::with(['propertyType', 'area.city.state', 'media'])
            ->where('status', 'available')
            ->where('id', '!=', $property->id) // Exclude current property
            ->where(function ($query) use ($property) {
                $query->where('area_id', $property->area_id) // Same area
                      ->orWhere('property_type_id', $property->property_type_id) // Same type
                      ->orWhereBetween('price', [ // Similar price range (±30%)
                          $property->price * 0.7,
                          $property->price * 1.3
                      ]);
            })
            ->limit(20) // Reasonable candidate pool
            ->get();

        // Get user context for personalized scoring
        $userContext = $this->getUserContext($user);

        // Score candidates
        $scoredProperties = $this->scoreProperties($candidates, $userContext, $property);

        return $scoredProperties->take($limit);
    }

    /**
     * Get user context from interaction history
     */
    private function getUserContext(User $user): array
    {
        // Get user interactions from last 90 days
        $interactions = PropertyInteraction::where('user_id', $user->id)
            ->where('interaction_date', '>=', now()->subDays(90))
            ->with('property')
            ->get();

        if ($interactions->isEmpty()) {
            return [
                'preferred_areas' => [],
                'price_range' => ['min' => null, 'max' => null],
                'property_types' => [],
            ];
        }

        // Prioritize recent interactions (last 30 days get 3x weight, last 7 days get 5x weight)
        $now = now();
        $weightedInteractions = $interactions->map(function ($interaction) use ($now) {
            $daysAgo = $now->diffInDays($interaction->interaction_date);
            if ($daysAgo <= 7) {
                $weight = 5.0; // Recent week - highest priority
            } elseif ($daysAgo <= 30) {
                $weight = 3.0; // Recent month - high priority
            } else {
                $weight = 1.0; // Older interactions - normal weight
            }
            return [
                'interaction' => $interaction,
                'weight' => $weight,
            ];
        });

        // Extract preferred areas with weighted counts and recency bias
        $areaData = [];
        foreach ($weightedInteractions as $item) {
            $areaId = $item['interaction']->property->area_id;
            if ($areaId) {
                if (!isset($areaData[$areaId])) {
                    $areaData[$areaId] = [
                        'weight' => 0,
                        'latest_interaction' => $item['interaction']->interaction_date,
                    ];
                }
                $areaData[$areaId]['weight'] += $item['weight'];

                // Update latest interaction date if this one is more recent
                if ($item['interaction']->interaction_date > $areaData[$areaId]['latest_interaction']) {
                    $areaData[$areaId]['latest_interaction'] = $item['interaction']->interaction_date;
                }
            }
        }

        // Sort by weight first, then by recency for tie-breaking
        uasort($areaData, function ($a, $b) {
            if ($a['weight'] === $b['weight']) {
                return $b['latest_interaction']->timestamp <=> $a['latest_interaction']->timestamp;
            }
            return $b['weight'] <=> $a['weight'];
        });

        $preferredAreas = array_slice(array_keys($areaData), 0, 3);

        // Extract price range (still use all interactions for broader range)
        $prices = $interactions->pluck('property.price')->filter()->sort()->values();
        $priceRange = $this->calculatePriceRange($prices);

        // Extract preferred property types with weighted counts
        $typeWeights = collect();
        foreach ($weightedInteractions as $item) {
            $typeId = $item['interaction']->property->property_type_id;
            if ($typeId) {
                $typeWeights[$typeId] = ($typeWeights[$typeId] ?? 0) + $item['weight'];
            }
        }
        $propertyTypes = $typeWeights->sortDesc()->take(2)->keys()->toArray();

        return [
            'preferred_areas' => $preferredAreas,
            'price_range' => $priceRange,
            'property_types' => $propertyTypes,
        ];
    }

    /**
     * Calculate price range from user interactions
     */
    private function calculatePriceRange(Collection $prices): array
    {
        if ($prices->isEmpty()) {
            return ['min' => null, 'max' => null];
        }

        $count = $prices->count();

        if ($count < 4) {
            // For small datasets, use min/max
            return [
                'min' => $prices->first(),
                'max' => $prices->last(),
            ];
        }

        // Use 25th and 75th percentile
        $p25Index = (int) ($count * 0.25);
        $p75Index = (int) ($count * 0.75);

        return [
            'min' => $prices[$p25Index],
            'max' => $prices[$p75Index],
        ];
    }

    /**
     * Get candidate properties based on user context
     */
    private function getCandidateProperties(array $userContext, int $limit): Collection
    {
        $query = Property::with(['propertyType', 'area.city.state', 'media'])
            ->where('status', 'available');

        // Exclude properties user has already interacted with
        $interactedPropertyIds = PropertyInteraction::where('user_id', auth()->id())
            ->pluck('property_id')
            ->toArray();

        if (!empty($interactedPropertyIds)) {
            $query->whereNotIn('id', $interactedPropertyIds);
        }

        // Apply flexible filtering - use OR conditions for better candidate pool
        if (!empty($userContext['preferred_areas']) ||
            ($userContext['price_range']['min'] && $userContext['price_range']['max']) ||
            !empty($userContext['property_types'])) {

            $query->where(function ($subQuery) use ($userContext) {
                $hasConditions = false;

                // Location preference (preferred but not required)
                if (!empty($userContext['preferred_areas'])) {
                    $subQuery->whereIn('area_id', $userContext['preferred_areas']);
                    $hasConditions = true;
                }

                // Price range preference (with buffer)
                if ($userContext['price_range']['min'] && $userContext['price_range']['max']) {
                    $minPrice = $userContext['price_range']['min'] * 0.6; // Wider buffer for more candidates
                    $maxPrice = $userContext['price_range']['max'] * 1.5; // Wider buffer for more candidates

                    if ($hasConditions) {
                        $subQuery->orWhereBetween('price', [$minPrice, $maxPrice]);
                    } else {
                        $subQuery->whereBetween('price', [$minPrice, $maxPrice]);
                        $hasConditions = true;
                    }
                }

                // Property type preference
                if (!empty($userContext['property_types'])) {
                    if ($hasConditions) {
                        $subQuery->orWhereIn('property_type_id', $userContext['property_types']);
                    } else {
                        $subQuery->whereIn('property_type_id', $userContext['property_types']);
                    }
                }
            });
        }

        return $query->limit($limit * 3)->get(); // Get more candidates for better scoring
    }

    /**
     * Score properties based on user context
     */
    private function scoreProperties(Collection $properties, array $userContext, ?Property $currentProperty = null): Collection
    {
        $scoredProperties = $properties->map(function ($property) use ($userContext, $currentProperty) {
            $score = $this->calculateSimpleScore($property, $userContext, $currentProperty);
            $property->recommendation_score = $score;
            return $property;
        });

        return $scoredProperties->sortByDesc('recommendation_score');
    }

    /**
     * Calculate simple 3-parameter score for a property
     */
    private function calculateSimpleScore(Property $property, array $userContext, ?Property $currentProperty = null): float
    {
        $score = 0;

        // Location Score (50% weight)
        if (!empty($userContext['preferred_areas']) && in_array($property->area_id, $userContext['preferred_areas'])) {
            $score += self::LOCATION_WEIGHT;
        } elseif ($currentProperty && $property->area_id === $currentProperty->area_id) {
            // For property-based recommendations, same area gets full location score
            $score += self::LOCATION_WEIGHT;
        }

        // Price Score (30% weight)
        if ($userContext['price_range']['min'] && $userContext['price_range']['max']) {
            $minPrice = $userContext['price_range']['min'];
            $maxPrice = $userContext['price_range']['max'];

            if ($property->price >= $minPrice && $property->price <= $maxPrice) {
                $score += self::PRICE_WEIGHT;
            }
        } elseif ($currentProperty) {
            // For property-based recommendations, similar price range (±30%)
            $minSimilarPrice = $currentProperty->price * 0.7;
            $maxSimilarPrice = $currentProperty->price * 1.3;

            if ($property->price >= $minSimilarPrice && $property->price <= $maxSimilarPrice) {
                $score += self::PRICE_WEIGHT;
            }
        }

        // Property Type Score (20% weight)
        if (!empty($userContext['property_types']) && in_array($property->property_type_id, $userContext['property_types'])) {
            $score += self::TYPE_WEIGHT;
        } elseif ($currentProperty && $property->property_type_id === $currentProperty->property_type_id) {
            // For property-based recommendations, same type gets full type score
            $score += self::TYPE_WEIGHT;
        }

        return round($score, 3);
    }

    /**
     * Get popular properties as fallback for new users
     */
    private function getPopularProperties(int $limit): Collection
    {
        return Property::with(['propertyType', 'area.city.state', 'media'])
            ->where('status', 'available')
            ->where('created_at', '>=', now()->subDays(30)) // Recent properties
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($property) {
                $property->recommendation_score = 0.1; // Low baseline score
                return $property;
            });
    }

    /**
     * Track property view interaction (simplified)
     */
    public static function trackPropertyView(int $userId, int $propertyId): void
    {
        try {
            PropertyInteraction::firstOrCreate([
                'user_id' => $userId,
                'property_id' => $propertyId,
                'interaction_type' => 'view',
                'interaction_date' => now()->startOfHour(), // Group by hour to prevent duplicates
            ], [
                'interaction_score' => 1.0, // Simple scoring
                'source' => 'web',
            ]);

            // Clear user's recommendation cache when they interact
            Cache::forget("simple_recommendations_user_{$userId}_6");
            Cache::forget("simple_recommendations_user_{$userId}_4");

        } catch (Exception $e) {
            Log::warning("Failed to track property view: {$e->getMessage()}");
        }
    }

    /**
     * Track property inquiry interaction (simplified)
     */
    public static function trackPropertyInquiry(int $userId, int $propertyId): void
    {
        try {
            PropertyInteraction::create([
                'user_id' => $userId,
                'property_id' => $propertyId,
                'interaction_type' => 'inquiry',
                'interaction_score' => 3.0, // Higher score for inquiry
                'source' => 'web',
                'interaction_date' => now(),
            ]);

            // Clear user's recommendation cache
            Cache::forget("simple_recommendations_user_{$userId}_6");
            Cache::forget("simple_recommendations_user_{$userId}_4");

        } catch (Exception $e) {
            Log::warning("Failed to track property inquiry: {$e->getMessage()}");
        }
    }

    /**
     * Get user's recommendation statistics
     */
    public function getRecommendationStats(User $user): array
    {
        $interactions = PropertyInteraction::where('user_id', $user->id)->count();
        $recommendations = $this->getRecommendationsForUser($user, 1);

        return [
            'total_interactions' => $interactions,
            'has_recommendations' => $recommendations->isNotEmpty(),
            'recommendation_quality' => $interactions > 5 ? 'high' : ($interactions > 0 ? 'medium' : 'low'),
        ];
    }
}