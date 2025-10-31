<div class="min-h-screen bg-gray-50">
    <!-- Minimal Airbnb-Style Hero Section -->
    <section class="relative bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <!-- Minimal Header - Just Search -->
            <div class="text-center mb-8">
                <h1 class="text-2xl lg:text-3xl font-semibold text-gray-900 mb-6">
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
                                <div class="flex-shrink-0 w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center mr-3">
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
            <div class="bg-white/90 backdrop-blur-sm border border-gray-200/50 rounded-2xl p-4 mb-6 shadow-sm">
                <!-- Listing Type Filters (Always Visible) -->
                <div class="mb-4">
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        @foreach($filterOptions['listing_types'] as $listingType)
                            <button
                                wire:click="updateFilter('listing_type', '{{ $listingType['value'] }}')"
                                class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 {{ $selectedListingType === $listingType['value'] ? 'bg-emerald-600 text-white shadow-md' : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-300' }}"
                            >
                                {{ $listingType['label'] }}
                            </button>
                        @endforeach

                        <!-- More Filters Toggle Button (Mobile Only) -->
                        <button
                            wire:click="toggleMobileFilters"
                            class="md:hidden px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 bg-gray-100 border border-gray-300 text-gray-700 hover:bg-gray-200"
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
                <div class="md:grid md:grid-cols-5 md:gap-3 {{ $showMobileFilters ? 'block space-y-3' : 'hidden md:block' }}">
                    <!-- Bedrooms Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-2">Bedrooms</label>
                        <div class="flex flex-wrap gap-1">
                            @foreach($filterOptions['bedrooms'] as $bedroom)
                                <button
                                    wire:click="updateFilter('bedrooms', '{{ $bedroom['value'] }}')"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 {{ $selectedBedrooms === $bedroom['value'] ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-blue-100' }}"
                                >
                                    {{ $bedroom['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Property Type Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-2">Property Type</label>
                        <select
                            wire:model.live="selectedPropertyType"
                            class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                            <option value="">All Types</option>
                            @foreach($filterOptions['property_types'] as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Furnishing Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-2">Furnishing</label>
                        <select
                            wire:model.live="selectedFurnishing"
                            class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                            <option value="">Any</option>
                            @foreach($filterOptions['furnishing_types'] as $furnishing)
                                <option value="{{ $furnishing['value'] }}">{{ $furnishing['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-2">Price Range</label>
                        <select
                            wire:model.live="selectedPriceRange"
                            class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                            <option value="">Any Price</option>
                            @foreach($filterOptions['price_ranges'] as $priceRange)
                                <option value="{{ $priceRange['value'] }}">{{ $priceRange['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-2">Popular Cities</label>
                        <select
                            wire:model.live="selectedLocation"
                            class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        >
                            <option value="">All Cities</option>
                            @foreach($filterOptions['popular_locations'] as $location)
                                <option value="{{ $location['value'] }}">{{ $location['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Clear Filters Button -->
                @if($selectedListingType || $selectedPropertyType || $selectedBedrooms || $selectedFurnishing || $selectedPriceRange || $selectedLocation || $searchQuery)
                    <div class="mt-4 text-center {{ $showMobileFilters ? 'block' : 'hidden md:block' }}">
                        <button
                            wire:click="clearAllFilters"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear All Filters
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
                                        <div class="inline-flex items-center space-x-1.5 bg-blue-100/90 backdrop-blur-sm border border-blue-300/50 text-blue-700 px-2.5 py-1 rounded-lg font-bold text-xs shadow-lg">
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
                                        <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 bg-gradient-to-r from-emerald-100/90 to-green-100/90 backdrop-blur-sm border border-emerald-300/50 text-emerald-700 text-xs font-bold rounded-lg shadow-lg">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z"></path>
                                            </svg>
                                            <span>For Rent</span>
                                        </div>
                                    @elseif($property->listing_type === 'sale')
                                        <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 bg-gradient-to-r from-blue-100/90 to-indigo-100/90 backdrop-blur-sm border border-blue-300/50 text-blue-700 text-xs font-bold rounded-lg shadow-lg">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                            <span>For Sale</span>
                                        </div>
                                    @elseif($property->listing_type === 'lease')
                                        <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 bg-gradient-to-r from-purple-100/90 to-pink-100/90 backdrop-blur-sm border border-purple-300/50 text-purple-700 text-xs font-bold rounded-lg shadow-lg">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <span>For Lease</span>
                                        </div>
                                    @elseif($property->listing_type === 'shortlet')
                                        <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 bg-gradient-to-r from-yellow-100/90 to-orange-100/90 backdrop-blur-sm border border-yellow-300/50 text-yellow-700 text-xs font-bold rounded-lg shadow-lg">
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
                            <div class="relative -mt-2 bg-white/95 backdrop-blur-sm border border-gray-100/80 rounded-xl shadow-sm px-3 py-2 space-y-1">
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
                                    <span class="text-lg font-bold text-gray-900 bg-gradient-to-r from-emerald-600 to-blue-600 bg-clip-text text-transparent">
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
                                   class="flex items-center justify-center w-10 h-10 text-gray-700 bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-xl transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </a>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($properties->getUrlRange(1, $properties->lastPage()) as $page => $url)
                                @if ($page == $properties->currentPage())
                                    <span class="flex items-center justify-center w-10 h-10 text-white font-semibold bg-gradient-to-r from-emerald-500 to-blue-500 rounded-xl shadow-md">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                       class="flex items-center justify-center w-10 h-10 text-gray-700 font-medium bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-xl transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($properties->hasMorePages())
                                <a href="{{ $properties->nextPageUrl() }}"
                                   class="flex items-center justify-center w-10 h-10 text-gray-700 bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-xl transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md">
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
            <div class="flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span class="font-medium">${message}</span>
            <button onclick="this.parentElement.remove()" class="flex-shrink-0 ml-4 hover:bg-white/20 rounded p-1 transition-colors">
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