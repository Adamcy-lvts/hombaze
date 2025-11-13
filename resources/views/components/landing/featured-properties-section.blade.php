<!-- Clean Featured Properties Section -->
<section class="hidden md:block relative py-32 overflow-hidden" id="featured-properties">
    <!-- Sophisticated Background -->
    <div class="absolute inset-0 bg-linear-to-br from-gray-50 via-white to-blue-50/30"></div>

    <!-- Floating Elements -->
    <div
        class="absolute top-20 left-10 w-72 h-72 bg-linear-to-br from-blue-500/10 to-emerald-500/10 rounded-full blur-3xl floating-element">
    </div>
    <div class="absolute bottom-20 right-10 w-96 h-96 bg-linear-to-tl from-emerald-500/8 to-blue-500/8 rounded-full blur-3xl floating-element"
        style="animation-delay: 1s;"></div>
    <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-linear-to-r from-purple-500/5 to-pink-500/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 floating-element"
        style="animation-delay: 2s;"></div>

    <!-- Grid Pattern -->
    <div class="absolute inset-0 opacity-[0.02]">
        <div class="h-full w-full"
            style="background-image: radial-gradient(circle at 1px 1px, rgba(0,0,0,0.3) 1px, transparent 0); background-size: 20px 20px;">
        </div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Premium Section Header -->
        <div class="text-center mb-6" data-animate="header">
            <!-- Section Badge -->
            <div
                class="inline-flex items-center px-4 py-2 rounded-full bg-linear-to-r from-blue-500/10 to-emerald-500/10 border border-blue-200/30 backdrop-blur-xs mb-6">
                <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                    </path>
                </svg>
                <span class="text-sm font-medium text-blue-700">Handpicked Collection</span>
            </div>

            <!-- Main Heading -->
            <h2 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                <span class="bg-linear-to-r from-gray-900 via-blue-900 to-emerald-900 bg-clip-text text-transparent">
                    Featured
                </span>
                <br>
                <span class="bg-linear-to-r from-blue-600 to-emerald-600 bg-clip-text text-transparent">
                    Properties
                </span>
            </h2>

            <!-- Description -->
            <p class="text-xl text-gray-600 max-w-4xl mx-auto leading-relaxed font-light">
                Discover extraordinary properties from our curated collection of premium listings across Nigeria's most
                prestigious locations
            </p>

            <!-- Decorative Line -->
            <div class="flex items-center justify-center mt-8">
                <div class="h-px w-24 bg-linear-to-r from-transparent to-blue-300"></div>
                <div class="w-2 h-2 rounded-full bg-blue-500 mx-4"></div>
                <div class="h-px w-24 bg-linear-to-l from-transparent to-emerald-300"></div>
            </div>
        </div>

        <!-- 3D Perspective Carousel Container with Side Navigation -->
        <div class="relative h-[900px] flex items-center justify-between max-w-7xl mx-auto px-4" data-animate="grid">
            <!-- Dark Cloudy Background Behind Carousel Center -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="relative w-[600px] h-[700px]">
                    <!-- Main dark cloud effect -->
                    <div class="absolute inset-0 bg-linear-to-br from-gray-900/15 via-slate-800/10 to-gray-900/15 rounded-[50%] blur-[120px] opacity-70"></div>
                    <!-- Secondary cloud layers for depth -->
                    <div class="absolute top-10 left-10 w-96 h-96 bg-linear-to-br from-gray-800/12 to-slate-900/8 rounded-full blur-[100px] opacity-60"></div>
                    <div class="absolute bottom-10 right-10 w-80 h-80 bg-linear-to-tl from-slate-700/10 to-gray-800/12 rounded-full blur-[90px] opacity-50"></div>
                    <!-- Center focus cloud -->
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-linear-to-r from-gray-900/8 via-slate-800/12 to-gray-900/8 rounded-full blur-[80px] opacity-40"></div>
                </div>
            </div>

            <!-- Left Navigation Button -->
            <button id="carousel-prev"
                class="group relative p-4 bg-white/90 backdrop-blur-xs border border-white/50 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 shrink-0">
                <svg class="w-6 h-6 text-gray-600 group-hover:text-blue-600 transition-colors duration-300"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <div
                    class="absolute inset-0 bg-linear-to-br from-white/20 to-transparent rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                </div>
            </button>

            <!-- 3D Carousel Stage -->
            <div class="relative flex-1 h-full overflow-hidden mx-8"
                style="perspective: 1200px; perspective-origin: center center;" id="carousel-stage">
                <!-- Properties arranged in 3D space -->
                <div class="relative w-full h-full" style="transform-style: preserve-3d;" id="properties-carousel">
                    <!-- Property Card 1 -->
                    <div class="property-card absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white/20 backdrop-blur-xl border border-white/40 rounded-3xl shadow-2xl overflow-hidden w-96 h-[580px] transition-all duration-700 ease-out group"
                        data-index="0">
                        <!-- Enhanced Glassmorphism Effect -->
                        <div
                            class="absolute inset-0 bg-linear-to-br from-white/10 via-white/5 to-transparent rounded-3xl pointer-events-none">
                        </div>
                        <div
                            class="absolute inset-0 bg-linear-to-tl from-blue-500/5 via-transparent to-emerald-500/5 rounded-3xl pointer-events-none">
                        </div>
                        <div class="absolute inset-0 border border-white/20 rounded-3xl pointer-events-none"></div>

                        <div
                            class="relative h-60 bg-linear-to-br from-blue-50 to-emerald-50 overflow-hidden rounded-t-2xl">
                            <img src="https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=500&h=300&fit=crop"
                                alt="Modern 2BR Apartment"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                            <div
                                class="absolute inset-0 bg-linear-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>

                            <div class="absolute top-4 left-4">
                                <div
                                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/90 backdrop-blur-xs border border-white/50 shadow-lg">
                                    <div class="w-2 h-2 rounded-full bg-blue-500 mr-2"></div>
                                    <span class="text-xs font-semibold text-gray-700">Apartment</span>
                                </div>
                            </div>

                            <div class="absolute top-4 right-4">
                                <div
                                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-linear-to-r from-emerald-500 to-blue-500 text-white shadow-lg">
                                    <span class="text-sm font-bold">₦450,000</span>
                                </div>
                            </div>

                            <div class="absolute bottom-4 left-4">
                                <div
                                    class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-400/90 backdrop-blur-xs">
                                    <svg class="w-3 h-3 text-yellow-800 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="text-xs font-semibold text-yellow-800">Featured</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 relative">
                            <!-- Agent Profile Section -->
                            <div
                                class="flex items-center mb-4 p-3 bg-white/10 backdrop-blur-xs rounded-xl border border-white/20">
                                <div class="relative">
                                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face"
                                        alt="Agent"
                                        class="w-10 h-10 rounded-full object-cover border-2 border-white/30">
                                    <!-- Verified Badge -->
                                    <div
                                        class="absolute -top-1 -right-1 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center">
                                        <h4 class="text-sm font-semibold text-gray-800">Sarah Johnson</h4>
                                        <span
                                            class="ml-2 text-xs px-2 py-0.5 bg-blue-100/80 text-blue-700 rounded-full font-medium">Verified
                                            Agent</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-0.5">Premium Properties Ltd • 4.9★</p>
                                </div>
                            </div>

                            <h3
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-700 transition-colors duration-300">
                                Modern 2BR Apartment in Victoria Island
                            </h3>

                            <div class="flex items-center text-gray-600 mb-4">
                                <div
                                    class="w-5 h-5 rounded-full bg-linear-to-r from-blue-500 to-emerald-500 flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Lagos, Nigeria</span>
                            </div>

                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0">
                                        </path>
                                    </svg>
                                    <span class="text-sm text-gray-600">2 beds</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">2 baths</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                        </path>
                                    </svg>
                                    <span class="text-sm text-gray-600">85 m²</span>
                                </div>
                            </div>

                            <a href="/properties"
                                class="group/btn relative w-full inline-flex items-center justify-center px-6 py-3 bg-linear-to-r from-blue-600 to-emerald-600 text-white font-semibold rounded-xl shadow-lg transition-all duration-300 overflow-hidden">
                                <div class="absolute inset-0 bg-white/20 rounded-xl"></div>
                                <span class="relative z-10">View Details</span>
                                <svg class="relative z-10 w-4 h-4 ml-2 transform group-hover/btn:translate-x-1 transition-transform duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                                <div
                                    class="absolute inset-0 -top-1 -left-1 bg-linear-to-r from-transparent via-white/30 to-transparent skew-x-12 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700 rounded-xl">
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Property Card 2 -->
                    <div class="property-card absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white/20 backdrop-blur-xl border border-white/40 rounded-3xl shadow-2xl overflow-hidden w-96 h-[580px] transition-all duration-700 ease-out group"
                        data-index="1">
                        <!-- Enhanced Glassmorphism Effect -->
                        <div
                            class="absolute inset-0 bg-linear-to-br from-white/10 via-white/5 to-transparent rounded-3xl pointer-events-none">
                        </div>
                        <div
                            class="absolute inset-0 bg-linear-to-tl from-blue-500/5 via-transparent to-emerald-500/5 rounded-3xl pointer-events-none">
                        </div>
                        <div class="absolute inset-0 border border-white/20 rounded-3xl pointer-events-none"></div>

                        <div
                            class="relative h-60 bg-linear-to-br from-blue-50 to-emerald-50 overflow-hidden rounded-t-2xl">
                            <img src="https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=500&h=300&fit=crop"
                                alt="Luxury 3BR House"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                            <div
                                class="absolute inset-0 bg-linear-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>

                            <div class="absolute top-4 left-4">
                                <div
                                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/90 backdrop-blur-xs border border-white/50 shadow-lg">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></div>
                                    <span class="text-xs font-semibold text-gray-700">House</span>
                                </div>
                            </div>

                            <div class="absolute top-4 right-4">
                                <div
                                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-linear-to-r from-emerald-500 to-blue-500 text-white shadow-lg">
                                    <span class="text-sm font-bold">₦850,000</span>
                                </div>
                            </div>

                            <div class="absolute bottom-4 left-4">
                                <div
                                    class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-400/90 backdrop-blur-xs">
                                    <svg class="w-3 h-3 text-yellow-800 mr-1" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="text-xs font-semibold text-yellow-800">Featured</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 relative">
                            <!-- Agent Profile Section -->
                            <div
                                class="flex items-center mb-4 p-3 bg-white/10 backdrop-blur-xs rounded-xl border border-white/20">
                                <div class="relative">
                                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=40&h=40&fit=crop&crop=face"
                                        alt="Agent"
                                        class="w-10 h-10 rounded-full object-cover border-2 border-white/30">
                                    <!-- Verified Badge -->
                                    <div
                                        class="absolute -top-1 -right-1 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center">
                                        <h4 class="text-sm font-semibold text-gray-800">Michael Chen</h4>
                                        <span
                                            class="ml-2 text-xs px-2 py-0.5 bg-blue-100/80 text-blue-700 rounded-full font-medium">Verified
                                            Agent</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-0.5">Elite Realty Group • 4.8★</p>
                                </div>
                            </div>

                            <h3
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-700 transition-colors duration-300">
                                Luxury 3BR House in Maitama
                            </h3>

                            <div class="flex items-center text-gray-600 mb-4">
                                <div
                                    class="w-5 h-5 rounded-full bg-linear-to-r from-blue-500 to-emerald-500 flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Abuja, Nigeria</span>
                            </div>

                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0">
                                        </path>
                                    </svg>
                                    <span class="text-sm text-gray-600">3 beds</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">3 baths</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                        </path>
                                    </svg>
                                    <span class="text-sm text-gray-600">150 m²</span>
                                </div>
                            </div>

                            <a href="/properties"
                                class="group/btn relative w-full inline-flex items-center justify-center px-6 py-3 bg-linear-to-r from-blue-600 to-emerald-600 text-white font-semibold rounded-xl shadow-lg transition-all duration-300 overflow-hidden">
                                <div class="absolute inset-0 bg-white/20 rounded-xl"></div>
                                <span class="relative z-10">View Details</span>
                                <svg class="relative z-10 w-4 h-4 ml-2 transform group-hover/btn:translate-x-1 transition-transform duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                                <div
                                    class="absolute inset-0 -top-1 -left-1 bg-linear-to-r from-transparent via-white/30 to-transparent skew-x-12 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700 rounded-xl">
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Property Card 3 -->
                    <div class="property-card absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white/20 backdrop-blur-xl border border-white/40 rounded-3xl shadow-2xl overflow-hidden w-96 h-[580px] transition-all duration-700 ease-out group"
                        data-index="2">
                        <!-- Enhanced Glassmorphism Effect -->
                        <div
                            class="absolute inset-0 bg-linear-to-br from-white/10 via-white/5 to-transparent rounded-3xl pointer-events-none">
                        </div>
                        <div
                            class="absolute inset-0 bg-linear-to-tl from-blue-500/5 via-transparent to-emerald-500/5 rounded-3xl pointer-events-none">
                        </div>
                        <div class="absolute inset-0 border border-white/20 rounded-3xl pointer-events-none"></div>

                        <div
                            class="relative h-60 bg-linear-to-br from-blue-50 to-emerald-50 overflow-hidden rounded-t-2xl">
                            <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=500&h=300&fit=crop"
                                alt="Executive Duplex"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                            <div
                                class="absolute inset-0 bg-linear-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>

                            <div class="absolute top-4 left-4">
                                <div
                                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/90 backdrop-blur-xs border border-white/50 shadow-lg">
                                    <div class="w-2 h-2 rounded-full bg-purple-500 mr-2"></div>
                                    <span class="text-xs font-semibold text-gray-700">Duplex</span>
                                </div>
                            </div>

                            <div class="absolute top-4 right-4">
                                <div
                                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-linear-to-r from-emerald-500 to-blue-500 text-white shadow-lg">
                                    <span class="text-sm font-bold">₦1,200,000</span>
                                </div>
                            </div>

                            <div class="absolute bottom-4 left-4">
                                <div
                                    class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-400/90 backdrop-blur-xs">
                                    <svg class="w-3 h-3 text-yellow-800 mr-1" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="text-xs font-semibold text-yellow-800">Featured</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 relative">
                            <!-- Agent Profile Section -->
                            <div
                                class="flex items-center mb-4 p-3 bg-white/10 backdrop-blur-xs rounded-xl border border-white/20">
                                <div class="relative">
                                    <img src="https://images.unsplash.com/photo-1494790108755-2616b612b77c?w=40&h=40&fit=crop&crop=face"
                                        alt="Agent"
                                        class="w-10 h-10 rounded-full object-cover border-2 border-white/30">
                                    <!-- Verified Badge -->
                                    <div
                                        class="absolute -top-1 -right-1 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center">
                                        <h4 class="text-sm font-semibold text-gray-800">Diana Martinez</h4>
                                        <span
                                            class="ml-2 text-xs px-2 py-0.5 bg-blue-100/80 text-blue-700 rounded-full font-medium">Verified
                                            Agent</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-0.5">Luxury Homes Agency • 4.9★</p>
                                </div>
                            </div>

                            <h3
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-700 transition-colors duration-300">
                                Executive 4BR Duplex in GRA
                            </h3>

                            <div class="flex items-center text-gray-600 mb-4">
                                <div
                                    class="w-5 h-5 rounded-full bg-linear-to-r from-blue-500 to-emerald-500 flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Port Harcourt, Nigeria</span>
                            </div>

                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0">
                                        </path>
                                    </svg>
                                    <span class="text-sm text-gray-600">4 beds</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">4 baths</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                        </path>
                                    </svg>
                                    <span class="text-sm text-gray-600">220 m²</span>
                                </div>
                            </div>

                            <a href="/properties"
                                class="group/btn relative w-full inline-flex items-center justify-center px-6 py-3 bg-linear-to-r from-blue-600 to-emerald-600 text-white font-semibold rounded-xl shadow-lg transition-all duration-300 overflow-hidden">
                                <div class="absolute inset-0 bg-white/20 rounded-xl"></div>
                                <span class="relative z-10">View Details</span>
                                <svg class="relative z-10 w-4 h-4 ml-2 transform group-hover/btn:translate-x-1 transition-transform duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                                <div
                                    class="absolute inset-0 -top-1 -left-1 bg-linear-to-r from-transparent via-white/30 to-transparent skew-x-12 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700 rounded-xl">
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Property Card 4 -->
                    <div class="property-card absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white/20 backdrop-blur-xl border border-white/40 rounded-3xl shadow-2xl overflow-hidden w-96 h-[580px] transition-all duration-700 ease-out group"
                        data-index="3">
                        <!-- Enhanced Glassmorphism Effect -->
                        <div
                            class="absolute inset-0 bg-linear-to-br from-white/10 via-white/5 to-transparent rounded-3xl pointer-events-none">
                        </div>
                        <div
                            class="absolute inset-0 bg-linear-to-tl from-blue-500/5 via-transparent to-emerald-500/5 rounded-3xl pointer-events-none">
                        </div>
                        <div class="absolute inset-0 border border-white/20 rounded-3xl pointer-events-none"></div>

                        <div
                            class="relative h-60 bg-linear-to-br from-blue-50 to-emerald-50 overflow-hidden rounded-t-2xl">
                            <img src="https://images.unsplash.com/photo-1513584684374-8bab748fbf90?w=500&h=300&fit=crop"
                                alt="Luxury Penthouse"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                            <div
                                class="absolute inset-0 bg-linear-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>

                            <div class="absolute top-4 left-4">
                                <div
                                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/90 backdrop-blur-xs border border-white/50 shadow-lg">
                                    <div class="w-2 h-2 rounded-full bg-orange-500 mr-2"></div>
                                    <span class="text-xs font-semibold text-gray-700">Penthouse</span>
                                </div>
                            </div>

                            <div class="absolute top-4 right-4">
                                <div
                                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-linear-to-r from-emerald-500 to-blue-500 text-white shadow-lg">
                                    <span class="text-sm font-bold">₦2,500,000</span>
                                </div>
                            </div>

                            <div class="absolute bottom-4 left-4">
                                <div
                                    class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-400/90 backdrop-blur-xs">
                                    <svg class="w-3 h-3 text-yellow-800 mr-1" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="text-xs font-semibold text-yellow-800">Featured</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 relative">
                            <!-- Agent Profile Section -->
                            <div
                                class="flex items-center mb-4 p-3 bg-white/10 backdrop-blur-xs rounded-xl border border-white/20">
                                <div class="relative">
                                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=40&h=40&fit=crop&crop=face"
                                        alt="Agent"
                                        class="w-10 h-10 rounded-full object-cover border-2 border-white/30">
                                    <!-- Verified Badge -->
                                    <div
                                        class="absolute -top-1 -right-1 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center">
                                        <h4 class="text-sm font-semibold text-gray-800">James Wilson</h4>
                                        <span
                                            class="ml-2 text-xs px-2 py-0.5 bg-blue-100/80 text-blue-700 rounded-full font-medium">Verified
                                            Agent</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-0.5">Metropolitan Properties • 4.7★</p>
                                </div>
                            </div>

                            <h3
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-700 transition-colors duration-300">
                                Luxury Penthouse in Ikoyi
                            </h3>

                            <div class="flex items-center text-gray-600 mb-4">
                                <div
                                    class="w-5 h-5 rounded-full bg-linear-to-r from-blue-500 to-emerald-500 flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Lagos, Nigeria</span>
                            </div>

                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0">
                                        </path>
                                    </svg>
                                    <span class="text-sm text-gray-600">5 beds</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">5 baths</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                        </path>
                                    </svg>
                                    <span class="text-sm text-gray-600">350 m²</span>
                                </div>
                            </div>

                            <a href="/properties"
                                class="group/btn relative w-full inline-flex items-center justify-center px-6 py-3 bg-linear-to-r from-blue-600 to-emerald-600 text-white font-semibold rounded-xl shadow-lg transition-all duration-300 overflow-hidden">
                                <div class="absolute inset-0 bg-white/20 rounded-xl"></div>
                                <span class="relative z-10">View Details</span>
                                <svg class="relative z-10 w-4 h-4 ml-2 transform group-hover/btn:translate-x-1 transition-transform duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                                <div
                                    class="absolute inset-0 -top-1 -left-1 bg-linear-to-r from-transparent via-white/30 to-transparent skew-x-12 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700 rounded-xl">
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Property Card 5 -->
                    <div class="property-card absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white/20 backdrop-blur-xl border border-white/40 rounded-3xl shadow-2xl overflow-hidden w-96 h-[580px] transition-all duration-700 ease-out group"
                        data-index="4">
                        <!-- Enhanced Glassmorphism Effect -->
                        <div
                            class="absolute inset-0 bg-linear-to-br from-white/10 via-white/5 to-transparent rounded-3xl pointer-events-none">
                        </div>
                        <div
                            class="absolute inset-0 bg-linear-to-tl from-blue-500/5 via-transparent to-emerald-500/5 rounded-3xl pointer-events-none">
                        </div>
                        <div class="absolute inset-0 border border-white/20 rounded-3xl pointer-events-none"></div>

                        <div
                            class="relative h-60 bg-linear-to-br from-blue-50 to-emerald-50 overflow-hidden rounded-t-2xl">
                            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=500&h=300&fit=crop"
                                alt="Modern Studio"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                            <div
                                class="absolute inset-0 bg-linear-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>

                            <div class="absolute top-4 left-4">
                                <div
                                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/90 backdrop-blur-xs border border-white/50 shadow-lg">
                                    <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>
                                    <span class="text-xs font-semibold text-gray-700">Studio</span>
                                </div>
                            </div>

                            <div class="absolute top-4 right-4">
                                <div
                                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-linear-to-r from-emerald-500 to-blue-500 text-white shadow-lg">
                                    <span class="text-sm font-bold">₦300,000</span>
                                </div>
                            </div>

                            <div class="absolute bottom-4 left-4">
                                <div
                                    class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-400/90 backdrop-blur-xs">
                                    <svg class="w-3 h-3 text-yellow-800 mr-1" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="text-xs font-semibold text-yellow-800">Featured</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 relative">
                            <!-- Agent Profile Section -->
                            <div
                                class="flex items-center mb-4 p-3 bg-white/10 backdrop-blur-xs rounded-xl border border-white/20">
                                <div class="relative">
                                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&crop=face"
                                        alt="Agent"
                                        class="w-10 h-10 rounded-full object-cover border-2 border-white/30">
                                    <!-- Verified Badge -->
                                    <div
                                        class="absolute -top-1 -right-1 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center">
                                        <h4 class="text-sm font-semibold text-gray-800">Lisa Thompson</h4>
                                        <span
                                            class="ml-2 text-xs px-2 py-0.5 bg-blue-100/80 text-blue-700 rounded-full font-medium">Verified
                                            Agent</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-0.5">Urban Living Experts • 4.8★</p>
                                </div>
                            </div>

                            <h3
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-700 transition-colors duration-300">
                                Modern Studio in Surulere
                            </h3>

                            <div class="flex items-center text-gray-600 mb-4">
                                <div
                                    class="w-5 h-5 rounded-full bg-linear-to-r from-blue-500 to-emerald-500 flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Lagos, Nigeria</span>
                            </div>

                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0">
                                        </path>
                                    </svg>
                                    <span class="text-sm text-gray-600">Studio</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">1 bath</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                        </path>
                                    </svg>
                                    <span class="text-sm text-gray-600">45 m²</span>
                                </div>
                            </div>

                            <a href="/properties"
                                class="group/btn relative w-full inline-flex items-center justify-center px-6 py-3 bg-linear-to-r from-blue-600 to-emerald-600 text-white font-semibold rounded-xl shadow-lg transition-all duration-300 overflow-hidden">
                                <div class="absolute inset-0 bg-white/20 rounded-xl"></div>
                                <span class="relative z-10">View Details</span>
                                <svg class="relative z-10 w-4 h-4 ml-2 transform group-hover/btn:translate-x-1 transition-transform duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                                <div
                                    class="absolute inset-0 -top-1 -left-1 bg-linear-to-r from-transparent via-white/30 to-transparent skew-x-12 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700 rounded-xl">
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Property Card 6 -->
                    <div class="property-card absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white/20 backdrop-blur-xl border border-white/40 rounded-3xl shadow-2xl overflow-hidden w-96 h-[580px] transition-all duration-700 ease-out group"
                        data-index="5">
                        <!-- Enhanced Glassmorphism Effect -->
                        <div
                            class="absolute inset-0 bg-linear-to-br from-white/10 via-white/5 to-transparent rounded-3xl pointer-events-none">
                        </div>
                        <div
                            class="absolute inset-0 bg-linear-to-tl from-blue-500/5 via-transparent to-emerald-500/5 rounded-3xl pointer-events-none">
                        </div>
                        <div class="absolute inset-0 border border-white/20 rounded-3xl pointer-events-none"></div>

                        <div
                            class="relative h-60 bg-linear-to-br from-blue-50 to-emerald-50 overflow-hidden rounded-t-2xl">
                            <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=500&h=300&fit=crop"
                                alt="Family Townhouse"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                            <div
                                class="absolute inset-0 bg-linear-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>

                            <div class="absolute top-4 left-4">
                                <div
                                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/90 backdrop-blur-xs border border-white/50 shadow-lg">
                                    <div class="w-2 h-2 rounded-full bg-red-500 mr-2"></div>
                                    <span class="text-xs font-semibold text-gray-700">Townhouse</span>
                                </div>
                            </div>

                            <div class="absolute top-4 right-4">
                                <div
                                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-linear-to-r from-emerald-500 to-blue-500 text-white shadow-lg">
                                    <span class="text-sm font-bold">₦1,800,000</span>
                                </div>
                            </div>

                            <div class="absolute bottom-4 left-4">
                                <div
                                    class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-400/90 backdrop-blur-xs">
                                    <svg class="w-3 h-3 text-yellow-800 mr-1" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="text-xs font-semibold text-yellow-800">Featured</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 relative">
                            <!-- Agent Profile Section -->
                            <div
                                class="flex items-center mb-4 p-3 bg-white/10 backdrop-blur-xs rounded-xl border border-white/20">
                                <div class="relative">
                                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=40&h=40&fit=crop&crop=face"
                                        alt="Agent"
                                        class="w-10 h-10 rounded-full object-cover border-2 border-white/30">
                                    <!-- Verified Badge -->
                                    <div
                                        class="absolute -top-1 -right-1 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center">
                                        <h4 class="text-sm font-semibold text-gray-800">David Brown</h4>
                                        <span
                                            class="ml-2 text-xs px-2 py-0.5 bg-blue-100/80 text-blue-700 rounded-full font-medium">Verified
                                            Agent</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-0.5">Coastal Properties Inc • 4.9★</p>
                                </div>
                            </div>

                            <h3
                                class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-700 transition-colors duration-300">
                                Family Townhouse in Lekki
                            </h3>

                            <div class="flex items-center text-gray-600 mb-4">
                                <div
                                    class="w-5 h-5 rounded-full bg-linear-to-r from-blue-500 to-emerald-500 flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Lagos, Nigeria</span>
                            </div>

                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0">
                                        </path>
                                    </svg>
                                    <span class="text-sm text-gray-600">3 beds</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10v11M20 10v11"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">2 baths</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                        </path>
                                    </svg>
                                    <span class="text-sm text-gray-600">180 m²</span>
                                </div>
                            </div>

                            <a href="/properties"
                                class="group/btn relative w-full inline-flex items-center justify-center px-6 py-3 bg-linear-to-r from-blue-600 to-emerald-600 text-white font-semibold rounded-xl shadow-lg transition-all duration-300 overflow-hidden">
                                <div class="absolute inset-0 bg-white/20 rounded-xl"></div>
                                <span class="relative z-10">View Details</span>
                                <svg class="relative z-10 w-4 h-4 ml-2 transform group-hover/btn:translate-x-1 transition-transform duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                                <div
                                    class="absolute inset-0 -top-1 -left-1 bg-linear-to-r from-transparent via-white/30 to-transparent skew-x-12 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-700 rounded-xl">
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Navigation Button -->
            <button id="carousel-next"
                class="group relative p-4 bg-white/90 backdrop-blur-xs border border-white/50 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 shrink-0">
                <svg class="w-6 h-6 text-gray-600 group-hover:text-blue-600 transition-colors duration-300"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <div
                    class="absolute inset-0 bg-linear-to-br from-white/20 to-transparent rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                </div>
            </button>
        </div>

        <!-- Pagination Dots -->
        <div class="flex justify-center items-center mt-6">
            <div class="flex space-x-2" id="carousel-dots">
                <!-- Dots will be generated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Call-to-Action -->
    <div class="text-center mt-20" data-animate="cta">
        <div class="inline-flex flex-col items-center">
            <a href="/properties"
                class="group relative inline-flex items-center px-8 py-4 bg-linear-to-r from-blue-600 to-emerald-600 text-white font-semibold rounded-2xl shadow-xl transition-all duration-500">
                <div class="absolute inset-0 bg-white/20 rounded-2xl backdrop-blur-xs"></div>
                <span class="relative z-10 text-lg">Explore All Properties</span>
                <svg class="relative z-10 w-6 h-6 ml-3 transform group-hover:translate-x-1 transition-transform duration-300"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
                <div
                    class="absolute inset-0 -top-1 -left-1 bg-linear-to-r from-transparent via-white/30 to-transparent skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 rounded-2xl">
                </div>
            </a>
            <p class="text-sm text-gray-500 mt-4 font-light">
                Over <span class="font-medium text-blue-600">10,000+</span> verified properties waiting for you
            </p>
        </div>
    </div>
    </div>
