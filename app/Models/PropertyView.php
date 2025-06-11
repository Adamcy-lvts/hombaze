<?php

namespace App\Models;

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
        'user_agent',
        'session_id',
        'referrer',
        'device_type',
        'browser',
        'platform',
        'country',
        'city',
        'viewed_at',
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

    // Static Methods

    /**
     * Record a property view
     */
    public static function recordView(
        Property $property, 
        ?User $user = null, 
        string $ipAddress = null, 
        string $userAgent = null,
        string $sessionId = null,
        string $referrer = null
    ): self {
        return self::create([
            'property_id' => $property->id,
            'user_id' => $user?->id,
            'ip_address' => $ipAddress ?: request()->ip(),
            'user_agent' => $userAgent ?: request()->userAgent(),
            'session_id' => $sessionId ?: session()->getId(),
            'referrer' => $referrer ?: request()->header('referer'),
            'device_type' => self::detectDeviceType($userAgent ?: request()->userAgent()),
            'browser' => self::detectBrowser($userAgent ?: request()->userAgent()),
            'platform' => self::detectPlatform($userAgent ?: request()->userAgent()),
            'viewed_at' => now(),
        ]);
    }

    /**
     * Get analytics for a property
     */
    public static function getPropertyAnalytics(Property $property, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $views = self::where('property_id', $property->id)
                    ->where('viewed_at', '>=', $startDate)
                    ->get();

        return [
            'total_views' => $views->count(),
            'unique_views' => $views->unique(function ($view) {
                return $view->user_id ?: $view->ip_address;
            })->count(),
            'authenticated_views' => $views->where('user_id', '!=', null)->count(),
            'anonymous_views' => $views->where('user_id', null)->count(),
            'daily_views' => $views->groupBy(function ($view) {
                return $view->viewed_at->toDateString();
            })->map->count(),
            'top_referrers' => $views->whereNotNull('referrer')
                                  ->groupBy('referrer')
                                  ->map->count()
                                  ->sortDesc()
                                  ->take(5),
            'device_breakdown' => $views->groupBy('device_type')->map->count(),
            'browser_breakdown' => $views->groupBy('browser')->map->count(),
        ];
    }

    /**
     * Get trending properties
     */
    public static function getTrendingProperties(int $days = 7, int $limit = 10): \Illuminate\Support\Collection
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

    /**
     * Check if this is a unique view
     */
    public function isUniqueView(): bool
    {
        $query = self::where('property_id', $this->property_id)
                    ->where('viewed_at', '>=', now()->subHours(24));

        if ($this->user_id) {
            $query->where('user_id', $this->user_id);
        } else {
            $query->where('ip_address', $this->ip_address);
        }

        return $query->where('id', '!=', $this->id)->doesntExist();
    }
}
