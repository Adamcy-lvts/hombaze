<!-- Popular Locations Section -->
<section class="relative py-16 lg:py-24 bg-linear-to-br from-slate-50 via-white to-slate-100" x-data="popularLocationsComponent()">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;><g fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;><g fill=&quot;%23000000&quot; fill-opacity=&quot;0.1&quot;><circle cx=&quot;30&quot; cy=&quot;30&quot; r=&quot;1&quot;/></g></svg>'); background-size: 60px 60px;"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-12 lg:mb-16">
            <div class="inline-flex items-center space-x-2 bg-emerald-50 text-emerald-700 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Popular Destinations</span>
            </div>
            <h2 class="text-3xl lg:text-5xl font-black text-gray-900 mb-4 lg:mb-6">
                Discover Prime
                <span class="bg-linear-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">Locations</span>
            </h2>
            <p class="text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Explore Nigeria's most sought-after neighborhoods where dreams meet reality. 
                From bustling city centers to serene residential areas.
            </p>
        </div>

        <!-- Popular Locations Grid -->
        @if(false)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            @foreach($popularLocations as $location)
                @if(is_array($location))
                <div class="group relative bg-white rounded-2xl lg:rounded-3xl shadow-lg border border-gray-200/60 overflow-hidden hover:shadow-2xl hover:border-emerald-200 transition-all duration-700 transform hover:-translate-y-2"
                     x-intersect.once="$el.classList.add('animate-fade-in-up')">
                    <!-- Location Image -->
                    <div class="relative h-48 lg:h-56 overflow-hidden">
                        <img src="{{ $location['image'] ?? 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" 
                             alt="{{ $location['name'] ?? 'Location' }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-linear-to-t from-black/60 via-black/20 to-transparent"></div>
                        
                        <!-- Location Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center space-x-1 bg-white/90 backdrop-blur-xs text-gray-900 px-3 py-1.5 rounded-lg text-sm font-semibold">
                                <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ $location['state'] ?? 'Lagos' }}</span>
                            </span>
                        </div>

                        <!-- Stats Badge -->
                        @if(isset($location['trending']) && $location['trending'])
                        <div class="absolute top-4 right-4">
                            <span class="inline-flex items-center space-x-1 bg-linear-to-r from-orange-500 to-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
                                </svg>
                                <span>Trending</span>
                            </span>
                        </div>
                        @endif
                    </div>

                    <!-- Location Content -->
                    <div class="p-6 lg:p-8">
                        <div class="mb-4">
                            <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2 group-hover:text-emerald-600 transition-colors duration-300">
                                {{ $location['name'] ?? 'Prime Location' }}
                            </h3>
                            <p class="text-gray-600 text-sm lg:text-base leading-relaxed">
                                {{ $location['description'] ?? 'Discover amazing properties in this prime location with excellent amenities and connectivity.' }}
                            </p>
                        </div>

                        <!-- Location Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-lg lg:text-xl font-bold text-emerald-600">{{ number_format($location['property_count'] ?? 0) }}</div>
                                <div class="text-xs text-gray-600">Properties</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-lg lg:text-xl font-bold text-blue-600">
                                    @if(isset($location['avg_price']) && is_numeric($location['avg_price']) && $location['avg_price'] > 0)
                                        ₦{{ number_format($location['avg_price'] / 1000) }}K
                                    @else
                                        ₦500K
                                    @endif
                                </div>
                                <div class="text-xs text-gray-600">Avg. Price</div>
                            </div>
                        </div>

                        <!-- Search Button -->
                        <a href="{{ route('properties.search', ['location' => $location['slug'] ?? strtolower(str_replace(' ', '-', $location['name'] ?? 'location'))]) }}" 
                           class="group/btn relative w-full bg-linear-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl">
                            <span>Explore Properties</span>
                            <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
        @else
        <!-- Fallback when no data -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            @php
                $fallbackLocations = [
                    [
                        'name' => 'Victoria Island',
                        'state' => 'Lagos',
                        'description' => 'Premier business district with luxury apartments and world-class amenities.',
                        'property_count' => 1250,
                        'avg_price' => 2500000,
                        'trending' => true,
                        'image' => 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                    ],
                    [
                        'name' => 'Lekki Phase 1',
                        'state' => 'Lagos',
                        'description' => 'Upscale residential area with modern infrastructure and beach proximity.',
                        'property_count' => 890,
                        'avg_price' => 1800000,
                        'trending' => false,
                        'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                    ],
                    [
                        'name' => 'Wuse 2',
                        'state' => 'Abuja',
                        'description' => 'Central business district with high-rise apartments and commercial spaces.',
                        'property_count' => 675,
                        'avg_price' => 1500000,
                        'trending' => true,
                        'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                    ],
                    [
                        'name' => 'Ikeja GRA',
                        'state' => 'Lagos',
                        'description' => 'Government Reserved Area with established residential properties.',
                        'property_count' => 540,
                        'avg_price' => 1200000,
                        'trending' => false,
                        'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                    ],
                    [
                        'name' => 'Maitama',
                        'state' => 'Abuja',
                        'description' => 'Prestigious diplomatic district with luxury homes and embassies.',
                        'property_count' => 320,
                        'avg_price' => 3500000,
                        'trending' => false,
                        'image' => 'https://images.unsplash.com/photo-1613977257363-707ba9348227?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                    ],
                    [
                        'name' => 'Port Harcourt GRA',
                        'state' => 'Rivers',
                        'description' => 'Prime residential area in the oil capital with modern amenities.',
                        'property_count' => 280,
                        'avg_price' => 1000000,
                        'trending' => false,
                        'image' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                    ]
                ];
            @endphp
            
            @foreach($fallbackLocations as $location)
                <div class="group relative bg-white rounded-2xl lg:rounded-3xl shadow-lg border border-gray-200/60 overflow-hidden hover:shadow-2xl hover:border-emerald-200 transition-all duration-700 transform hover:-translate-y-2"
                     x-intersect.once="$el.classList.add('animate-fade-in-up')">
                    <!-- Location Image -->
                    <div class="relative h-48 lg:h-56 overflow-hidden">
                        <img src="{{ $location['image'] }}" 
                             alt="{{ $location['name'] }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-linear-to-t from-black/60 via-black/20 to-transparent"></div>
                        
                        <!-- Location Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center space-x-1 bg-white/90 backdrop-blur-xs text-gray-900 px-3 py-1.5 rounded-lg text-sm font-semibold">
                                <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ $location['state'] }}</span>
                            </span>
                        </div>

                        <!-- Trending Badge -->
                        @if($location['trending'])
                        <div class="absolute top-4 right-4">
                            <span class="inline-flex items-center space-x-1 bg-linear-to-r from-orange-500 to-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
                                </svg>
                                <span>Trending</span>
                            </span>
                        </div>
                        @endif
                    </div>

                    <!-- Location Content -->
                    <div class="p-6 lg:p-8">
                        <div class="mb-4">
                            <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2 group-hover:text-emerald-600 transition-colors duration-300">
                                {{ $location['name'] }}
                            </h3>
                            <p class="text-gray-600 text-sm lg:text-base leading-relaxed">
                                {{ $location['description'] }}
                            </p>
                        </div>

                        <!-- Location Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-lg lg:text-xl font-bold text-emerald-600">{{ number_format($location['property_count']) }}</div>
                                <div class="text-xs text-gray-600">Properties</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <div class="text-lg lg:text-xl font-bold text-blue-600">₦{{ number_format($location['avg_price'] / 1000) }}K</div>
                                <div class="text-xs text-gray-600">Avg. Price</div>
                            </div>
                        </div>

                        <!-- Search Button -->
                        <a href="{{ route('properties.search', ['location' => strtolower(str_replace(' ', '-', $location['name']))]) }}" 
                           class="group/btn relative w-full bg-linear-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl">
                            <span>Explore Properties</span>
                            <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- View All Locations CTA -->
        <div class="text-center mt-12 lg:mt-16">
            <a href="{{ route('properties.search') }}" 
               class="group inline-flex items-center space-x-3 bg-white hover:bg-gray-50 text-gray-900 font-semibold py-4 px-8 rounded-2xl border-2 border-gray-200 hover:border-emerald-300 transition-all duration-300 shadow-lg hover:shadow-xl">
                <span class="text-lg">View All Locations</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<style>
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Stagger animation for location cards */
    .group:nth-child(1) { animation-delay: 0.1s; }
    .group:nth-child(2) { animation-delay: 0.2s; }
    .group:nth-child(3) { animation-delay: 0.3s; }
    .group:nth-child(4) { animation-delay: 0.4s; }
    .group:nth-child(5) { animation-delay: 0.5s; }
    .group:nth-child(6) { animation-delay: 0.6s; }
</style>

<script>
    function popularLocationsComponent() {
        return {
            init() {
                // Initialize any additional interactions here
                console.log('Popular Locations component initialized');
            }
        }
    }
</script>