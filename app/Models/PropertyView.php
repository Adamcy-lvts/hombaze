<?php

namespace App\Models;

use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class PropertyView extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'ip_address',
        'ip_address_hash',
        'user_agent',
        'user_agent_hash',
        'session_id',
        'fingerprint',
        'referrer',
        'device_type',
        'browser',
        'platform',
        'country',
        'city',
        'viewed_at',
        'source',
        'smart_search_match_id',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    // Relationships

    /**
     * Get the property that was viewed
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user who viewed the property (nullable for anonymous views)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the SmartSearch match associated with this view (for claim tracking)
     */
    public function smartSearchMatch(): BelongsTo
    {
        return $this->belongsTo(SmartSearchMatch::class);
    }

    // Scopes

    /**
     * Scope for authenticated user views
     */
    public function scopeAuthenticated($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Scope for anonymous views
     */
    public function scopeAnonymous($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Scope for views within date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('viewed_at', [$startDate, $endDate]);
    }

    /**
     * Scope for today's views
     */
    public function scopeToday($query)
    {
        return $query->whereDate('viewed_at', today());
    }

    /**
     * Scope for this week's views
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('viewed_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for this month's views
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('viewed_at', now()->month)
                    ->whereYear('viewed_at', now()->year);
    }

    /**
     * Scope for unique views (one per user/IP per property)
     */
    public function scopeUnique($query)
    {
        return $query->select('property_id', 'user_id', 'ip_address')
                    ->distinct();
    }

    /**
     * Scope for views from SmartSearch
     */
    public function scopeFromSmartSearch($query)
    {
        return $query->where('source', 'smartsearch');
    }

    /**
     * Scope for views since a specific date
     */
    public function scopeSince($query, $date)
    {
        return $query->where('viewed_at', '>=', $date);
    }

    // Static Methods

    /**
     * Create a unique fingerprint for view tracking
     */
    public static function createFingerprint(int $propertyId, string $ipAddress, string $userAgent): string
    {
        return hash('sha256', $ipAddress . substr($userAgent, 0, 100) . $propertyId);
    }

    /**
     * Hash IP address for privacy compliance
     */
    public static function hashIpAddress(string $ipAddress): string
    {
        return hash('sha256', $ipAddress . config('app.key'));
    }

    /**
     * Check if a view already exists within the last 24 hours
     */
    public static function hasRecentView(string $fingerprint): bool
    {
        return self::where('fingerprint', $fingerprint)
                  ->where('viewed_at', '>=', now()->subHours(24))
                  ->exists();
    }

    /**
     * Record a property view with duplicate prevention
     */
    public static function recordView(
        int $propertyId,
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?string $sessionId = null
    ): ?self {
        $ipAddress = $ipAddress ?: request()->ip();
        $userAgent = $userAgent ?: request()->userAgent();
        $sessionId = $sessionId ?: session()->getId();

        // Create unique fingerprint
        $fingerprint = self::createFingerprint($propertyId, $ipAddress, $userAgent);

        // Check for recent views
        if (self::hasRecentView($fingerprint)) {
            return null; // Duplicate view, not recorded
        }

        try {
            return self::create([
                'property_id' => $propertyId,
                'user_id' => $userId,
                'ip_address' => $ipAddress, // Store original for existing functionality
                'ip_address_hash' => self::hashIpAddress($ipAddress),
                'user_agent' => $userAgent, // Store original for existing functionality
                'user_agent_hash' => hash('sha256', $userAgent),
                'session_id' => $sessionId,
                'fingerprint' => $fingerprint,
                'referrer' => request()->header('referer'),
                'device_type' => self::detectDeviceType($userAgent),
                'browser' => self::detectBrowser($userAgent),
                'platform' => self::detectPlatform($userAgent),
                'viewed_at' => now(),
            ]);
        } catch (QueryException $e) {
            // Handle race condition where duplicate fingerprint is inserted
            if ($e->getCode() == 23000) { // Integrity constraint violation
                return null;
            }
            throw $e;
        }
    }

    /**
     * Get analytics for a property
     */
    public static function getPropertyAnalytics(int $propertyId, int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $views = self::where('property_id', $propertyId)
                    ->where('viewed_at', '>=', $startDate)
                    ->get();

        return [
            'total_views' => $views->count(),
            'unique_views' => $views->unique('fingerprint')->count(),
            'authenticated_views' => $views->whereNotNull('user_id')->count(),
            'anonymous_views' => $views->whereNull('user_id')->count(),
            'daily_views' => $views->groupBy(function ($view) {
                return $view->viewed_at->toDateString();
            })->map->count(),
        ];
    }

    /**
     * Get trending properties
     */
    public static function getTrendingProperties(int $days = 7, int $limit = 10): Collection
    {
        return self::select('property_id', DB::raw('COUNT(*) as view_count'))
                  ->where('viewed_at', '>=', now()->subDays($days))
                  ->groupBy('property_id')
                  ->orderByDesc('view_count')
                  ->with('property')
                  ->limit($limit)
                  ->get();
    }

    // Helper Methods

    /**
     * Detect device type from user agent
     */
    private static function detectDeviceType(string $userAgent): string
    {
        if (preg_match('/mobile|android|iphone|ipad|tablet/i', $userAgent)) {
            if (preg_match('/ipad|tablet/i', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Detect browser from user agent
     */
    private static function detectBrowser(string $userAgent): string
    {
        if (preg_match('/chrome/i', $userAgent)) return 'Chrome';
        if (preg_match('/firefox/i', $userAgent)) return 'Firefox';
        if (preg_match('/safari/i', $userAgent)) return 'Safari';
        if (preg_match('/edge/i', $userAgent)) return 'Edge';
        if (preg_match('/opera/i', $userAgent)) return 'Opera';
        return 'Unknown';
    }

    /**
     * Detect platform from user agent
     */
    private static function detectPlatform(string $userAgent): string
    {
        if (preg_match('/windows/i', $userAgent)) return 'Windows';
        if (preg_match('/macintosh|mac os/i', $userAgent)) return 'macOS';
        if (preg_match('/linux/i', $userAgent)) return 'Linux';
        if (preg_match('/android/i', $userAgent)) return 'Android';
        if (preg_match('/iphone|ipad|ipod/i', $userAgent)) return 'iOS';
        return 'Unknown';
    }

    // SmartSearch Claim Detection Methods

    /**
     * Check if a user has viewed a property since a specific date
     * Used for SmartSearch claim detection
     */
    public static function hasUserViewedSince(int $propertyId, int $userId, $since): bool
    {
        return self::where('property_id', $propertyId)
            ->where('user_id', $userId)
            ->where('viewed_at', '>=', $since)
            ->exists();
    }

    /**
     * Record a SmartSearch-triggered view
     */
    public static function recordSmartSearchView(
        int $propertyId,
        int $userId,
        ?int $matchId = null
    ): ?self {
        return self::recordView(
            propertyId: $propertyId,
            userId: $userId,
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
            sessionId: session()->getId()
        );
    }
}
