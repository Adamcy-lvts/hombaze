<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\PropertyType;
use App\Models\PropertyFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertySearchController extends Controller
{
    /**
     * Get intelligent search suggestions
     */
    public function suggestions(Request $request)
    {
        $query = trim($request->get('q', ''));
        $limit = $request->get('limit', 10);

        if (strlen($query) < 2) {
            return response()->json([
                'suggestions' => [],
                'query' => $query
            ]);
        }

        $cacheKey = "search_suggestions_" . md5($query . $limit);
        
        $suggestions = Cache::remember($cacheKey, 1800, function () use ($query, $limit) {
            $results = collect();

            // 1. Property Suggestions (Top Priority)
            $propertyResults = $this->getPropertySuggestions($query, 3);
            $results = $results->merge($propertyResults);

            // 2. Location Suggestions
            $locationResults = $this->getLocationSuggestions($query, 4);
            $results = $results->merge($locationResults);

            // 3. Quick Filter Suggestions
            $filterResults = $this->getQuickFilterSuggestions($query, 2);
            $results = $results->merge($filterResults);

            // 4. Feature Suggestions
            $featureResults = $this->getFeatureSuggestions($query, 1);
            $results = $results->merge($featureResults);

            return $results->take($limit)->values();
        });

        return response()->json([
            'suggestions' => $suggestions,
            'query' => $query,
            'count' => $suggestions->count()
        ]);
    }

    /**
     * Advanced property search
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $filters = $request->only([
            'listing_type', 'property_type', 'bedrooms', 'bathrooms', 
            'min_price', 'max_price', 'state_id', 'city_id', 'area_id',
            'furnishing_status', 'features', 'is_featured', 'is_verified'
        ]);

        $properties = Property::published()
            ->available()
            ->with(['city', 'state', 'area', 'propertyType', 'features'])
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('title', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%")
                        ->orWhere('address', 'LIKE', "%{$query}%")
                        ->orWhere('landmark', 'LIKE', "%{$query}%")
                        ->orWhereHas('city', function ($cityQuery) use ($query) {
                            $cityQuery->where('name', 'LIKE', "%{$query}%");
                        })
                        ->orWhereHas('state', function ($stateQuery) use ($query) {
                            $stateQuery->where('name', 'LIKE', "%{$query}%");
                        })
                        ->orWhereHas('area', function ($areaQuery) use ($query) {
                            $areaQuery->where('name', 'LIKE', "%{$query}%");
                        });
                });
            })
            ->when($filters['listing_type'] ?? null, function ($q, $type) {
                $q->where('listing_type', $type);
            })
            ->when($filters['bedrooms'] ?? null, function ($q, $bedrooms) {
                $q->where('bedrooms', '>=', $bedrooms);
            })
            ->when($filters['bathrooms'] ?? null, function ($q, $bathrooms) {
                $q->where('bathrooms', '>=', $bathrooms);
            })
            ->when($filters['min_price'] ?? null, function ($q, $minPrice) {
                $q->where('price', '>=', $minPrice);
            })
            ->when($filters['max_price'] ?? null, function ($q, $maxPrice) {
                $q->where('price', '<=', $maxPrice);
            })
            ->when($filters['state_id'] ?? null, function ($q, $stateId) {
                $q->where('state_id', $stateId);
            })
            ->when($filters['city_id'] ?? null, function ($q, $cityId) {
                $q->where('city_id', $cityId);
            })
            ->when($filters['area_id'] ?? null, function ($q, $areaId) {
                $q->where('area_id', $areaId);
            })
            ->when($filters['furnishing_status'] ?? null, function ($q, $status) {
                $q->where('furnishing_status', $status);
            })
            ->when($filters['is_featured'] ?? null, function ($q) {
                $q->featured();
            })
            ->when($filters['is_verified'] ?? null, function ($q) {
                $q->verified();
            })
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'properties' => $properties->items(),
            'pagination' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ],
            'filters_applied' => array_filter($filters),
            'query' => $query
        ]);
    }

    /**
     * Get dynamic filter options
     */
    public function getFilters(Request $request)
    {
        $query = $request->get('q', '');

        $filters = Cache::remember("search_filters_" . md5($query), 3600, function () use ($query) {
            $baseQuery = Property::published()->available();

            if ($query) {
                $baseQuery->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%")
                        ->orWhere('address', 'LIKE', "%{$query}%");
                });
            }

            return [
                'listing_types' => $baseQuery->distinct()->pluck('listing_type')->filter()->values(),
                'price_ranges' => [
                    'min' => $baseQuery->min('price') ?? 0,
                    'max' => $baseQuery->max('price') ?? 50000000,
                    'avg' => $baseQuery->avg('price') ?? 2500000,
                ],
                'bedrooms' => $baseQuery->distinct()->orderBy('bedrooms')->pluck('bedrooms')->filter()->values(),
                'bathrooms' => $baseQuery->distinct()->orderBy('bathrooms')->pluck('bathrooms')->filter()->values(),
                'states' => State::whereHas('properties', function ($q) use ($baseQuery) {
                    $q->whereIn('id', $baseQuery->pluck('id'));
                })->get(['id', 'name']),
                'property_types' => PropertyType::whereHas('properties', function ($q) use ($baseQuery) {
                    $q->whereIn('id', $baseQuery->pluck('id'));
                })->get(['id', 'name']),
                'features' => PropertyFeature::active()->ordered()->take(10)->get(['id', 'name', 'icon']),
            ];
        });

        return response()->json($filters);
    }

    /**
     * Get location-specific suggestions
     */
    public function locationSuggestions(Request $request)
    {
        $query = trim($request->get('q', ''));
        $limit = $request->get('limit', 10);

        if (strlen($query) < 2) {
            return response()->json(['locations' => []]);
        }

        $locations = Cache::remember("location_suggestions_" . md5($query), 3600, function () use ($query, $limit) {
            $results = collect();

            // States
            $states = State::where('name', 'LIKE', "%{$query}%")
                ->withCount('properties')
                ->take(3)
                ->get()
                ->map(function ($state) {
                    return [
                        'type' => 'state',
                        'id' => $state->id,
                        'label' => $state->name,
                        'subtitle' => 'State',
                        'count' => $state->properties_count . ' properties',
                        'value' => $state->name
                    ];
                });

            // Cities
            $cities = City::where('name', 'LIKE', "%{$query}%")
                ->with('state')
                ->withCount('properties')
                ->take(4)
                ->get()
                ->map(function ($city) {
                    return [
                        'type' => 'city',
                        'id' => $city->id,
                        'label' => $city->name,
                        'subtitle' => $city->state->name . ' State',
                        'count' => $city->properties_count . ' properties',
                        'value' => $city->name . ', ' . $city->state->name
                    ];
                });

            // Areas
            $areas = Area::where('name', 'LIKE', "%{$query}%")
                ->with(['city.state'])
                ->withCount('properties')
                ->take(3)
                ->get()
                ->map(function ($area) {
                    return [
                        'type' => 'area',
                        'id' => $area->id,
                        'label' => $area->name,
                        'subtitle' => $area->city->name . ', ' . $area->city->state->name,
                        'count' => $area->properties_count . ' properties',
                        'value' => $area->name . ', ' . $area->city->name
                    ];
                });

            return $results->merge($states)->merge($cities)->merge($areas)->take($limit);
        });

        return response()->json(['locations' => $locations]);
    }

    /**
     * Get property suggestions
     */
    private function getPropertySuggestions($query, $limit = 3)
    {
        return Property::published()
            ->available()
            ->with(['city', 'state', 'propertyType'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->orderBy('is_featured', 'desc')
            ->orderBy('view_count', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($property) {
                $cityName = $property->city ? $property->city->name : 'Unknown City';
                $stateName = $property->state ? $property->state->name : 'Unknown State';
                
                return [
                    'type' => 'property',
                    'id' => $property->id,
                    'label' => $property->title,
                    'subtitle' => $cityName . ', ' . $stateName,
                    'price' => $property->formatted_price,
                    'image' => $property->getFeaturedImageUrl('thumb'),
                    'value' => $property->title,
                    'url' => '/property/' . $property->slug,
                    'bedrooms' => $property->bedrooms,
                    'bathrooms' => $property->bathrooms,
                    'is_featured' => $property->is_featured
                ];
            });
    }

    /**
     * Get location suggestions
     */
    private function getLocationSuggestions($query, $limit = 4)
    {
        $results = collect();

        // Cities with property counts
        $cities = City::where('name', 'LIKE', "%{$query}%")
            ->with('state')
            ->withCount('properties')
            ->orderBy('properties_count', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($city) {
                return [
                    'type' => 'location',
                    'subtype' => 'city',
                    'id' => $city->id,
                    'label' => $city->name,
                    'subtitle' => $city->state->name . ' State',
                    'count' => $city->properties_count . ' properties',
                    'value' => $city->name,
                    'icon' => 'map-pin'
                ];
            });

        return $cities;
    }

    /**
     * Get quick filter suggestions
     */
    private function getQuickFilterSuggestions($query, $limit = 2)
    {
        $suggestions = collect();

        // Price-based suggestions
        if (preg_match('/(\d+)/', $query, $matches)) {
            $price = intval($matches[1]);
            if ($price > 100) { // Assume it's a price
                $priceFormatted = $price < 1000000 ? '₦' . number_format($price) : '₦' . number_format($price / 1000000, 1) . 'M';
                $count = Property::where('price', '<=', $price * 1000)->count();
                
                $suggestions->push([
                    'type' => 'quick_filter',
                    'label' => "Properties under {$priceFormatted}",
                    'subtitle' => 'Quick filter',
                    'count' => $count . ' properties',
                    'value' => "max_price:{$price}",
                    'icon' => 'currency-naira'
                ]);
            }
        }

        // Bedroom-based suggestions
        if (preg_match('/(\d+)\s*(bed|bedroom)/i', $query, $matches)) {
            $bedrooms = intval($matches[1]);
            $count = Property::where('bedrooms', '>=', $bedrooms)->count();
            
            $suggestions->push([
                'type' => 'quick_filter',
                'label' => "{$bedrooms}+ Bedroom Properties",
                'subtitle' => 'Quick filter',
                'count' => $count . ' properties',
                'value' => "bedrooms:{$bedrooms}",
                'icon' => 'home'
            ]);
        }

        return $suggestions->take($limit);
    }

    /**
     * Get feature suggestions
     */
    private function getFeatureSuggestions($query, $limit = 1)
    {
        return PropertyFeature::active()
            ->where('name', 'LIKE', "%{$query}%")
            ->withCount('properties')
            ->orderBy('properties_count', 'desc')
            ->take($limit)
            ->get()
            ->map(function ($feature) {
                return [
                    'type' => 'feature',
                    'id' => $feature->id,
                    'label' => $feature->name,
                    'subtitle' => 'Property feature',
                    'count' => $feature->properties_count . ' properties',
                    'value' => $feature->name,
                    'icon' => $feature->icon ?? 'star'
                ];
            });
    }
}