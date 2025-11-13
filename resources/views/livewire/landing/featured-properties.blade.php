<div class="featured-properties-component">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($properties as $property)
            <div class="property-card group relative bg-white/80 backdrop-blur-xs border border-white/50 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:scale-[1.02] hover:-translate-y-1">
                <!-- Premium Glass Overlay -->
                <div class="absolute inset-0 bg-linear-to-br from-white/10 to-transparent rounded-2xl pointer-events-none"></div>
                
                <!-- Property Image Container -->
                <div class="relative h-56 bg-linear-to-br from-gray-100 to-gray-200 overflow-hidden rounded-t-2xl">
                    @if($property->getFirstMediaUrl('featured'))
                        <img src="{{ $property->getFirstMediaUrl('featured') }}" 
                             alt="{{ $property->title }}" 
                             class="property-image w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    @elseif($property->getFirstMediaUrl('gallery'))
                        <img src="{{ $property->getFirstMediaUrl('gallery') }}" 
                             alt="{{ $property->title }}" 
                             class="property-image w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    @else
                        <div class="w-full h-full bg-linear-to-br from-blue-50 to-emerald-50 flex items-center justify-center">
                            <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                    @endif
                    
                    <!-- Premium Gradient Overlay -->
                    <div class="absolute inset-0 bg-linear-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <!-- Premium Property Type Badge -->
                    <div class="absolute top-4 left-4">
                        <div class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/90 backdrop-blur-xs border border-white/50 shadow-lg">
                            <div class="w-2 h-2 rounded-full bg-blue-500 mr-2"></div>
                            <span class="text-xs font-semibold text-gray-700">
                                {{ $property->propertyType->name ?? 'Property' }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Premium Price Badge -->
                    <div class="absolute top-4 right-4">
                        <div class="inline-flex items-center px-3 py-1.5 rounded-full bg-linear-to-r from-emerald-500 to-blue-500 text-white shadow-lg">
                            <span class="text-sm font-bold">
                                ₦{{ number_format($property->price) }}
                            </span>
                        </div>
                    </div>

                    <!-- Featured Badge -->
                    <div class="absolute bottom-4 left-4">
                        <div class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-400/90 backdrop-blur-xs">
                            <svg class="w-3 h-3 text-yellow-800 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-xs font-semibold text-yellow-800">Featured</span>
                        </div>
                    </div>
                </div>

                <!-- Premium Property Details -->
                <div class="p-6 relative">
                    <!-- Property Title -->
                    <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-blue-700 transition-colors duration-300">
                        {{ $property->title }}
                    </h3>
                    
                    <!-- Premium Location -->
                    <div class="flex items-center text-gray-600 mb-4">
                        <div class="w-5 h-5 rounded-full bg-linear-to-r from-blue-500 to-emerald-500 flex items-center justify-center mr-2">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium">{{ $property->city->name }}, {{ $property->city->state->name }}</span>
                    </div>

                    <!-- Premium Property Features -->
                    <div class="flex items-center justify-between mb-6">
                        @if($property->bedrooms)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0"></path>
                                </svg>
                                <span class="text-sm text-gray-600">{{ $property->bedrooms }} beds</span>
                            </div>
                        @endif
                        @if($property->bathrooms)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11"></path>
                                </svg>
                                <span class="text-sm text-gray-600">{{ $property->bathrooms }} baths</span>
                            </div>
                        @endif
                        @if($property->size)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                </svg>
                                <span class="text-sm text-gray-600">{{ $property->size }} m²</span>
                            </div>
                        @endif
                    </div>

                    <!-- Premium Action Button -->
                    <a href="/property/{{ $property->slug }}" class="group/btn relative w-full inline-flex items-center justify-center px-6 py-3 bg-linear-to-r from-blue-600 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] overflow-hidden">
                        <!-- Button glass overlay -->
                        <div class="absolute inset-0 bg-white/20 rounded-xl"></div>
                        
                        <!-- Button content -->
                        <span class="relative z-10">View Details</span>
                        <svg class="relative z-10 w-4 h-4 ml-2 transform group-hover/btn:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                        
                        <!-- Button shimmer effect -->
                        <div class="absolute inset-0 -top-1 -left-1 bg-linear-to-r from-transparent via-white/30 to-transparent skew-x-12 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700 rounded-xl"></div>
                    </a>
                </div>

                <!-- Premium hover border effect -->
                <div class="absolute inset-0 rounded-2xl border-2 border-transparent bg-linear-to-r from-blue-500 to-emerald-500 bg-clip-border opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="padding: 1px;">
                    <div class="h-full w-full rounded-2xl bg-white"></div>
                </div>
            </div>
        @empty
            <!-- Premium Placeholder Properties -->
            @for($i = 1; $i <= 6; $i++)
                <div class="property-card group relative bg-white/80 backdrop-blur-xs border border-white/50 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:scale-[1.02] hover:-translate-y-1">
                    <!-- Glass Overlay -->
                    <div class="absolute inset-0 bg-linear-to-br from-white/10 to-transparent rounded-2xl pointer-events-none"></div>
                    
                    <div class="relative h-56 bg-linear-to-br from-blue-50 to-emerald-50 overflow-hidden rounded-t-2xl flex items-center justify-center">
                        <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        
                        <!-- Type Badge -->
                        <div class="absolute top-4 left-4">
                            <div class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/90 backdrop-blur-xs border border-white/50 shadow-lg">
                                <div class="w-2 h-2 rounded-full bg-blue-500 mr-2"></div>
                                <span class="text-xs font-semibold text-gray-700">
                                    {{ ['Apartment', 'House', 'Duplex'][($i-1) % 3] }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Price Badge -->
                        <div class="absolute top-4 right-4">
                            <div class="inline-flex items-center px-3 py-1.5 rounded-full bg-linear-to-r from-emerald-500 to-blue-500 text-white shadow-lg">
                                <span class="text-sm font-bold">
                                    ₦{{ number_format([250000, 400000, 650000, 800000, 1200000, 950000][$i-1]) }}
                                </span>
                            </div>
                        </div>

                        <!-- Featured Badge -->
                        <div class="absolute bottom-4 left-4">
                            <div class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-400/90 backdrop-blur-xs">
                                <svg class="w-3 h-3 text-yellow-800 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-xs font-semibold text-yellow-800">Featured</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 relative">
                        <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-700 transition-colors duration-300">
                            {{ ['Modern 2BR Apartment', 'Luxury 3BR House', 'Executive Duplex', 'Cozy 1BR Flat', 'Family 4BR Home', 'Spacious 3BR Apartment'][$i-1] }}
                        </h3>
                        
                        <div class="flex items-center text-gray-600 mb-4">
                            <div class="w-5 h-5 rounded-full bg-linear-to-r from-blue-500 to-emerald-500 flex items-center justify-center mr-2">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium">{{ ['Abuja', 'Kano', 'Kaduna', 'Maiduguri'][$i % 4] }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0"></path>
                                </svg>
                                <span class="text-sm text-gray-600">{{ [2, 3, 4, 1, 4, 3][$i-1] }} beds</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11"></path>
                                </svg>
                                <span class="text-sm text-gray-600">{{ [2, 2, 3, 1, 3, 2][$i-1] }} baths</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                </svg>
                                <span class="text-sm text-gray-600">{{ [80, 120, 200, 50, 180, 100][$i-1] }} m²</span>
                            </div>
                        </div>
                        
                        <button class="group/btn relative w-full inline-flex items-center justify-center px-6 py-3 bg-linear-to-r from-blue-600 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] overflow-hidden">
                            <div class="absolute inset-0 bg-white/20 rounded-xl"></div>
                            <span class="relative z-10">View Details</span>
                            <svg class="relative z-10 w-4 h-4 ml-2 transform group-hover/btn:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                            <div class="absolute inset-0 -top-1 -left-1 bg-linear-to-r from-transparent via-white/30 to-transparent skew-x-12 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700 rounded-xl"></div>
                        </button>
                    </div>

                    <!-- Premium hover border effect -->
                    <div class="absolute inset-0 rounded-2xl border-2 border-transparent bg-linear-to-r from-blue-500 to-emerald-500 bg-clip-border opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="padding: 1px;">
                        <div class="h-full w-full rounded-2xl bg-white"></div>
                    </div>
                </div>
            @endfor
        @endforelse
    </div>
</div>