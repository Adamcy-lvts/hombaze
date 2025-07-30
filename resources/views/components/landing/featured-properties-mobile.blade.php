<!-- Premium Mobile Featured Properties Section -->
<section class="block md:hidden relative py-12 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 overflow-hidden" x-data="mobilePropertiesComponent()">
    <!-- Premium Background Elements -->
    <div class="absolute inset-0">
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/20 via-transparent to-blue-900/20"></div>
        <!-- Floating Elements -->
        <div class="absolute top-10 right-4 w-20 h-20 bg-gradient-to-br from-emerald-400/30 to-teal-500/20 rounded-full blur-2xl"></div>
        <div class="absolute bottom-20 left-4 w-16 h-16 bg-gradient-to-br from-blue-400/25 to-indigo-500/15 rounded-full blur-xl"></div>
    </div>

    <div class="relative z-10 px-4">
        <!-- Section Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center space-x-2 bg-white/10 backdrop-blur-xl text-white px-4 py-2 rounded-full text-sm font-semibold mb-4">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span>Premium Properties</span>
            </div>
            <h2 class="text-2xl font-black text-white mb-3">
                Featured
                <span class="bg-gradient-to-r from-emerald-400 to-blue-400 bg-clip-text text-transparent">Homes</span>
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed max-w-sm mx-auto">
                Discover handpicked premium properties across Nigeria's most desirable locations
            </p>
        </div>

        <!-- Mobile Property Cards -->
        <div class="space-y-4 mb-8" x-data="{ activeCard: 0 }">
            @php
                $mobileProperties = [
                    [
                        'id' => 1,
                        'title' => 'Luxury Penthouse Apartment',
                        'location' => 'Victoria Island, Lagos',
                        'price' => '₦85,000,000',
                        'period' => 'For Sale',
                        'bedrooms' => 4,
                        'bathrooms' => 5,
                        'area' => '280',
                        'type' => 'Penthouse',
                        'featured' => true,
                        'verified' => true,
                        'images' => [
                            'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                            'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                        ],
                        'agent' => [
                            'name' => 'Sarah Johnson',
                            'avatar' => 'https://images.unsplash.com/photo-1494790108755-2616b612b47c?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80'
                        ]
                    ],
                    [
                        'id' => 2,
                        'title' => 'Modern Executive Duplex',
                        'location' => 'Lekki Phase 1, Lagos',
                        'price' => '₦45,000,000',
                        'period' => 'For Sale',
                        'bedrooms' => 5,
                        'bathrooms' => 4,
                        'area' => '320',
                        'type' => 'Duplex',
                        'featured' => true,
                        'verified' => true,
                        'images' => [
                            'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                            'https://images.unsplash.com/photo-1600585154526-990dced4db0d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                        ],
                        'agent' => [
                            'name' => 'Michael Chen',
                            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80'
                        ]
                    ],
                    [
                        'id' => 3,
                        'title' => 'Contemporary Townhouse',
                        'location' => 'Wuse 2, Abuja',
                        'price' => '₦2,500,000',
                        'period' => 'Monthly Rent',
                        'bedrooms' => 3,
                        'bathrooms' => 3,
                        'area' => '200',
                        'type' => 'Townhouse',
                        'featured' => true,
                        'verified' => true,
                        'images' => [
                            'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                        ],
                        'agent' => [
                            'name' => 'Aisha Okafor',
                            'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80'
                        ]
                    ]
                ];
            @endphp

            @foreach($mobileProperties as $index => $property)
            <div class="group relative bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 overflow-hidden shadow-2xl"
                 x-data="{ imageIndex: 0, images: {{ json_encode($property['images']) }} }"
                 x-intersect.once="$el.classList.add('animate-slide-up')">
                
                <!-- Property Image Carousel -->
                <div class="relative h-48 overflow-hidden">
                    <template x-for="(image, imgIndex) in images" :key="imgIndex">
                        <img :src="image" 
                             :alt="'{{ $property['title'] }}'"
                             class="absolute inset-0 w-full h-full object-cover transition-all duration-700"
                             :class="imageIndex === imgIndex ? 'opacity-100 scale-100' : 'opacity-0 scale-105'">
                    </template>
                    
                    <!-- Image Navigation Dots -->
                    <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 flex space-x-1.5">
                        <template x-for="(image, imgIndex) in images" :key="imgIndex">
                            <button @click="imageIndex = imgIndex"
                                    class="w-2 h-2 rounded-full transition-all duration-300"
                                    :class="imageIndex === imgIndex ? 'bg-white' : 'bg-white/40'">
                            </button>
                        </template>
                    </div>

                    <!-- Property Type Badge -->
                    <div class="absolute top-3 left-3">
                        <span class="inline-flex items-center space-x-1 bg-emerald-500/90 backdrop-blur-sm text-white px-3 py-1 rounded-lg text-xs font-bold">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span>{{ $property['type'] }}</span>
                        </span>
                    </div>

                    <!-- Verified Badge -->
                    @if($property['verified'])
                    <div class="absolute top-3 right-3">
                        <span class="inline-flex items-center space-x-1 bg-blue-500/90 backdrop-blur-sm text-white px-2 py-1 rounded-lg text-xs font-bold">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Verified</span>
                        </span>
                    </div>
                    @endif

                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                </div>

                <!-- Property Details -->
                <div class="p-4">
                    <!-- Title and Location -->
                    <div class="mb-3">
                        <h3 class="text-lg font-bold text-white mb-1 line-clamp-1">{{ $property['title'] }}</h3>
                        <div class="flex items-center space-x-1 text-slate-300 text-sm">
                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ $property['location'] }}</span>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="mb-4">
                        <div class="flex items-baseline space-x-2">
                            <span class="text-2xl font-black text-emerald-400">{{ $property['price'] }}</span>
                            <span class="text-sm text-slate-400">{{ $property['period'] }}</span>
                        </div>
                    </div>

                    <!-- Property Features -->
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="text-center p-2 bg-white/5 rounded-lg">
                            <div class="text-lg font-bold text-white">{{ $property['bedrooms'] }}</div>
                            <div class="text-xs text-slate-400">Bedrooms</div>
                        </div>
                        <div class="text-center p-2 bg-white/5 rounded-lg">
                            <div class="text-lg font-bold text-white">{{ $property['bathrooms'] }}</div>
                            <div class="text-xs text-slate-400">Bathrooms</div>
                        </div>
                        <div class="text-center p-2 bg-white/5 rounded-lg">
                            <div class="text-lg font-bold text-white">{{ $property['area'] }}m²</div>
                            <div class="text-xs text-slate-400">Area</div>
                        </div>
                    </div>

                    <!-- Agent Info -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $property['agent']['avatar'] }}" 
                                 alt="{{ $property['agent']['name'] }}"
                                 class="w-8 h-8 rounded-full object-cover">
                            <div>
                                <div class="text-sm font-semibold text-white">{{ $property['agent']['name'] }}</div>
                                <div class="text-xs text-slate-400">Verified Agent</div>
                            </div>
                        </div>
                        <button class="p-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors duration-300">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-3">
                        <button class="group flex items-center justify-center space-x-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 shadow-lg">
                            <span class="text-sm">View Details</span>
                            <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </button>
                        <button class="flex items-center justify-center space-x-2 bg-white/10 hover:bg-white/20 text-white font-semibold py-3 px-4 rounded-xl border border-white/20 hover:border-white/30 transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span class="text-sm">Save</span>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- View All CTA -->
        <div class="text-center">
            <a href="{{ route('properties.search') }}" 
               class="group inline-flex items-center justify-center space-x-2 bg-white/10 hover:bg-white/20 backdrop-blur-xl text-white font-semibold py-4 px-8 rounded-2xl border border-white/20 hover:border-white/30 transition-all duration-300 shadow-xl">
                <span>View All Properties</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<style>
    .animate-slide-up {
        animation: slideUp 0.6s ease-out forwards;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Stagger animation for mobile cards */
    .group:nth-child(1) { animation-delay: 0.1s; }
    .group:nth-child(2) { animation-delay: 0.2s; }
    .group:nth-child(3) { animation-delay: 0.3s; }
</style>

<script>
    function mobilePropertiesComponent() {
        return {
            init() {
                console.log('Mobile Properties component initialized');
                
                // Auto-advance image carousel every 4 seconds
                setInterval(() => {
                    const cards = document.querySelectorAll('[x-data*="imageIndex"]');
                    cards.forEach(card => {
                        const scope = Alpine.$data(card);
                        if (scope && scope.images) {
                            scope.imageIndex = (scope.imageIndex + 1) % scope.images.length;
                        }
                    });
                }, 4000);
            }
        }
    }
</script>