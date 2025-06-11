<x-layouts.landing>
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Search Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Property Search Results</h1>
                
                <!-- Search Form -->
                <form method="GET" action="{{ route('properties.search') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <select name="city_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Select City</option>
                        @foreach(\App\Models\City::whereIn('name', ['Maiduguri', 'Kaduna', 'Kano', 'Abuja'])->with('state')->get() as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}, {{ $city->state->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select name="property_type_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Any Type</option>
                        @foreach(\App\Models\PropertyType::where('is_active', true)->get() as $type)
                            <option value="{{ $type->id }}" {{ request('property_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <input type="number" name="min_price" placeholder="Min Price" value="{{ request('min_price') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                        Search
                    </button>
                </form>
            </div>

            <!-- Results -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($properties as $property)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Property Image -->
                        <div class="relative h-48 bg-gray-200">
                            @if($property->images->count() > 0)
                                <img src="{{ asset('storage/' . $property->images->first()->path) }}" 
                                     alt="{{ $property->title }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Property Type Badge -->
                            <div class="absolute top-3 left-3">
                                <span class="bg-blue-600 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                    {{ $property->propertyType->name ?? 'Property' }}
                                </span>
                            </div>
                            
                            <!-- Price Badge -->
                            <div class="absolute top-3 right-3">
                                <span class="bg-green-600 text-white text-sm font-bold px-3 py-1 rounded-full">
                                    ₦{{ number_format($property->price) }}
                                </span>
                            </div>
                        </div>

                        <!-- Property Details -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $property->title }}</h3>
                            
                            <!-- Location -->
                            <div class="flex items-center text-gray-600 mb-2">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-sm">{{ $property->city->name }}, {{ $property->city->state->name }}</span>
                            </div>

                            <!-- Property Features -->
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                                @if($property->bedrooms)
                                    <span>{{ $property->bedrooms }} beds</span>
                                @endif
                                @if($property->bathrooms)
                                    <span>{{ $property->bathrooms }} baths</span>
                                @endif
                                @if($property->size)
                                    <span>{{ $property->size }} m²</span>
                                @endif
                            </div>

                            <!-- Action Button -->
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                                View Details
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No properties found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria.</p>
                        <div class="mt-6">
                            <a href="{{ route('landing') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Back to Search
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($properties->hasPages())
                <div class="mt-8">
                    {{ $properties->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.landing>
