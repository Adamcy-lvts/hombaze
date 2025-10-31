<?php

namespace App\Services;

use App\Models\Property;
use App\Models\SavedSearch;
use App\Models\Area;
use App\Models\City;
use App\Models\State;
use App\Models\PropertySubtype;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SavedSearchMatcher
{
    private const CACHE_TTL = 1800; // 30 minutes cache
    private const MIN_MATCH_SCORE = 70; // Minimum score for a valid match
    private const PERFECT_MATCH_SCORE = 90; // Score for instant notifications

    /**
     * Find matches for a single property against all active saved searches
     */
    public function findMatchesForProperty(Property $property): Collection
    {
        \Log::info('🔍 STARTING PROPERTY MATCH SEARCH', [
            'property_id' => $property->id,
            'property_title' => $property->title,
            'property_price' => $property->price,
            'property_type' => $property->propertyType->name ?? 'Unknown',
            'listing_type' => $property->listing_type,
            'area' => $property->area->name ?? 'Unknown',
            'city' => $property->area->city->name ?? 'Unknown'
        ]);

        $this->log('info', "Finding matches for property: {$property->title} (ID: {$property->id})");

        $activeSearches = SavedSearch::active()
            ->with('user')
            ->get();

        \Log::info('📋 ACTIVE SAVED SEARCHES FOUND', [
            'total_active_searches' => $activeSearches->count(),
            'searches' => $activeSearches->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'user_email' => $s->user->email ?? 'No user',
                'categories' => $s->property_categories,
                'location' => $s->location_preferences
            ])
        ]);

        $matches = collect();

        foreach ($activeSearches as $search) {
            \Log::info('🎯 TESTING MATCH', [
                'search_id' => $search->id,
                'search_name' => $search->name,
                'user_email' => $search->user->email ?? 'No user',
                'property_id' => $property->id
            ]);

            $score = $this->calculateMatchScore($property, $search);

            \Log::info('📊 MATCH SCORE CALCULATED', [
                'search_id' => $search->id,
                'property_id' => $property->id,
                'score' => $score,
                'min_required' => self::MIN_MATCH_SCORE,
                'is_match' => $score >= self::MIN_MATCH_SCORE
            ]);

            if ($score >= self::MIN_MATCH_SCORE) {
                $matches->push([
                    'saved_search' => $search,
                    'property' => $property,
                    'score' => $score,
                    'match_reasons' => $this->getMatchReasons($property, $search, $score),
                    'notification_priority' => $this->getNotificationPriority($score),
                ]);

                \Log::info('✅ MATCH FOUND!', [
                    'search_id' => $search->id,
                    'search_name' => $search->name,
                    'user_email' => $search->user->email ?? 'No user',
                    'property_id' => $property->id,
                    'score' => $score,
                    'priority' => $this->getNotificationPriority($score)
                ]);

                $this->log('info', "Match found: Search '{$search->name}' (Score: {$score})");
            } else {
                \Log::info('❌ NO MATCH', [
                    'search_id' => $search->id,
                    'property_id' => $property->id,
                    'score' => $score,
                    'reason' => 'Score below minimum threshold'
                ]);
            }
        }

        \Log::info('🏁 PROPERTY MATCH SEARCH COMPLETE', [
            'property_id' => $property->id,
            'total_matches' => $matches->count(),
            'matches' => $matches->map(fn($m) => [
                'search_id' => $m['saved_search']->id,
                'user_email' => $m['saved_search']->user->email ?? 'No user',
                'score' => $m['score']
            ])
        ]);

        $this->log('info', "Found {$matches->count()} matches for property {$property->id}");

        return $matches->sortByDesc('score');
    }

    /**
     * Find matches for a single saved search against existing properties
     */
    public function findMatchesForSavedSearch(SavedSearch $search, int $limit = 20): Collection
    {
        $this->log('info', "Finding matches for saved search: {$search->name} (ID: {$search->id})");

        $cacheKey = "saved_search_matches_{$search->id}_{$limit}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($search, $limit) {
            return $this->calculateMatchesForSavedSearch($search, $limit);
        });
    }

    /**
     * Calculate matches for a saved search
     */
    private function calculateMatchesForSavedSearch(SavedSearch $search, int $limit): Collection
    {
        $baseQuery = Property::with(['propertyType', 'area.city.state', 'media', 'features'])
            ->where('status', 'available');

        // Apply basic filters to reduce dataset
        $candidateProperties = $this->applyCandidateFilters($baseQuery, $search)->get();

        $matches = collect();

        foreach ($candidateProperties as $property) {
            $score = $this->calculateMatchScore($property, $search);

            if ($score >= self::MIN_MATCH_SCORE) {
                $matches->push([
                    'property' => $property,
                    'saved_search' => $search,
                    'score' => $score,
                    'match_reasons' => $this->getMatchReasons($property, $search, $score),
                ]);
            }
        }

        return $matches->sortByDesc('score')->take($limit);
    }

    /**
     * Apply candidate filters to reduce the property dataset
     */
    private function applyCandidateFilters($query, SavedSearch $search)
    {
        // Location filtering - supports both old and new formats
        if ($search->location_preferences) {
            $location = $search->location_preferences;

            // Handle new multiple area format
            if (isset($location['area_selection_type'])) {
                if ($location['area_selection_type'] === 'specific' && isset($location['selected_areas']) && !empty($location['selected_areas'])) {
                    // Filter for properties in any of the selected areas
                    $query->whereIn('area_id', $location['selected_areas']);
                } elseif ($location['area_selection_type'] === 'any' || $location['area_selection_type'] === 'all') {
                    // For 'any' or 'all', filter by city if available
                    if (isset($location['city']) && $location['city']) {
                        $query->whereHas('area', function ($q) use ($location) {
                            $q->where('city_id', (int)$location['city']);
                        });
                    } elseif (isset($location['state']) && $location['state']) {
                        $query->whereHas('area.city', function ($q) use ($location) {
                            $q->where('state_id', (int)$location['state']);
                        });
                    }
                }
            }
            // Handle old single area format (backward compatibility)
            elseif (isset($location['area']) && $location['area']) {
                $query->where('area_id', (int)$location['area']);
            } elseif (isset($location['city']) && $location['city']) {
                $query->whereHas('area', function ($q) use ($location) {
                    $q->where('city_id', (int)$location['city']);
                });
            } elseif (isset($location['state']) && $location['state']) {
                $query->whereHas('area.city', function ($q) use ($location) {
                    $q->where('state_id', (int)$location['state']);
                });
            }
        }

        // Budget filtering (with 20% buffer)
        if ($search->budget_min) {
            $query->where('price', '>=', $search->budget_min * 0.8);
        }
        if ($search->budget_max) {
            $query->where('price', '<=', $search->budget_max * 1.2);
        }

        // Property type filtering (new system)
        if ($search->selected_property_type) {
            $query->where('property_type_id', $search->selected_property_type);

            // Also filter by listing type based on search type
            if ($search->search_type) {
                $listingTypeMap = [
                    'buy' => 'sale',
                    'rent' => 'rent',
                    'shortlet' => 'shortlet'
                ];
                if (isset($listingTypeMap[$search->search_type])) {
                    $query->where('listing_type', $listingTypeMap[$search->search_type]);
                }
            }
        }
        // Fallback: Property category filtering (legacy system)
        elseif ($search->property_categories) {
            $this->applyPropertyCategoryFilter($query, $search->property_categories);
        }

        // Direct property type and listing type filtering from additional_filters
        if ($search->additional_filters) {
            $filters = $search->additional_filters;

            if (isset($filters['property_type_id'])) {
                $query->where('property_type_id', $filters['property_type_id']);
            }

            if (isset($filters['listing_type'])) {
                $query->where('listing_type', $filters['listing_type']);
            }

            if (isset($filters['bedrooms'])) {
                $query->where('bedrooms', '>=', $filters['bedrooms']);
            }
        }

        return $query;
    }

    /**
     * Apply property category filters based on saved search categories
     */
    private function applyPropertyCategoryFilter($query, array $categories)
    {
        $query->where(function ($q) use ($categories) {
            foreach ($categories as $category) {
                switch ($category) {
                    case 'house_rent':
                        $q->orWhere(function ($subQ) {
                            $subQ->whereHas('propertyType', function ($typeQ) {
                                $typeQ->where('name', 'like', '%house%');
                            })->where('listing_type', 'rent');
                        });
                        break;

                    case 'house_buy':
                        $q->orWhere(function ($subQ) {
                            $subQ->whereHas('propertyType', function ($typeQ) {
                                $typeQ->where('name', 'like', '%house%');
                            })->where('listing_type', 'sale');
                        });
                        break;

                    case 'land_buy':
                        $q->orWhere(function ($subQ) {
                            $subQ->whereHas('propertyType', function ($typeQ) {
                                $typeQ->where('name', 'like', '%land%');
                            })->where('listing_type', 'sale');
                        });
                        break;

                    case 'shop_rent':
                        $q->orWhere(function ($subQ) {
                            $subQ->whereHas('propertyType', function ($typeQ) {
                                $typeQ->whereIn('name', ['Commercial', 'Shop']);
                            })->where('listing_type', 'rent');
                        });
                        break;

                    case 'shop_buy':
                        $q->orWhere(function ($subQ) {
                            $subQ->whereHas('propertyType', function ($typeQ) {
                                $typeQ->whereIn('name', ['Commercial', 'Shop']);
                            })->where('listing_type', 'sale');
                        });
                        break;
                }
            }
        });
    }

    /**
     * Calculate match score between property and saved search
     */
    public function calculateMatchScore(Property $property, SavedSearch $search): float
    {
        $score = 0;

        // Location matching (30 points max)
        $score += $this->calculateLocationScore($property, $search);

        // Property category matching (25 points max)
        $score += $this->calculateCategoryScore($property, $search);

        // Budget matching (20 points max)
        $score += $this->calculateBudgetScore($property, $search);

        // Property subtype matching (15 points max)
        $score += $this->calculateSubtypeScore($property, $search);

        // Additional filters matching (10 points max)
        $score += $this->calculateAdditionalFiltersScore($property, $search);

        return round($score, 2);
    }

    /**
     * Calculate location match score - supports both old and new area formats
     */
    private function calculateLocationScore(Property $property, SavedSearch $search): float
    {
        if (!$search->location_preferences) {
            return 10; // Neutral score if no location preference
        }

        $location = $search->location_preferences;

        // Handle new multiple area format
        if (isset($location['area_selection_type'])) {
            if ($location['area_selection_type'] === 'specific' && isset($location['selected_areas']) && !empty($location['selected_areas'])) {
                // Check if property is in any of the selected areas
                if (in_array($property->area_id, $location['selected_areas'])) {
                    return 30; // Perfect area match
                }
            } elseif ($location['area_selection_type'] === 'any') {
                // For 'any' area, give a good score for city/state match
                if (isset($location['city']) && $property->area && (int)$location['city'] === $property->area->city_id) {
                    return 25; // High score for any area in preferred city
                }
                if (isset($location['state']) && $property->area?->city && (int)$location['state'] === $property->area->city->state_id) {
                    return 15; // Good score for any area in preferred state
                }
            } elseif ($location['area_selection_type'] === 'all') {
                // For 'all' areas, give highest score for city match
                if (isset($location['city']) && $property->area && (int)$location['city'] === $property->area->city_id) {
                    return 30; // Perfect score for all areas in city
                }
                if (isset($location['state']) && $property->area?->city && (int)$location['state'] === $property->area->city->state_id) {
                    return 20; // High score for all areas in state
                }
            }
        }
        // Handle old single area format (backward compatibility)
        elseif (isset($location['area']) && (int)$location['area'] === $property->area_id) {
            return 30; // Perfect area match
        }

        // Fallback to city/state matching for both formats
        if (isset($location['city']) && $property->area && (int)$location['city'] === $property->area->city_id) {
            return 20; // City match
        }

        if (isset($location['state']) && $property->area?->city && (int)$location['state'] === $property->area->city->state_id) {
            return 10; // State match
        }

        return 0; // No location match
    }

    /**
     * Calculate property category match score
     */
    private function calculateCategoryScore(Property $property, SavedSearch $search): float
    {
        // Priority 1: Check new property type system
        if ($search->selected_property_type) {
            if ($property->property_type_id == $search->selected_property_type) {
                // Also check listing type matches search intention
                if ($search->search_type) {
                    $listingTypeMap = [
                        'buy' => 'sale',
                        'rent' => 'rent',
                        'shortlet' => 'shortlet'
                    ];
                    $expectedListingType = $listingTypeMap[$search->search_type] ?? null;
                    if ($expectedListingType && $property->listing_type === $expectedListingType) {
                        return 25; // Perfect match: correct property type and listing type
                    } else if ($expectedListingType) {
                        return 15; // Good match: correct property type but wrong listing type
                    }
                }
                return 20; // Good match: correct property type, no listing type check
            }
            return 5; // Property type mismatch but still give some score
        }

        // Priority 2: Fallback to legacy property categories system
        if (!$search->property_categories) {
            return 12; // Neutral score if no preference at all
        }

        foreach ($search->property_categories as $category) {
            if ($this->propertyMatchesCategory($property, $category)) {
                return 25; // Perfect category match
            }

            // Check for compatible categories
            if ($this->propertyMatchesCompatibleCategory($property, $category)) {
                return 15; // Compatible category match
            }
        }

        return 0; // No category match
    }

    /**
     * Check if property matches a specific category
     */
    private function propertyMatchesCategory(Property $property, string $category): bool
    {
        $propertyTypeName = strtolower($property->propertyType->name ?? '');
        $listingType = $property->listing_type;

        switch ($category) {
            case 'house_rent':
                return str_contains($propertyTypeName, 'house') && $listingType === 'rent';

            case 'house_buy':
                return str_contains($propertyTypeName, 'house') && $listingType === 'sale';

            case 'land_buy':
                return str_contains($propertyTypeName, 'land') && $listingType === 'sale';

            case 'shop_rent':
                return in_array($propertyTypeName, ['commercial', 'shop']) && $listingType === 'rent';

            case 'shop_buy':
                return in_array($propertyTypeName, ['commercial', 'shop']) && $listingType === 'sale';

            default:
                return false;
        }
    }

    /**
     * Check if property matches a compatible category
     */
    private function propertyMatchesCompatibleCategory(Property $property, string $category): bool
    {
        $propertyTypeName = strtolower($property->propertyType->name ?? '');

        // Houses are compatible with apartments for rent/buy
        if (in_array($category, ['house_rent', 'house_buy'])) {
            return str_contains($propertyTypeName, 'apartment');
        }

        // Commercial spaces are somewhat compatible
        if (in_array($category, ['shop_rent', 'shop_buy'])) {
            return str_contains($propertyTypeName, 'office');
        }

        return false;
    }

    /**
     * Calculate budget match score
     */
    private function calculateBudgetScore(Property $property, SavedSearch $search): float
    {
        $price = $property->price;

        // Use budget from main fields or additional_filters
        $budgetMin = $search->budget_min;
        $budgetMax = $search->budget_max;

        if (!$budgetMin && !$budgetMax && $search->additional_filters) {
            $budgets = $search->additional_filters['budgets'] ?? [];

            // Priority 1: Use budget based on new property type system
            if ($search->selected_property_type && $search->search_type) {
                $budgetKey = $this->getBudgetKeyFromPropertyType($search->selected_property_type, $search->search_type);
                if ($budgetKey && isset($budgets[$budgetKey])) {
                    $budgetMin = $budgets[$budgetKey]['min'] ?? null;
                    $budgetMax = $budgets[$budgetKey]['max'] ?? null;
                }
            }

            // Priority 2: Fallback to legacy property categories system
            if (!$budgetMin && !$budgetMax) {
                foreach ($search->property_categories ?? [] as $category) {
                    if (isset($budgets[$category])) {
                        $budgetMin = $budgets[$category]['min'] ?? null;
                        $budgetMax = $budgets[$category]['max'] ?? null;
                        break;
                    }
                }
            }
        }

        if (!$budgetMin && !$budgetMax) {
            return 10; // Neutral score if no budget preference
        }

        // Perfect match - within budget range
        if (($budgetMin === null || $price >= $budgetMin) &&
            ($budgetMax === null || $price <= $budgetMax)) {
            return 20;
        }

        // Good match - within 10% of budget range
        $lowerBound = $budgetMin ? $budgetMin * 0.9 : 0;
        $upperBound = $budgetMax ? $budgetMax * 1.1 : PHP_FLOAT_MAX;

        if ($price >= $lowerBound && $price <= $upperBound) {
            return 15;
        }

        // Acceptable match - within 20% of budget range
        $lowerBound = $budgetMin ? $budgetMin * 0.8 : 0;
        $upperBound = $budgetMax ? $budgetMax * 1.2 : PHP_FLOAT_MAX;

        if ($price >= $lowerBound && $price <= $upperBound) {
            return 10;
        }

        return 0; // No budget match
    }

    /**
     * Calculate property subtype match score
     */
    private function calculateSubtypeScore(Property $property, SavedSearch $search): float
    {
        if (!$search->property_subtypes) {
            return 7; // Neutral score if no subtype preference
        }

        if (in_array($property->property_subtype_id, $search->property_subtypes)) {
            return 15; // Perfect subtype match
        }

        // Check for similar subtypes (e.g., 2BR vs 3BR)
        if ($this->areSubtypesSimilar($property->property_subtype_id, $search->property_subtypes)) {
            return 10;
        }

        return 0; // No subtype match
    }

    /**
     * Check if subtypes are similar (e.g., different bedroom counts)
     */
    private function areSubtypesSimilar(int $propertySubtypeId, array $searchSubtypes): bool
    {
        $propertySubtype = PropertySubtype::find($propertySubtypeId);
        if (!$propertySubtype) return false;

        foreach ($searchSubtypes as $searchSubtypeId) {
            $searchSubtype = PropertySubtype::find($searchSubtypeId);
            if (!$searchSubtype) continue;

            // Both are bedroom-based (1BR, 2BR, etc.)
            if (str_contains($propertySubtype->name, 'BR') && str_contains($searchSubtype->name, 'BR')) {
                return true;
            }

            // Both are the same type (Duplex, Terrace, etc.)
            $propertyWords = explode(' ', strtolower($propertySubtype->name));
            $searchWords = explode(' ', strtolower($searchSubtype->name));

            if (count(array_intersect($propertyWords, $searchWords)) > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate additional filters match score
     */
    private function calculateAdditionalFiltersScore(Property $property, SavedSearch $search): float
    {
        if (!$search->additional_filters) {
            return 5; // Neutral score if no additional filters
        }

        $score = 0;
        $maxScore = 10;
        $filters = $search->additional_filters;

        // Bedroom count filter
        if (isset($filters['bedrooms']) && $property->bedrooms) {
            if ($property->bedrooms >= $filters['bedrooms']) {
                $score += 3;
            }
        }

        // Bathroom count filter
        if (isset($filters['bathrooms']) && $property->bathrooms) {
            if ($property->bathrooms >= $filters['bathrooms']) {
                $score += 2;
            }
        }

        // Furnishing filter
        if (isset($filters['furnishing']) && $property->furnishing) {
            if ($property->furnishing === $filters['furnishing']) {
                $score += 2;
            }
        }

        // Property features filter
        if (isset($filters['features']) && is_array($filters['features'])) {
            $propertyFeatureIds = $property->features->pluck('id')->toArray();
            $matchingFeatures = array_intersect($filters['features'], $propertyFeatureIds);
            $score += min(count($matchingFeatures) * 0.5, 3); // Max 3 points for features
        }

        return min($score, $maxScore);
    }

    /**
     * Get match reasons for explanation
     */
    private function getMatchReasons(Property $property, SavedSearch $search, float $score): array
    {
        $reasons = [];

        // Location reasons - supports both old and new formats
        if ($search->location_preferences) {
            $location = $search->location_preferences;

            // Handle new multiple area format
            if (isset($location['area_selection_type'])) {
                if ($location['area_selection_type'] === 'specific' && isset($location['selected_areas']) && in_array($property->area_id, $location['selected_areas'])) {
                    $area = Area::find($property->area_id);
                    $reasons[] = "Located in your preferred area: " . ($area->name ?? 'N/A');
                } elseif ($location['area_selection_type'] === 'any' && isset($location['city']) && $property->area && $location['city'] === $property->area->city_id) {
                    $city = City::find($location['city']);
                    $reasons[] = "Located in your preferred city (any area): " . ($city->name ?? 'N/A');
                } elseif ($location['area_selection_type'] === 'all' && isset($location['city']) && $property->area && $location['city'] === $property->area->city_id) {
                    $city = City::find($location['city']);
                    $reasons[] = "Located in your preferred city (all areas): " . ($city->name ?? 'N/A');
                }
            }
            // Handle old single area format (backward compatibility)
            elseif (isset($location['area']) && $location['area'] === $property->area_id) {
                $area = Area::find($location['area']);
                $reasons[] = "Located in your preferred area: " . ($area->name ?? 'N/A');
            } elseif (isset($location['city']) && $property->area && $location['city'] === $property->area->city_id) {
                $city = City::find($location['city']);
                $reasons[] = "Located in your preferred city: " . ($city->name ?? 'N/A');
            }
        }

        // Budget reasons
        $budgetMin = $search->budget_min;
        $budgetMax = $search->budget_max;
        if ($budgetMin && $budgetMax && $property->price >= $budgetMin && $property->price <= $budgetMax) {
            $reasons[] = "Within your budget range: ₦" . number_format($budgetMin) . " - ₦" . number_format($budgetMax);
        }

        // Category reasons
        if ($search->property_categories) {
            foreach ($search->property_categories as $category) {
                if ($this->propertyMatchesCategory($property, $category)) {
                    $reasons[] = "Matches your preferred property type: " . ucwords(str_replace('_', ' ', $category));
                    break;
                }
            }
        }

        // Quality indicator
        if ($score >= self::PERFECT_MATCH_SCORE) {
            $reasons[] = "Perfect match for your criteria!";
        } elseif ($score >= 80) {
            $reasons[] = "Excellent match for your criteria";
        } elseif ($score >= self::MIN_MATCH_SCORE) {
            $reasons[] = "Good match for your criteria";
        }

        return $reasons;
    }

    /**
     * Get notification priority based on match score
     */
    private function getNotificationPriority(float $score): string
    {
        if ($score >= self::PERFECT_MATCH_SCORE) {
            return 'instant'; // Send immediately
        } elseif ($score >= 80) {
            return 'daily'; // Include in daily digest
        } else {
            return 'weekly'; // Include in weekly summary
        }
    }

    /**
     * Get budget key from property type and search type
     */
    private function getBudgetKeyFromPropertyType(int $propertyTypeId, string $searchType): ?string
    {
        // Determine budget key based on property type and search type
        switch ($propertyTypeId) {
            case 1: // Apartment
            case 2: // House
                return $searchType === 'buy' ? 'house_buy' : 'house_rent';
            case 3: // Land
                return 'land_buy';
            case 4: // Commercial
            case 5: // Office
            case 6: // Warehouse
                return $searchType === 'buy' ? 'shop_buy' : 'shop_rent';
            default:
                return null;
        }
    }

    /**
     * Log matching activity
     */
    private function log(string $level, string $message, array $context = []): void
    {
        Log::channel('daily')->{$level}("[SavedSearchMatcher] {$message}", $context);
    }
}