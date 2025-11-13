<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavedSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'description', 'search_type',
        'selected_property_type', 'property_categories', 'location_preferences', 'property_subtypes',
        'budget_min', 'budget_max', 'additional_filters',
        'notification_settings', 'is_active', 'is_default',
        // Legacy fields for backward compatibility
        'search_criteria', 'alert_frequency', 'last_alerted_at'
    ];

    protected $casts = [
        'property_categories' => 'array',
        'location_preferences' => 'array',
        'property_subtypes' => 'array',
        'additional_filters' => 'array',
        'notification_settings' => 'array',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        // Legacy fields
        'search_criteria' => 'array',
        'last_alerted_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('search_type', $type);
    }

    // Helper methods for display
    public function getFormattedBudgetAttribute(): string
    {
        $budgetMin = $this->budget_min;
        $budgetMax = $this->budget_max;

        // Check additional_filters for budget information if not in main columns
        if (!$budgetMin && !$budgetMax && $this->additional_filters && isset($this->additional_filters['budgets'])) {
            $budgets = $this->additional_filters['budgets'];

            // Priority 1: Use budget based on new property type system
            if ($this->selected_property_type) {
                // Map property type to budget category
                $budgetKey = $this->getBudgetKeyFromPropertyType();
                if ($budgetKey && isset($budgets[$budgetKey])) {
                    $budgetMin = $budgets[$budgetKey]['min'] ?? null;
                    $budgetMax = $budgets[$budgetKey]['max'] ?? null;
                }
            }
            // Priority 2: Fallback to old property categories system
            elseif ($this->property_categories) {
                foreach ($this->property_categories as $category) {
                    if (isset($budgets[$category])) {
                        $budgetMin = $budgets[$category]['min'] ?? null;
                        $budgetMax = $budgets[$category]['max'] ?? null;
                        break; // Use first found budget
                    }
                }
            }
        }

        if (!$budgetMin && !$budgetMax) {
            return 'Any budget';
        }

        $min = $budgetMin ? '₦' . number_format($budgetMin) : 'No min';
        $max = $budgetMax ? '₦' . number_format($budgetMax) : 'No max';

        return "{$min} - {$max}";
    }

    private function getBudgetKeyFromPropertyType(): ?string
    {
        if (!$this->selected_property_type) {
            return null;
        }

        // Determine budget key based on property type and search type
        $searchType = $this->search_type ?? 'rent';

        switch ($this->selected_property_type) {
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

    public function getLocationDisplayAttribute(): string
    {
        if (!$this->location_preferences) {
            return 'Any location';
        }

        $location = $this->location_preferences;
        $parts = [];

        // Handle new multiple area selection
        if (isset($location['area_selection_type'])) {
            if ($location['area_selection_type'] === 'any') {
                $parts[] = 'Any area';
            } elseif ($location['area_selection_type'] === 'all') {
                $parts[] = 'All areas';
            } elseif ($location['area_selection_type'] === 'specific' && isset($location['selected_areas']) && !empty($location['selected_areas'])) {
                $areas = Area::whereIn('id', $location['selected_areas'])->pluck('name')->toArray();
                if (count($areas) > 3) {
                    $displayAreas = array_slice($areas, 0, 3);
                    $parts[] = implode(', ', $displayAreas) . ' +' . (count($areas) - 3) . ' more';
                } else {
                    $parts[] = implode(', ', $areas);
                }
            }
        }

        // Fallback for old single area selection (backward compatibility)
        elseif (isset($location['area']) && $location['area']) {
            $area = Area::find($location['area']);
            if ($area) $parts[] = $area->name;
        }

        // Add city and state
        if (isset($location['city']) && $location['city']) {
            $city = City::find($location['city']);
            if ($city) $parts[] = $city->name;
        }

        if (isset($location['state']) && $location['state']) {
            $state = State::find($location['state']);
            if ($state) $parts[] = $state->name;
        }

        return !empty($parts) ? implode(', ', $parts) : 'Any location';
    }

    public function getPropertyTypesDisplayAttribute(): string
    {
        // Priority 1: Use new property type system
        if ($this->selected_property_type) {
            $propertyType = PropertyType::find($this->selected_property_type);
            if ($propertyType) {
                return $propertyType->name;
            }
        }

        // Priority 2: Fallback to old property categories system
        if ($this->property_categories) {
            $types = [];
            foreach ($this->property_categories as $category) {
                switch ($category) {
                    case 'house_rent':
                        $types[] = 'Houses (Rent)';
                        break;
                    case 'house_buy':
                        $types[] = 'Houses (Buy)';
                        break;
                    case 'land_buy':
                        $types[] = 'Land';
                        break;
                    case 'shop_rent':
                        $types[] = 'Shops (Rent)';
                        break;
                    case 'shop_buy':
                        $types[] = 'Shops (Buy)';
                        break;
                }
            }

            return !empty($types) ? implode(', ', $types) : 'Any property type';
        }

        return 'Any property type';
    }
}
