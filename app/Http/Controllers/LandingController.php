<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use App\Models\Agent;
use App\Models\Agency;

class LandingController extends Controller
{
    public function index()
    {
        // Get statistics for trust signals
        $stats = [
            'properties' => Property::where('is_published', true)->count() ?: 1250, // Placeholder if no data
            'users' => User::count() ?: 850,
            'agents' => Agent::where('is_active', true)->count() ?: 120,
            'agencies' => Agency::where('is_active', true)->count() ?: 45,
        ];

        // Get property types for search
        $propertyTypes = PropertyType::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Get target cities
        $targetCities = City::whereIn('name', ['Maiduguri', 'Kaduna', 'Kano', 'Abuja'])
            ->with('state')
            ->get(['id', 'name', 'state_id']);

        // Get featured properties (limit 6 for carousel)
        $featuredProperties = Property::where('is_published', true)
            ->where('is_featured', true)
            ->with(['propertyType', 'city.state'])
            ->take(6)
            ->get();

        return view('landing', compact('stats', 'propertyTypes', 'targetCities', 'featuredProperties'));
    }

    public function search(Request $request)
    {
        $query = Property::where('is_published', true);

        // Apply filters
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->filled('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('keyword')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keyword . '%')
                  ->orWhere('description', 'like', '%' . $request->keyword . '%');
            });
        }

        $properties = $query->with(['propertyType', 'city.state'])
            ->paginate(12);

        return view('properties.search', compact('properties'));
    }
}
