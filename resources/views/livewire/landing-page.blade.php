@push('head')
    @if (app()->environment('production'))
        @include('components.analytics.google-tag')
    @endif
@endpush

<div class="min-h-screen bg-gray-50">
    <!-- Minimal Airbnb-Style Hero Section -->
    <section class="relative bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <!-- Minimal Header - Just Search -->
            <div class="text-center mb-6">
                <h1 class="text-2xl lg:text-3xl font-semibold text-gray-900 mb-4">
                    Find your dream home
                </h1>
            </div>

            <!-- Prominent Airbnb-Style Search Bar -->
            <div class="max-w-3xl mx-auto relative mb-6">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="searchQuery"
                        wire:focus="updateSuggestions"
                        placeholder="Search locations, property types, or areas"
                        class="w-full pl-6 pr-16 py-4 text-base bg-white border border-gray-300 rounded-full focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-lg hover:shadow-xl transition-all duration-200"
                        autocomplete="off"
                    >

                    <!-- Search Button -->
                    <div class="absolute inset-y-0 right-0 pr-2 flex items-center">
                        <button
                            onclick="window.location.href='/properties?q=' + encodeURIComponent(document.querySelector('input[wire\\:model\\.live\\.debounce\\.500ms=\"searchQuery\"]').value)"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white p-3 rounded-full transition-colors duration-200 shadow-lg"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Search Suggestions -->
                @if($showSuggestions && count($suggestions) > 0)
                    <div class="absolute w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl max-h-80 overflow-y-auto z-50">
                        @foreach($suggestions as $suggestion)
                            <div
                                wire:click="selectSuggestion({{ json_encode($suggestion) }})"
                                class="flex items-center px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors duration-200 border-b border-gray-100 last:border-b-0"
                            >
                                <div class="shrink-0 w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center mr-3">
                                    @if($suggestion['icon'] === 'location-dot')
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ $suggestion['text'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $suggestion['category'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Enhanced Quick Filters -->
            <div class="bg-white/90 backdrop-blur-xs border border-gray-200/50 rounded-xl p-3 mb-4 shadow-xs">
                <!-- Listing Type Filters (Always Visible) -->
                <div class="mb-3">
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        @foreach($filterOptions['listing_types'] as $listingType)
                            <button
                                wire:click="updateFilter('listing_type', '{{ $listingType['value'] }}')"
                                class="px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-200 {{ $selectedListingType === $listingType['value'] ? 'bg-emerald-600 text-white shadow-md' : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-300' }}"
                            >
                                {{ $listingType['label'] }}
                            </button>
                        @endforeach

                        <!-- More Filters Toggle Button (Mobile Only) -->
                        <button
                            wire:click="toggleMobileFilters"
                            class="md:hidden px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-200 bg-gray-100 border border-gray-300 text-gray-700 hover:bg-gray-200"
                        >
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4 transform transition-transform duration-200 {{ $showMobileFilters ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                <span>{{ $showMobileFilters ? 'Less' : 'More' }} Filters</span>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Additional Filters Row (Collapsible on Mobile) -->
                <div class="md:grid md:grid-cols-6 md:gap-2 {{ $showMobileFilters ? 'block space-y-2' : 'hidden md:block' }}">
                    <!-- Bedrooms Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Bedrooms</label>
                        <div class="flex flex-wrap gap-1">
                            @foreach($filterOptions['bedrooms'] as $bedroom)
                                <button
                                    wire:click="updateFilter('bedrooms', '{{ $bedroom['value'] }}')"
                                    class="px-2 py-1 rounded-sm text-xs font-medium transition-all duration-200 {{ $selectedBedrooms === $bedroom['value'] ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-blue-100' }}"
                                >
                                    @if($bedroom['value'] === '1')
                                        1BR
                                    @elseif($bedroom['value'] === '2')
                                        2BR
                                    @elseif($bedroom['value'] === '3')
                                        3BR
                                    @elseif($bedroom['value'] === '4')
                                        4BR
                                    @elseif($bedroom['value'] === '5+')
                                        5+BR
                                    @else
                                        {{ $bedroom['label'] }}
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Property Type Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Property Type</label>
                        <select
                            wire:model.live="selectedPropertyType"
                            class="w-full px-2 py-1 text-xs border border-gray-300 rounded-sm focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                            <option value="">All Types</option>
                            @foreach($filterOptions['property_types'] as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- State Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">State</label>
                        <select
                            wire:model.live="selectedState"
                            class="w-full px-2 py-1 text-xs border border-gray-300 rounded-sm focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                            <option value="">All States</option>
                            @foreach($filterOptions['states'] as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- City Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">City</label>
                        <select
                            wire:model.live="selectedCity"
                            class="w-full px-2 py-1 text-xs border border-gray-300 rounded-sm focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                            {{ !$selectedState ? 'disabled' : '' }}
                        >
                            <option value="">{{ $selectedState ? 'All Cities' : 'Select State' }}</option>
                            @if($selectedState && isset($filterOptions['cities']))
                                @foreach($filterOptions['cities'] as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Area Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Area</label>
                        <select
                            wire:model.live="selectedArea"
                            class="w-full px-2 py-1 text-xs border border-gray-300 rounded-sm focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                            {{ !$selectedCity ? 'disabled' : '' }}
                        >
                            <option value="">{{ $selectedCity ? 'All Areas' : 'Select City' }}</option>
                            @if($selectedCity && isset($filterOptions['areas']))
                                @foreach($filterOptions['areas'] as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Price Range Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Price Range</label>
                        <div class="space-y-1">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>₦0</span>
                                <span>₦50M</span>
                                <span>₦100M+</span>
                            </div>
                            <input
                                type="range"
                                wire:model.live="selectedPriceRange"
                                min="0"
                                max="100000000"
                                step="5000000"
                                class="w-full h-1 bg-gray-200 rounded-sm appearance-none cursor-pointer slider"
                            >
                            <div class="text-center">
                                <span class="text-xs font-medium text-gray-700">
                                    @if($selectedPriceRange && $selectedPriceRange > 0)
                                        Up to ₦{{ number_format($selectedPriceRange / 1000000) }}M
                                    @else
                                        Any Price
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clear Filters Button -->
                @if($selectedListingType || $selectedPropertyType || $selectedBedrooms || $selectedState || $selectedCity || $selectedArea || $selectedPriceRange || $searchQuery)
                    <div class="mt-3 pt-2 border-t border-gray-200 text-center {{ $showMobileFilters ? 'block' : 'hidden md:block' }}">
                        <button
                            wire:click="clearAllFilters"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-red-600 bg-gray-50 hover:bg-red-50 border border-gray-200 hover:border-red-200 rounded-lg transition-all duration-200"
                        >
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear All
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Property Grid Section -->
    <section class="py-6 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($properties->count() > 0)
                <!-- Properties Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($properties as $property)
                        <a href="{{ route('property.show', $property->slug ?? $property->id) }}"
                           class="group block transition-all duration-200 hover:scale-105">

                            <!-- Property Image -->
                            <div class="relative h-64 overflow-hidden rounded-xl mb-3">
                                @if($property->getMedia('featured')->count() > 0)
                                    <img src="{{ $property->getFirstMedia('featured')->getUrl() }}"
                                         alt="{{ $property->title }}"
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                @elseif($property->getMedia('gallery')->count() > 0)
                                    <img src="{{ $property->getMedia('gallery')->first()->getUrl() }}"
                                         alt="{{ $property->title }}"
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Verified Badge -->
                                @if($property->is_verified)
                                    <div class="absolute top-3 left-3">
                                        <div class="inline-flex items-center space-x-1.5 bg-blue-100/90 backdrop-blur-xs border border-blue-300/50 text-blue-700 px-2.5 py-1 rounded-lg font-bold text-xs shadow-lg">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Verified</span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Listing Type Badge -->
                                <div class="absolute top-3 right-3">
                                    @if($property->listing_type === 'rent')
                                        <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 bg-linear-to-r from-emerald-100/90 to-green-100/90 backdrop-blur-xs border border-emerald-300/50 text-emerald-700 text-xs font-bold rounded-lg shadow-lg">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"></path>
                                            </svg>
                                            <span>For Rent</span>
                                        </div>
                                    @elseif($property->listing_type === 'sale')
                                        <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 bg-linear-to-r from-blue-100/90 to-indigo-100/90 backdrop-blur-xs border border-blue-300/50 text-blue-700 text-xs font-bold rounded-lg shadow-lg">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                            <span>For Sale</span>
                                        </div>
                                    @elseif($property->listing_type === 'lease')
                                        <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 bg-linear-to-r from-purple-100/90 to-pink-100/90 backdrop-blur-xs border border-purple-300/50 text-purple-700 text-xs font-bold rounded-lg shadow-lg">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <span>For Lease</span>
                                        </div>
                                    @elseif($property->listing_type === 'shortlet')
                                        <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 bg-linear-to-r from-yellow-100/90 to-orange-100/90 backdrop-blur-xs border border-yellow-300/50 text-yellow-700 text-xs font-bold rounded-lg shadow-lg">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 2L3 7v11a2 2 0 002 2h4v-6h2v6h4a2 2 0 002-2V7l-7-5z"/>
                                            </svg>
                                            <span>Shortlet</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Heart Icon - moved to bottom right -->
                                <div class="absolute bottom-3 right-3">
                                    <button
                                        wire:click.stop="toggleSaveProperty({{ $property->id }})"
                                        onclick="event.stopPropagation(); event.preventDefault();"
                                        class="p-2 bg-white/90 hover:bg-white rounded-full hover:scale-110 transition-all duration-200 shadow-lg"
                                        title="{{ $this->isPropertySaved($property->id) ? 'Remove from saved' : 'Save property' }}"
                                    >
                                        @if($this->isPropertySaved($property->id))
                                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-600 hover:text-red-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                        @endif
                                    </button>
                                </div>
                            </div>

                            <!-- Property Details (compact stool-like info) -->
                            <div class="relative -mt-2 bg-white/95 backdrop-blur-xs border border-gray-100/80 rounded-xl shadow-xs px-3 py-2 space-y-1">
                                <!-- Location (compact) -->
                                <div class="text-xs text-gray-600 font-medium truncate">
                                    {{ $property->city->name ?? 'Unknown' }}, {{ $property->state->name ?? 'Unknown' }}
                                </div>

                                <!-- Title (compact) -->
                                <div class="text-sm text-gray-900 font-semibold line-clamp-1 group-hover:text-emerald-600 transition-colors duration-200">
                                    {{ $property->title }}
                                </div>

                                <!-- Bedroom info (compact) -->
                                @if($property->bedrooms)
                                <div class="text-xs text-gray-600 font-medium">
                                    {{ $property->bedrooms }} {{ $property->bedrooms == 1 ? 'Bedroom' : 'Bedrooms' }}
                                </div>
                                @endif

                                <!-- Price (compact but prominent) -->
                                <div class="flex items-baseline justify-between pt-1">
                                    <span class="text-lg font-bold text-gray-900 bg-linear-to-r from-emerald-600 to-blue-600 bg-clip-text text-transparent">
                                        ₦{{ number_format($property->price) }}
                                    </span>
                                    @if($property->listing_type === 'rent')
                                        <span class="text-xs text-gray-500 font-medium">per year</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Custom Pagination -->
                <div class="mt-12 flex justify-center">
                    @if($properties->hasPages())
                        <nav class="flex items-center space-x-2" role="navigation" aria-label="Pagination Navigation">
                            {{-- Previous Page Link --}}
                            @if ($properties->onFirstPage())
                                <span class="flex items-center justify-center w-10 h-10 text-gray-400 bg-gray-100/80 border border-gray-200/50 rounded-xl cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $properties->previousPageUrl() }}"
                                   class="flex items-center justify-center w-10 h-10 text-gray-700 bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-xl transition-all duration-200 hover:scale-105 shadow-xs hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </a>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($properties->getUrlRange(1, $properties->lastPage()) as $page => $url)
                                @if ($page == $properties->currentPage())
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
                            @if ($properties->hasMorePages())
                                <a href="{{ $properties->nextPageUrl() }}"
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No properties found</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your search criteria or browse all available properties.</p>
                    <button
                        wire:click="clearAllFilters"
                        onclick="window.location.href='/properties'"
                        class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors duration-200"
                    >
                        Browse All Properties
                    </button>
                </div>
            @endif
        </div>
    </section>

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
</div>

@push('styles')
<style>
/* Line Clamp Utility */
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom Range Slider Styling */
.slider::-webkit-slider-thumb {
    appearance: none;
    height: 16px;
    width: 16px;
    border-radius: 50%;
    background: #059669;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.slider::-moz-range-thumb {
    height: 16px;
    width: 16px;
    border-radius: 50%;
    background: #059669;
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.slider::-webkit-slider-track {
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
}

.slider::-moz-range-track {
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    border: none;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('livewire:initialized', () => {
    // Toast notification handlers
    Livewire.on('property-saved', (data) => {
        showToast('Property saved successfully!', 'success');
    });

    Livewire.on('property-unsaved', (data) => {
        showToast('Property removed from saved list', 'info');
    });

    // Toast notification function
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');

        const bgColor = type === 'success' ? 'bg-emerald-500' :
                       type === 'error' ? 'bg-red-500' :
                       'bg-blue-500';

        toast.className = `flex items-center space-x-3 ${bgColor} text-white px-6 py-4 rounded-xl shadow-lg transform translate-x-full transition-transform duration-300`;
        toast.innerHTML = `
            <div class="shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span class="font-medium">${message}</span>
            <button onclick="this.parentElement.remove()" class="shrink-0 ml-4 hover:bg-white/20 rounded-sm p-1 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;

        container.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.parentElement.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }

    // Auto-hide suggestions when clicking outside
    document.addEventListener('click', (e) => {
        const searchInput = document.querySelector('input[wire\\:model\\.live\\.debounce\\.500ms="searchQuery"]');
        const suggestionsDropdown = document.querySelector('.absolute.z-50');

        if (searchInput && !searchInput.contains(e.target) &&
            (!suggestionsDropdown || !suggestionsDropdown.contains(e.target))) {
            @this.hideSuggestions();
        }
    });
});
</script>
@endpush