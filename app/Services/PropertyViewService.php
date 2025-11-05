<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyView;
use App\Jobs\UpdatePropertyViewCountJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PropertyViewService
{
    /**
     * Track a property view with authentication and duplicate prevention
     */
    public function trackView(Property $property, ?Request $request = null): bool
    {
        $request = $request ?: request();

        // Skip tracking for admin users or property owners/agents
        if ($this->shouldSkipTracking($property)) {
            return false;
        }

        // Get user details
        $userId = Auth::id();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Bot filtering disabled - track all legitimate user visits
        // if ($this->isBotTraffic($userAgent, $ipAddress)) {
        //     Log::info("Skipped bot traffic for property {$property->id}");
        //     return false;
        // }

        // Attempt to record the view
        $propertyView = PropertyView::recordView(
            $property->id,
            $userId,
            $ipAddress,
            $userAgent,
            session()->getId()
        );

        // If view was recorded (not a duplicate), update the counter
        if ($propertyView) {
            // Dispatch job to update cached counter
            UpdatePropertyViewCountJob::dispatch($property->id);

            Log::info("Recorded new view for property {$property->id}" .
                     ($userId ? " by user {$userId}" : " by anonymous user"));

            return true;
        }

        return false; // Duplicate view
    }

    /**
     * Get view count for a property
     */
    public function getViewCount(Property $property): int
    {
        return $property->view_count ?? 0;
    }

    /**
     * Get recent views analytics for a property
     */
    public function getViewAnalytics(Property $property, int $days = 30): array
    {
        return PropertyView::getPropertyAnalytics($property->id, $days);
    }

    /**
     * Check if tracking should be skipped for this user/property
     */
    private function shouldSkipTracking(Property $property): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false; // Track anonymous users
        }

        // Skip if user is the property owner, agent, or agency owner
        if ($property->owner_id === $user->id ||
            $property->agent_id === $user->id ||
            ($property->agency_id && $property->agency_id === $user->agency_id)) {
            return true;
        }

        // Skip if user is admin or staff
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        return false;
    }

    /**
     * Detect if the request is from a bot/crawler
     */
    private function isBotTraffic(string $userAgent, string $ipAddress): bool
    {
        // Common bot user agents
        $botPatterns = [
            '/bot/i',
            '/crawler/i',
            '/spider/i',
            '/scraper/i',
            '/facebook/i',
            '/twitter/i',
            '/linkedin/i',
            '/whatsapp/i',
            '/telegram/i',
            '/googlebot/i',
            '/bingbot/i',
            '/slurp/i',
            '/duckduckbot/i',
            '/baiduspider/i',
            '/yandexbot/i',
            '/facebookexternalhit/i',
            '/twitterbot/i',
            '/linkedinbot/i',
            '/pinterest/i',
            '/curl/i',
            '/wget/i',
            '/python/i',
            '/java/i',
            '/node/i',
            '/postman/i',
            '/insomnia/i',
        ];

        foreach ($botPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return true;
            }
        }

        // Check for private/internal IP addresses
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return true; // Local/private IP
        }

        // Check for empty or suspicious user agents
        if (empty($userAgent) || strlen($userAgent) < 10) {
            return true;
        }

        return false;
    }

    /**
     * Get trending properties based on recent views
     */
    public function getTrendingProperties(int $days = 7, int $limit = 10): \Illuminate\Support\Collection
    {
        return PropertyView::getTrendingProperties($days, $limit);
    }

    /**
     * Batch update view counts for multiple properties
     */
    public function batchUpdateViewCounts(array $propertyIds): void
    {
        foreach ($propertyIds as $propertyId) {
            UpdatePropertyViewCountJob::dispatch($propertyId);
        }
    }
}