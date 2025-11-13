<?php
// app/Http/Controllers/LandingController.php

namespace App\Http\Controllers;

use Exception;
use App\Models\Property;
use App\Models\User;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    /**
     * Display the landing page with statistics and featured content
     */
    public function index()
    {
        // Cache statistics for better performance
        $stats = Cache::remember('landing_stats', 3600, function () {
            return [
                'properties' => Property::where('status', 'available')->where('is_published', true)->count(),
                'clients' => User::whereHas('roles', function ($query) {
                    $query->where('name', 'tenant');
                })->count(),
                'agents' => User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['agent', 'landlord']);
                })->where('is_verified', true)->count(),
                'agencies' => Agency::where('is_verified', true)->count(),
            ];
        });

        // Get featured properties for quick display
        $featuredProperties = Cache::remember('featured_properties', 1800, function () {
            return Property::with(['city', 'state', 'propertyType'])
                ->where('status', 'available')
                ->where('is_published', true)
                ->where('is_featured', true)
                ->latest()
                ->limit(6)
                ->get();
        });

        // Get popular locations for search suggestions
        $popularLocations = Cache::remember('popular_locations', 3600, function () {
            return DB::table('properties')
                ->join('cities', 'properties.city_id', '=', 'cities.id')
                ->select('cities.name as city_name', DB::raw('count(*) as property_count'))
                ->where('properties.status', 'available')
                ->where('properties.is_published', true)
                ->groupBy('cities.name')
                ->orderBy('property_count', 'desc')
                ->limit(10)
                ->pluck('city_name')
                ->toArray();
        });

        return view('landing', compact('stats', 'featuredProperties', 'popularLocations'));
    }

    /**
     * Handle property search from hero section
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
            'listingType' => 'nullable|in:rent,sale,lease,shortlet',
            'propertyType' => 'nullable|string|max:255',
            'bedrooms' => 'nullable|integer|min:0|max:10',
            'bathrooms' => 'nullable|integer|min:1|max:10',
            'priceRange' => 'nullable|numeric|min:0',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'furnishingStatus' => 'nullable|in:furnished,semi_furnished,unfurnished',
            'sizeRange' => 'nullable|numeric|min:0',
            'parkingSpaces' => 'nullable|integer|min:0',
            'features' => 'nullable|string',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'area_id' => 'nullable|exists:areas,id',
            'isFeatured' => 'nullable|boolean',
            'isVerified' => 'nullable|boolean',
        ]);

        // Build the search query
        $query = Property::with(['city', 'state', 'area', 'propertyType'])
            ->published()
            ->available();

        // Global search query
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('address', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('landmark', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('city', function ($cityQuery) use ($searchTerm) {
                        $cityQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('state', function ($stateQuery) use ($searchTerm) {
                        $stateQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('area', function ($areaQuery) use ($searchTerm) {
                        $areaQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        // Filter by listing type
        if ($request->filled('listingType')) {
            $query->where('listing_type', $request->listingType);
        }

        // Filter by property type
        if ($request->filled('propertyType')) {
            $query->whereHas('propertyType', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->propertyType}%");
            });
        }

        // Filter by location IDs
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        // Filter by price range
        if ($request->filled('priceRange')) {
            $query->where('price', '<=', $request->priceRange);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by bedrooms
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        // Filter by bathrooms
        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', $request->bathrooms);
        }

        // Filter by furnishing status
        if ($request->filled('furnishingStatus')) {
            $query->where('furnishing_status', $request->furnishingStatus);
        }

        // Filter by size
        if ($request->filled('sizeRange')) {
            $query->where('size_sqm', '<=', $request->sizeRange);
        }

        // Filter by parking spaces
        if ($request->filled('parkingSpaces')) {
            $query->where('parking_spaces', '>=', $request->parkingSpaces);
        }

        // Filter by features
        if ($request->filled('features')) {
            $query->whereHas('features', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->features}%");
            });
        }

        // Filter by featured status
        if ($request->filled('isFeatured') && $request->isFeatured) {
            $query->featured();
        }

        // Filter by verified status
        if ($request->filled('isVerified') && $request->isVerified) {
            $query->verified();
        }

        // Get paginated results
        $properties = $query->orderBy('is_featured', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(12)
            ->appends($request->query());

        // Track search for analytics
        $this->trackSearch($request);

        return view('properties.index', compact('properties'));
    }

    /**
     * Get real-time statistics for counters
     */
    public function getStats()
    {
        $stats = Cache::remember('realtime_stats', 300, function () {
            return [
                'properties' => Property::where('status', 'available')->where('is_published', true)->count(),
                'clients' => User::whereHas('roles', function ($query) {
                    $query->where('name', 'tenant');
                })->count(),
                'agents' => User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['agent', 'landlord']);
                })->where('is_verified', true)->count(),
                'successful_matches' => DB::table('property_inquiries')
                    ->where('status', 'completed')
                    ->count(),
            ];
        });

        return response()->json($stats);
    }

    /**
     * Get location suggestions for search autocomplete
     */
    public function getLocationSuggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = Cache::remember("location_suggestions_{$query}", 3600, function () use ($query) {
            $cities = DB::table('properties')
                ->join('cities', 'properties.city_id', '=', 'cities.id')
                ->select('cities.name as city_name', DB::raw('count(*) as count'))
                ->where('cities.name', 'LIKE', "%{$query}%")
                ->where('properties.status', 'available')
                ->where('properties.is_published', true)
                ->groupBy('cities.name')
                ->orderBy('count', 'desc')
                ->limit(8)
                ->get();

            $areas = DB::table('properties')
                ->join('areas', 'properties.area_id', '=', 'areas.id')
                ->join('cities', 'properties.city_id', '=', 'cities.id')
                ->select('areas.name as area_name', 'cities.name as city_name', DB::raw('count(*) as count'))
                ->where('areas.name', 'LIKE', "%{$query}%")
                ->where('properties.status', 'available')
                ->where('properties.is_published', true)
                ->whereNotNull('areas.name')
                ->groupBy('areas.name', 'cities.name')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();

            $formatted = collect();

            foreach ($cities as $city) {
                $formatted->push([
                    'label' => $city->city_name,
                    'value' => $city->city_name,
                    'type' => 'city',
                    'count' => $city->count
                ]);
            }

            foreach ($areas as $area) {
                $formatted->push([
                    'label' => "{$area->area_name}, {$area->city_name}",
                    'value' => $area->area_name,
                    'type' => 'area',
                    'count' => $area->count
                ]);
            }

            return $formatted->take(10)->values();
        });

        return response()->json($suggestions);
    }

    /**
     * Track search analytics
     */
    private function trackSearch(Request $request)
    {
        try {
            DB::table('search_analytics')->insert([
                'search_terms' => json_encode($request->only([
                    'type',
                    'location',
                    'min_price',
                    'max_price',
                    'bedrooms',
                    'bathrooms',
                    'amenities'
                ])),
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        } catch (Exception $e) {
            // Silent fail for analytics
            logger()->warning('Failed to track search analytics', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
        }
    }

    /**
     * Get featured content for the landing page
     */
    public function getFeaturedContent()
    {
        $content = Cache::remember('landing_featured_content', 3600, function () {
            return [
                'properties' => Property::with(['images', 'location'])
                    ->where('status', 'active')
                    ->where('is_featured', true)
                    ->orderBy('updated_at', 'desc')
                    ->limit(6)
                    ->get(),

                'agents' => User::with(['profile', 'properties'])
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'agent');
                    })
                    ->where('is_verified', true)
                    ->where('is_featured', true)
                    ->withCount('properties')
                    ->orderBy('properties_count', 'desc')
                    ->limit(4)
                    ->get(),

                'agencies' => Agency::with(['agents', 'properties'])
                    ->where('is_verified', true)
                    ->where('is_featured', true)
                    ->withCount(['agents', 'properties'])
                    ->orderBy('properties_count', 'desc')
                    ->limit(3)
                    ->get(),
            ];
        });

        return response()->json($content);
    }

    /**
     * Display property details page
     */
    public function show(Property $property)
    {
        // Load all necessary relationships
        $property->load([
            'city.state',
            'area',
            'propertyType',
            'propertySubtype',
            'features' => function($query) {
                $query->where('is_active', true)->orderBy('name');
            },
            'agency',
            'agent',
            'owner',
            'media'
        ]);

        // Get related properties (same city, different property)
        $relatedProperties = Property::with(['city', 'state', 'propertyType'])
            ->where('city_id', $property->city_id)
            ->where('id', '!=', $property->id)
            ->where('status', 'available')
            ->where('is_published', true)
            ->orderBy('is_featured', 'desc')
            ->limit(4)
            ->get();

        // Track property view
        $this->trackPropertyView($property);

        return view('property.show', compact('property', 'relatedProperties'));
    }

    /**
     * Track property view for analytics
     */
    private function trackPropertyView(Property $property)
    {
        try {
            // Increment view count
            $property->increment('view_count');

            // Track detailed view analytics
            DB::table('property_views')->insert([
                'property_id' => $property->id,
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'referrer' => request()->header('referer'),
                'viewed_at' => now(),
            ]);
        } catch (Exception $e) {
            // Silent fail for analytics
            logger()->warning('Failed to track property view', [
                'property_id' => $property->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle newsletter subscription from landing page
     */
    public function subscribeNewsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscriptions,email',
            'name' => 'nullable|string|max:255',
        ]);

        try {
            DB::table('newsletter_subscriptions')->insert([
                'email' => $request->email,
                'name' => $request->name,
                'source' => 'landing_page',
                'subscribed_at' => now(),
                'ip_address' => $request->ip(),
            ]);

            // Send welcome email (queue for better performance)
            // dispatch(new SendWelcomeNewsletterEmail($request->email, $request->name));

            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing to our newsletter!'
            ]);
        } catch (Exception $e) {
            logger()->error('Newsletter subscription failed', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Subscription failed. Please try again.'
            ], 500);
        }
    }
}
