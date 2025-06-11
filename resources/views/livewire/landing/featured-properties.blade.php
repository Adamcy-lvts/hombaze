<div class="featured-properties-component">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($properties as $property)
            <div class="property-card bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg shadow-lg overflow-hidden hover:shadow-xl hover:bg-gray-800/70 transition-all duration-300">
                <!-- Property Image -->
                <div class="relative h-48 bg-gray-700">
                    @if($property->getFirstMediaUrl('featured'))
                        <img src="{{ $property->getFirstMediaUrl('featured') }}" 
                             alt="{{ $property->title }}" 
                             class="property-image w-full h-full object-cover">
                    @elseif($property->getFirstMediaUrl('gallery'))
                        <img src="{{ $property->getFirstMediaUrl('gallery') }}" 
                             alt="{{ $property->title }}" 
                             class="property-image w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                    @endif
                    
                    <!-- Property overlay for hover effect -->
                    <div class="property-overlay absolute inset-0 bg-black opacity-0 transition-opacity duration-300"></div>
                    
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
                    <h3 class="text-lg font-semibold text-gray-100 mb-2 line-clamp-2">{{ $property->title }}</h3>
                    
                    <!-- Location -->
                    <div class="flex items-center text-gray-300 mb-2">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm">{{ $property->city->name }}, {{ $property->city->state->name }}</span>
                    </div>

                    <!-- Property Features -->
                    <div class="flex items-center justify-between text-sm text-gray-400 mb-3">
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
                    <button class="w-full bg-blue-600 hover:bg-blue-500 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                        View Details
                    </button>
                </div>
            </div>
        @empty
            <!-- Placeholder Properties -->
            @for($i = 1; $i <= 6; $i++)
                <div class="property-card bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-lg shadow-lg overflow-hidden hover:shadow-xl hover:bg-gray-800/70 transition-all duration-300">
                    <div class="relative h-48 bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <div class="absolute top-3 left-3">
                            <span class="bg-blue-600 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                {{ ['Apartment', 'House', 'Duplex'][($i-1) % 3] }}
                            </span>
                        </div>
                        <div class="absolute top-3 right-3">
                            <span class="bg-green-600 text-white text-sm font-bold px-3 py-1 rounded-full">
                                ₦{{ number_format([250000, 400000, 650000, 800000, 1200000, 950000][$i-1]) }}
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-100 mb-2">{{ ['Modern 2BR Apartment', 'Luxury 3BR House', 'Executive Duplex', 'Cozy 1BR Flat', 'Family 4BR Home', 'Spacious 3BR Apartment'][$i-1] }}</h3>
                        <div class="flex items-center text-gray-300 mb-2">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-sm">{{ ['Abuja', 'Kano', 'Kaduna', 'Maiduguri'][$i % 4] }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm text-gray-400 mb-3">
                            <span>{{ [2, 3, 4, 1, 4, 3][$i-1] }} beds</span>
                            <span>{{ [2, 2, 3, 1, 3, 2][$i-1] }} baths</span>
                            <span>{{ [80, 120, 200, 50, 180, 100][$i-1] }} m²</span>
                        </div>
                        <button class="w-full bg-blue-600 hover:bg-blue-500 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                            View Details
                        </button>
                    </div>
                </div>
            @endfor
        @endforelse
    </div>
</div>
