<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmartSearch extends Model
{
    use HasFactory;

    protected $table = 'smart_searches';

    // Tier constants
    public const TIER_STARTER = 'starter';
    public const TIER_STANDARD = 'standard';
    public const TIER_PRIORITY = 'priority';
    public const TIER_VIP = 'vip';

    // Tier configurations
    public const TIER_CONFIGS = [
        self::TIER_STARTER => [
            'name' => 'Starter',
            'price' => 10000,
            'searches' => 1,
            'duration_days' => 60,
            'channels' => ['email'],
            'priority_order' => 4,
            'delay_hours' => 48, // After VIP cascade
            'description' => 'Perfect for first-time searchers',
        ],
        self::TIER_STANDARD => [
            'name' => 'Standard',
            'price' => 20000,
            'searches' => 3,
            'duration_days' => 90,
            'channels' => ['email', 'whatsapp'],
            'priority_order' => 3,
            'delay_hours' => 24, // After VIP cascade
            'description' => 'Most popular choice',
        ],
        self::TIER_PRIORITY => [
            'name' => 'Priority',
            'price' => 35000,
            'searches' => 5,
            'duration_days' => 90,
            'channels' => ['email', 'whatsapp', 'sms'],
            'priority_order' => 2,
            'delay_hours' => 0, // After VIP cascade completes
            'description' => 'Get matches before standard users',
        ],
        self::TIER_VIP => [
            'name' => 'VIP',
            'price' => 50000,
            'searches' => 999, // Unlimited
            'duration_days' => 120,
            'channels' => ['email', 'whatsapp', 'sms'],
            'priority_order' => 1,
            'delay_hours' => 0,
            'exclusive_window_hours' => 3,
            'description' => 'First Dibs - exclusive 3-hour access to new matches',
        ],
    ];

    // Renewal discount percentage
    public const RENEWAL_DISCOUNT_PERCENT = 50;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'search_type',
        'selected_property_type',
        'property_categories',
        'location_preferences',
        'property_subtypes',
        'budget_min',
        'budget_max',
        'additional_filters',
        'notification_settings',
        'is_active',
        'is_default',
        // Tier fields
        'tier',
        'expires_at',
        'purchased_at',
        'purchase_reference',
        'matches_sent',
        'last_match_at',
        'is_expired',
        'is_paused',
        // Legacy fields for backward compatibility
        'search_criteria',
        'alert_frequency',
        'last_alerted_at',
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
        'is_expired' => 'boolean',
        'is_paused' => 'boolean',
        'expires_at' => 'datetime',
        'purchased_at' => 'datetime',
        'last_match_at' => 'datetime',
        'matches_sent' => 'integer',
        // Legacy fields
        'search_criteria' => 'array',
        'last_alerted_at' => 'datetime',
    ];

    // =========================================
    // Relationships
    // =========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'selected_property_type');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(SmartSearchMatch::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(SmartSearchSubscription::class, 'user_id', 'user_id')
            ->where('tier', $this->tier)
            ->where('payment_status', 'paid')
            ->latest();
    }

    // =========================================
    // Scopes
    // =========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('is_expired', false)
            ->where('is_paused', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('search_type', $type);
    }

    public function scopeByTier($query, string $tier)
    {
        return $query->where('tier', $tier);
    }

    public function scopeVip($query)
    {
        return $query->where('tier', self::TIER_VIP);
    }

    public function scopePriority($query)
    {
        return $query->where('tier', self::TIER_PRIORITY);
    }

    public function scopeStandard($query)
    {
        return $query->where('tier', self::TIER_STANDARD);
    }

    public function scopeStarter($query)
    {
        return $query->where('tier', self::TIER_STARTER);
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('is_expired', false)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    // =========================================
    // Tier Helper Methods
    // =========================================

    public function isVip(): bool
    {
        return $this->tier === self::TIER_VIP;
    }

    public function isPriority(): bool
    {
        return $this->tier === self::TIER_PRIORITY;
    }

    public function isStandard(): bool
    {
        return $this->tier === self::TIER_STANDARD;
    }

    public function isStarter(): bool
    {
        return $this->tier === self::TIER_STARTER;
    }

    public function getTierConfig(): array
    {
        return self::TIER_CONFIGS[$this->tier] ?? self::TIER_CONFIGS[self::TIER_STARTER];
    }

    public function getTierName(): string
    {
        return $this->getTierConfig()['name'] ?? 'Unknown';
    }

    public function getTierPrice(): int
    {
        return $this->getTierConfig()['price'] ?? 10000;
    }

    public function getNotificationChannels(): array
    {
        return $this->getTierConfig()['channels'] ?? ['email'];
    }

    public function getPriorityOrder(): int
    {
        return $this->getTierConfig()['priority_order'] ?? 4;
    }

    public function getDelayHours(): int
    {
        return $this->getTierConfig()['delay_hours'] ?? 48;
    }

    public function getExclusiveWindowHours(): int
    {
        return $this->getTierConfig()['exclusive_window_hours'] ?? 0;
    }

    public function hasExclusiveWindow(): bool
    {
        return $this->isVip() && $this->getExclusiveWindowHours() > 0;
    }

    // =========================================
    // Expiration & Renewal Methods
    // =========================================

    public function isExpired(): bool
    {
        return $this->is_expired ||
            ($this->expires_at && $this->expires_at->isPast());
    }

    public function daysRemaining(): int
    {
        if (!$this->expires_at) {
            return 0;
        }

        return max(0, (int) now()->diffInDays($this->expires_at, false));
    }

    public function canRenew(): bool
    {
        // Can renew if within 14 days of expiry or already expired
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->isBetween(now()->subDays(30), now()->addDays(14));
    }

    public function getRenewalPrice(): int
    {
        $basePrice = $this->getTierPrice();
        $discount = self::RENEWAL_DISCOUNT_PERCENT / 100;

        return (int) ($basePrice * (1 - $discount));
    }

    public function getFormattedRenewalPrice(): string
    {
        return '₦' . number_format($this->getRenewalPrice());
    }

    public function extendDuration(int $days = 30): void
    {
        $newExpiry = $this->expires_at && $this->expires_at->isFuture()
            ? $this->expires_at->addDays($days)
            : now()->addDays($days);

        $this->update([
            'expires_at' => $newExpiry,
            'is_expired' => false,
            'is_active' => true,
        ]);
    }

    // =========================================
    // Match Tracking Methods
    // =========================================

    public function incrementMatchesSent(int $count = 1): void
    {
        $this->increment('matches_sent', $count);
        $this->update(['last_match_at' => now()]);
    }

    public function hasReceivedMatches(): bool
    {
        return $this->matches_sent > 0;
    }

    // =========================================
    // Display Helper Methods
    // =========================================

    public function getFormattedBudgetAttribute(): string
    {
        $budgetMin = $this->budget_min;
        $budgetMax = $this->budget_max;

        // Check additional_filters for budget information if not in main columns
        if (!$budgetMin && !$budgetMax && $this->additional_filters && isset($this->additional_filters['budgets'])) {
            $budgets = $this->additional_filters['budgets'];

            // Priority 1: Use budget based on new property type system
            if ($this->selected_property_type) {
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
                        break;
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
            if ($area) {
                $parts[] = $area->name;
            }
        }

        // Add city and state
        if (isset($location['city']) && $location['city']) {
            $city = City::find($location['city']);
            if ($city) {
                $parts[] = $city->name;
            }
        }

        if (isset($location['state']) && $location['state']) {
            $state = State::find($location['state']);
            if ($state) {
                $parts[] = $state->name;
            }
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

    public function getStatusBadgeAttribute(): array
    {
        if ($this->is_expired || $this->isExpired()) {
            return ['label' => 'Expired', 'color' => 'danger'];
        }

        if ($this->is_paused) {
            return ['label' => 'Paused', 'color' => 'warning'];
        }

        if (!$this->is_active) {
            return ['label' => 'Inactive', 'color' => 'gray'];
        }

        $daysLeft = $this->daysRemaining();
        if ($daysLeft <= 7) {
            return ['label' => "Expiring in {$daysLeft}d", 'color' => 'warning'];
        }

        return ['label' => 'Active', 'color' => 'success'];
    }

    public function getTierBadgeAttribute(): array
    {
        $colors = [
            self::TIER_VIP => 'purple',
            self::TIER_PRIORITY => 'blue',
            self::TIER_STANDARD => 'green',
            self::TIER_STARTER => 'gray',
        ];

        return [
            'label' => $this->getTierName(),
            'color' => $colors[$this->tier] ?? 'gray',
        ];
    }

    // =========================================
    // Static Helper Methods
    // =========================================

    public static function getTierOptions(): array
    {
        return collect(self::TIER_CONFIGS)->map(function ($config, $key) {
            return [
                'value' => $key,
                'label' => $config['name'],
                'price' => $config['price'],
                'formatted_price' => '₦' . number_format($config['price']),
                'searches' => $config['searches'],
                'duration_days' => $config['duration_days'],
                'channels' => $config['channels'],
                'description' => $config['description'],
            ];
        })->values()->toArray();
    }

    public static function getTierBySlug(string $slug): ?array
    {
        return self::TIER_CONFIGS[$slug] ?? null;
    }
}
