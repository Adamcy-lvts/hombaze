<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'interested_in',
        'budget_min',
        'budget_max',
        'preferred_property_types',
        'preferred_locations',
        'notification_preferences',
        'email_alerts',
        'sms_alerts',
        'whatsapp_alerts',
        'search_history',
        'viewed_properties',
        'last_search_at',
        'last_recommendation_sent_at',

        // New simplified property preferences
        'property_categories',
        'apartment_subtypes',
        'house_subtypes',
        'land_sizes',
        'shop_selected',
        'budgets',
    ];

    protected $casts = [
        'interested_in' => 'array',
        'preferred_property_types' => 'array',
        'preferred_locations' => 'array',
        'notification_preferences' => 'array',
        'search_history' => 'array',
        'viewed_properties' => 'array',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'email_alerts' => 'boolean',
        'sms_alerts' => 'boolean',
        'whatsapp_alerts' => 'boolean',
        'last_search_at' => 'datetime',
        'last_recommendation_sent_at' => 'datetime',

        // New simplified property preferences
        'property_categories' => 'array',
        'apartment_subtypes' => 'array',
        'house_subtypes' => 'array',
        'land_sizes' => 'array',
        'shop_selected' => 'boolean',
        'budgets' => 'array',
    ];

    /**
     * Relationship: User that this profile belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if customer is interested in buying
     */
    public function interestedInBuying(): bool
    {
        return in_array('buying', $this->interested_in ?? []);
    }

    /**
     * Check if customer is interested in renting
     */
    public function interestedInRenting(): bool
    {
        return in_array('renting', $this->interested_in ?? []);
    }

    /**
     * Check if customer is interested in shortlet
     */
    public function interestedInShortlet(): bool
    {
        return in_array('shortlet', $this->interested_in ?? []);
    }

    /**
     * Get budget range as formatted string
     */
    public function getBudgetRangeAttribute(): string
    {
        if (!$this->budget_min && !$this->budget_max) {
            return 'No budget set';
        }

        if ($this->budget_min && $this->budget_max) {
            return '₦' . number_format($this->budget_min) . ' - ₦' . number_format($this->budget_max);
        }

        if ($this->budget_min) {
            return 'From ₦' . number_format($this->budget_min);
        }

        return 'Up to ₦' . number_format($this->budget_max);
    }

    /**
     * Check if property matches customer preferences
     */
    public function matchesProperty(Property $property): bool
    {
        // Check budget range
        if ($this->budget_min && $property->price < $this->budget_min) {
            return false;
        }

        if ($this->budget_max && $property->price > $this->budget_max) {
            return false;
        }

        // Check property type preferences
        if (!empty($this->preferred_property_types) &&
            !in_array($property->property_type_id, $this->preferred_property_types)) {
            return false;
        }

        // Check location preferences
        if (!empty($this->preferred_locations)) {
            $locationMatch = in_array($property->city_id, $this->preferred_locations) ||
                           in_array($property->area_id, $this->preferred_locations);
            if (!$locationMatch) {
                return false;
            }
        }

        // Check listing type matches interests
        $interests = $this->interested_in ?? [];
        if (!empty($interests)) {
            $listingTypeMatch = false;

            if (in_array('buying', $interests) && $property->listing_type === 'sale') {
                $listingTypeMatch = true;
            }

            if (in_array('renting', $interests) && in_array($property->listing_type, ['rent', 'lease'])) {
                $listingTypeMatch = true;
            }

            if (in_array('shortlet', $interests) && $property->listing_type === 'shortlet') {
                $listingTypeMatch = true;
            }

            if (!$listingTypeMatch) {
                return false;
            }
        }

        return true;
    }

    /**
     * Add property to search history
     */
    public function addToSearchHistory(array $searchParams): void
    {
        $history = $this->search_history ?? [];

        // Add new search to beginning of array
        array_unshift($history, [
            'params' => $searchParams,
            'timestamp' => now()->toISOString(),
        ]);

        // Keep only last 20 searches
        $history = array_slice($history, 0, 20);

        $this->update([
            'search_history' => $history,
            'last_search_at' => now(),
        ]);
    }

    /**
     * Add property to viewed properties
     */
    public function addViewedProperty(int $propertyId): void
    {
        $viewed = $this->viewed_properties ?? [];

        // Remove if already exists to move to front
        $viewed = array_filter($viewed, fn($id) => $id !== $propertyId);

        // Add to beginning
        array_unshift($viewed, $propertyId);

        // Keep only last 50 viewed properties
        $viewed = array_slice($viewed, 0, 50);

        $this->update(['viewed_properties' => $viewed]);
    }

    /**
     * Get interests as formatted string
     */
    public function getInterestsStringAttribute(): string
    {
        if (empty($this->interested_in)) {
            return 'No preferences set';
        }

        return implode(', ', array_map('ucfirst', $this->interested_in));
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(array $preferences): void
    {
        $this->update([
            'email_alerts' => $preferences['email_alerts'] ?? $this->email_alerts,
            'sms_alerts' => $preferences['sms_alerts'] ?? $this->sms_alerts,
            'whatsapp_alerts' => $preferences['whatsapp_alerts'] ?? $this->whatsapp_alerts,
            'notification_preferences' => array_merge(
                $this->notification_preferences ?? [],
                $preferences['additional'] ?? []
            ),
        ]);
    }

    /**
     * Get user's total engagement score based on interactions
     */
    public function getEngagementScore(int $days = 30): float
    {
        return PropertyInteraction::getUserEngagementScore($this->user_id, $days);
    }

    /**
     * Get user's preferred property features based on interaction history
     */
    public function getPreferredFeatures(): array
    {
        $interactions = PropertyInteraction::forUser($this->user_id)
            ->highEngagement()
            ->with('property.features')
            ->get();

        $featureScores = [];

        foreach ($interactions as $interaction) {
            $weight = $interaction->getTimeDecayedScore();
            foreach ($interaction->property->features ?? [] as $feature) {
                $featureScores[$feature->id] = ($featureScores[$feature->id] ?? 0) + $weight;
            }
        }

        arsort($featureScores);
        return array_keys(array_slice($featureScores, 0, 10)); // Top 10 features
    }

    /**
     * Get user's preferred locations based on interaction history
     */
    public function getPreferredLocationsFromBehavior(): array
    {
        $interactions = PropertyInteraction::forUser($this->user_id)
            ->highEngagement()
            ->with('property.area.city.state')
            ->get();

        $locationScores = [
            'areas' => [],
            'cities' => [],
            'states' => []
        ];

        foreach ($interactions as $interaction) {
            $weight = $interaction->getTimeDecayedScore();
            $property = $interaction->property;

            if ($property->area_id) {
                $locationScores['areas'][$property->area_id] =
                    ($locationScores['areas'][$property->area_id] ?? 0) + $weight;
            }

            if ($property->area?->city_id) {
                $locationScores['cities'][$property->area->city_id] =
                    ($locationScores['cities'][$property->area->city_id] ?? 0) + $weight;
            }

            if ($property->area?->city?->state_id) {
                $locationScores['states'][$property->area->city->state_id] =
                    ($locationScores['states'][$property->area->city->state_id] ?? 0) + $weight;
            }
        }

        // Sort by score and return top locations
        foreach ($locationScores as &$scores) {
            arsort($scores);
        }

        return $locationScores;
    }

    /**
     * Get user's preferred price range based on interaction history
     */
    public function getPreferredPriceRange(): array
    {
        $interactions = PropertyInteraction::forUser($this->user_id)
            ->highEngagement()
            ->with('property')
            ->get();

        $prices = [];
        foreach ($interactions as $interaction) {
            $weight = $interaction->getTimeDecayedScore();
            // Weight the price by interaction strength
            for ($i = 0; $i < $weight; $i++) {
                $prices[] = $interaction->property->price;
            }
        }

        if (empty($prices)) {
            return ['min' => null, 'max' => null];
        }

        sort($prices);
        $count = count($prices);

        // Use 25th and 75th percentile as preferred range
        $minIndex = (int) ($count * 0.25);
        $maxIndex = (int) ($count * 0.75);

        return [
            'min' => $prices[$minIndex] ?? null,
            'max' => $prices[$maxIndex] ?? null,
            'median' => $prices[(int) ($count / 2)] ?? null
        ];
    }

    /**
     * Get similar users based on interaction patterns
     */
    public function getSimilarUsers(int $limit = 10): array
    {
        // Get this user's highly interacted properties
        $userProperties = PropertyInteraction::forUser($this->user_id)
            ->highEngagement()
            ->pluck('property_id')
            ->toArray();

        if (empty($userProperties)) {
            return [];
        }

        // Find users who also interacted with similar properties
        $similarUsers = PropertyInteraction::whereIn('property_id', $userProperties)
            ->where('user_id', '!=', $this->user_id)
            ->highEngagement()
            ->selectRaw('user_id, COUNT(*) as shared_properties, SUM(interaction_score) as total_score')
            ->groupBy('user_id')
            ->orderByDesc('total_score')
            ->limit($limit)
            ->get()
            ->toArray();

        return $similarUsers;
    }

    /**
     * Calculate affinity score for a specific property
     */
    public function getPropertyAffinityScore(Property $property): float
    {
        $score = 0;

        // Direct interaction history
        $directScore = PropertyInteraction::getUserPropertyAffinity($this->user_id, $property->id);
        $score += $directScore * 3; // Weight direct interactions heavily

        // Similar properties score
        $similarProperties = Property::where('property_type_id', $property->property_type_id)
            ->where('area_id', $property->area_id)
            ->where('id', '!=', $property->id)
            ->pluck('id');

        foreach ($similarProperties as $similarPropertyId) {
            $similarScore = PropertyInteraction::getUserPropertyAffinity($this->user_id, $similarPropertyId);
            $score += $similarScore * 0.3; // Lower weight for similar properties
        }

        // Price range compatibility
        $preferredRange = $this->getPreferredPriceRange();
        if ($preferredRange['min'] && $preferredRange['max']) {
            $priceInRange = $property->price >= $preferredRange['min'] &&
                           $property->price <= $preferredRange['max'];
            if ($priceInRange) {
                $score += 5;
            }
        }

        // Location preference match
        $locationPrefs = $this->getPreferredLocationsFromBehavior();
        if (isset($locationPrefs['areas'][$property->area_id])) {
            $score += $locationPrefs['areas'][$property->area_id] * 0.5;
        }

        return round($score, 2);
    }
}
