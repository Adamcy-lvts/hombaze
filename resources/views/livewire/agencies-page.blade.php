<div class="min-h-screen bg-gray-50">
    <!-- Clean Header Section -->
    <section class="relative bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <!-- Breadcrumb -->
            <nav aria-label="Breadcrumb" class="mb-6">
                <ol
                    class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 overflow-x-auto whitespace-nowrap px-3 py-2 border border-gray-200 rounded-xl bg-white shadow-xs shadow-gray-200/70 w-full">
                    <li>
                        <a href="{{ route('landing') }}" wire:navigate
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:text-emerald-600 hover:border-emerald-300 transition">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                            <span class="font-medium">Home</span>
                        </a>
                    </li>
                    <li class="flex items-center shrink-0">
                        <div class="w-8 sm:w-12 h-0.5 bg-linear-to-r from-gray-300 to-gray-400 rounded-full"></div>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 -ml-1" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5l7 7-7 7"></path>
                        </svg>
                    </li>
                    <li class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 7h18M3 12h18M3 17h18"></path>
                        </svg>
                        <span class="font-semibold">Real Estate Agencies</span>
                    </li>
                </ol>
            </nav>

            <!-- Clean Header -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-6">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-semibold text-gray-900 mb-2">
                        Find Trusted <span class="text-emerald-600">Real Estate</span> Agencies
                    </h1>
                    <p class="text-gray-600">Partner with verified, established agencies across Nigeria</p>
                </div>

                <!-- Results Count -->
                <div class="flex items-center space-x-3 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                    <span class="font-semibold text-gray-900">{{ $agencies->total() }}</span>
                    <span class="text-gray-600">agencies found</span>
                </div>
            </div>

            <!-- Clean Search Bar -->
            <div class="max-w-3xl mx-auto relative mb-6">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="searchQuery"
                        placeholder="Search agencies by name, description, or location..."
                        class="w-full pl-6 pr-16 py-4 text-base bg-white border border-gray-300 rounded-full focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-lg hover:shadow-xl transition-all duration-200"
                        autocomplete="off"
                    >

                    <!-- Search Button -->
                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center">
                        <button class="bg-emerald-600 hover:bg-emerald-700 text-white p-3 rounded-full transition-colors duration-200 shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Clean Filters -->
            <div class="bg-white/90 backdrop-blur-xs border border-gray-200/50 rounded-xl p-3 mb-4 shadow-xs">
                <!-- Filter Controls -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <!-- Filter Toggle -->
                    <button
                        wire:click="toggleFilters"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $showFilters ? 'bg-emerald-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-300' }}"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                        </svg>
                        {{ $showFilters ? 'Hide Filters' : 'Show Filters' }}
                    </button>

                    <!-- Sort Options -->
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-700">Sort by:</span>
                        <select
                            wire:model.live="sortBy"
                            class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                            <option value="rating">Highest Rated</option>
                            <option value="experience">Most Experienced</option>
                            <option value="properties">Most Properties</option>
                            <option value="agents">Most Agents</option>
                            <option value="newest">Newest Agencies</option>
                        </select>
                    </div>
                </div>

                <!-- Expanded Filters Panel -->
                @if($showFilters)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Location Filter -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-2">Location</label>
                                <select wire:model.live="selectedLocation" class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">All Locations</option>
                                    @foreach($locationOptions as $state)
                                        <option value="{{ $state->id }}">{{ $state->name }} ({{ $state->agencies_count }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Agency Size Filter -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-2">Agency Size</label>
                                <select wire:model.live="agencySizeFilter" class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">All Sizes</option>
                                    <option value="small">Small (1-5 agents)</option>
                                    <option value="medium">Medium (6-20 agents)</option>
                                    <option value="large">Large (21+ agents)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Clean Agencies Grid Section -->
    <section class="py-6 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($agencies->count() > 0)
            <!-- Agencies Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($agencies as $agency)
                    <a href="#" wire:navigate
                       class="group block transition-all duration-200 hover:scale-105">

                        <!-- Agency Card -->
                        <div class="relative bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                            <!-- Agency Logo/Header -->
                            <div class="relative h-48 bg-linear-to-br from-emerald-50 to-blue-50 flex items-center justify-center">
                                @if($agency->logo)
                                    <img src="{{ Storage::url($agency->logo) }}"
                                         alt="{{ $agency->name }}"
                                         class="w-24 h-24 rounded-full border-4 border-white shadow-lg object-cover">
                                @else
                                    <div class="w-24 h-24 bg-linear-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                                        <span class="text-white font-bold text-2xl">{{ substr($agency->name, 0, 1) }}</span>
                                    </div>
                                @endif

                                <!-- Verified Badge -->
                                @if($agency->is_verified)
                                    <div class="absolute top-3 right-3">
                                        <div class="inline-flex items-center space-x-1 bg-blue-100/90 backdrop-blur-xs border border-blue-300/50 text-blue-700 px-2 py-1 rounded-lg font-bold text-xs shadow-lg">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Verified</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Agency Details -->
                            <div class="p-4 space-y-2">
                                <!-- Name -->
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-emerald-600 transition-colors duration-200 truncate">
                                    {{ $agency->name }}
                                </h3>

                                <!-- Location -->
                                <p class="text-sm text-gray-600">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $agency->state->name ?? 'Nigeria' }}
                                </p>

                                <!-- Rating & Experience -->
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <div class="flex items-center space-x-1">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-3 h-3 {{ $i <= ($agency->rating ?? 0) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="font-medium">{{ number_format($agency->rating ?? 0, 1) }}</span>
                                    </div>
                                    <span>{{ $agency->years_in_business ?? 0 }}y exp</span>
                                </div>

                                <!-- Stats & Contact -->
                                <div class="flex items-center justify-between text-xs text-gray-500 pt-2">
                                    <div class="flex space-x-3">
                                        <span class="flex items-center space-x-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span>{{ $agency->agents->count() }} agents</span>
                                        </span>
                                        <span class="flex items-center space-x-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            <span>{{ $agency->properties->count() }} listings</span>
                                        </span>
                                    </div>
                                    <div class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-md text-xs font-medium">
                                        View Agency
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

                <!-- Custom Pagination -->
                <div class="mt-12 flex justify-center">
                    @if($agencies->hasPages())
                        <nav class="flex items-center space-x-2" role="navigation" aria-label="Pagination Navigation">
                            {{-- Previous Page Link --}}
                            @if ($agencies->onFirstPage())
                                <span class="flex items-center justify-center w-10 h-10 text-gray-400 bg-gray-100/80 border border-gray-200/50 rounded-xl cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $agencies->previousPageUrl() }}"
                                   class="flex items-center justify-center w-10 h-10 text-gray-700 bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-xl transition-all duration-200 hover:scale-105 shadow-xs hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </a>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($agencies->getUrlRange(1, $agencies->lastPage()) as $page => $url)
                                @if ($page == $agencies->currentPage())
                                    <span class="flex items-center justify-center w-10 h-10 text-white font-semibold bg-linear-to-r from-emerald-500 to-blue-500 rounded-xl shadow-md">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                       class="flex items-center justify-center w-10 h-10 text-gray-700 font-medium bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-xl transition-all duration-200 hover:scale-105 shadow-xs hover:shadow-md">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($agencies->hasMorePages())
                                <a href="{{ $agencies->nextPageUrl() }}"
                                   class="flex items-center justify-center w-10 h-10 text-gray-700 bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-xl transition-all duration-200 hover:scale-105 shadow-xs hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @else
                                <span class="flex items-center justify-center w-10 h-10 text-gray-400 bg-gray-100/80 border border-gray-200/50 rounded-xl cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @endif
                        </nav>
                    @endif
                </div>
            @else
                <!-- No Results -->
                <div class="text-center py-16">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h3v3H7V7z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No agencies found</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your search criteria or browse all available agencies.</p>
                    <button
                        wire:click="clearFilters"
                        class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors duration-200"
                    >
                        Browse All Agencies
                    </button>
                </div>
            @endif
        </div>
    </section>
</div>