</section>

<style>
    /* 3D Perspective Carousel - CSS First Approach */
    #carousel-stage {
        perspective: 1200px !important;
        perspective-origin: center center !important;
    }

    #properties-carousel {
        transform-style: preserve-3d !important;
    }

    /* Glassmorphism card effects */
    .property-card {
        backdrop-filter: blur(25px) saturate(1.5) !important;
        -webkit-backdrop-filter: blur(25px) saturate(1.5) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.25),
            0 0 0 1px rgba(255, 255, 255, 0.2) inset !important;
        background: rgba(255, 255, 255, 0.4) !important;
        transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94),
            background 0.5s ease,
            backdrop-filter 0.5s ease,
            -webkit-backdrop-filter 0.5s ease !important;
    }

    /* Default 3D positioning - cards start in proper 3D arrangement (larger scale) */
    .property-card[data-index="0"] {
        transform: translate(-50%, -50%) translateZ(0px) scale(1.1) !important;
        opacity: 1 !important;
        z-index: 20 !important;
    }

    .property-card[data-index="1"] {
        transform: translate(-50%, -50%) translateZ(-150px) translateX(250px) rotateY(-25deg) scale(0.9) !important;
        opacity: 0.8 !important;
        z-index: 15 !important;
    }

    .property-card[data-index="2"] {
        transform: translate(-50%, -50%) translateZ(-300px) translateX(400px) rotateY(-35deg) scale(0.75) !important;
        opacity: 0.6 !important;
        z-index: 10 !important;
    }

    .property-card[data-index="3"] {
        transform: translate(-50%, -50%) translateZ(-500px) translateX(550px) rotateY(45deg) scale(0.5) !important;
        opacity: 0.3 !important;
        z-index: 5 !important;
    }

    .property-card[data-index="4"] {
        transform: translate(-50%, -50%) translateZ(-500px) translateX(-550px) rotateY(45deg) scale(0.5) !important;
        opacity: 0.3 !important;
        z-index: 5 !important;
    }

    .property-card[data-index="5"] {
        transform: translate(-50%, -50%) translateZ(-300px) translateX(-400px) rotateY(35deg) scale(0.75) !important;
        opacity: 0.6 !important;
        z-index: 10 !important;
    }

    /* Enhanced glassmorphism hover effects */
    #properties-carousel .property-card:hover {
        filter: brightness(1.05) saturate(1.1) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35) !important;
    }

    /* Enhanced glassmorphism for center card (any card in center position) */
    .property-card.center-card {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(50px) saturate(2) !important;
        -webkit-backdrop-filter: blur(50px) saturate(2) !important;
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35),
            0 0 0 1px rgba(255, 255, 255, 0.5) inset,
            0 0 80px rgba(255, 255, 255, 0.5) inset,
            0 0 120px rgba(100, 200, 255, 0.3) !important;
        overflow: hidden !important;
    }

   /* Enhanced text readability for center card */
    .property-card.center-card h3,
    .property-card.center-card h4,
    .property-card.center-card p,
    .property-card.center-card span {
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
        position: relative !important;
        z-index: 10 !important;
    }
    /* Additional backdrop blur for center card content area */
    .property-card.center-card .p-6 {
        background: rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(30px) !important;
        -webkit-backdrop-filter: blur(30px) !important;
        border-radius: 0 0 24px 24px !important;
    }

    /* Opaque background overlay for center card to prevent text bleed-through */
    .property-card.center-card::before {
        content: '' !important;
        position: absolute !important;
        inset: 0 !important;
        background: linear-gradient(to bottom,
                rgba(255, 255, 255, 0.9) 0%,
                rgba(248, 250, 252, 0.95) 100%) !important;
        border-radius: 24px !important;
        z-index: 1 !important;
    }

    /* Ensure all content is above the background overlay */
    .property-card.center-card>* {
        position: relative !important;
        z-index: 2 !important;
    }

    /* Floating Elements Animation */
    @keyframes floating {

        0%,
        100% {
            transform: translateY(0px) rotate(0deg);
        }

        33% {
            transform: translateY(-20px) rotate(1deg);
        }

        66% {
            transform: translateY(-10px) rotate(-1deg);
        }
    }

    .floating-element {
        animation: floating 6s ease-in-out infinite;
    }

    /* Gradient Text */
    .bg-clip-text {
        -webkit-background-clip: text;
        background-clip: text;
    }

    /* Enhanced Property Card Hover Effects */
    .property-card {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 1 !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
    }

    .property-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        backdrop-filter: blur(25px) !important;
        -webkit-backdrop-filter: blur(25px) !important;
    }

    /* Agent verification badge animation */
    .property-card .agent-profile img {
        transition: all 0.3s ease;
    }

    .property-card:hover .agent-profile img {
        transform: scale(1.05);
    }

    /* Button Animations */
    .group/btn:hover {
        transform: scale(1.05) translateY(-2px);
        box-shadow: 0 20px 40px -12px rgba(59, 130, 246, 0.5);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .property-card:hover {
            transform: none;
        }
    }

    /* Shimmer effect for center card */
    .property-card.center-card::after {
        content: '' !important;
        position: absolute !important;
        top: -50% !important;
        left: -50% !important;
        width: 200% !important;
        height: 200% !important;
        background: linear-gradient(45deg,
                transparent 30%,
                rgba(255, 255, 255, 0.1) 50%,
                transparent 70%) !important;
        animation: shimmer 3s infinite !important;
        pointer-events: none !important;
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%) translateY(-100%) !important;
        }

        100% {
            transform: translateX(100%) translateY(100%) !important;
        }
    }
</style>
