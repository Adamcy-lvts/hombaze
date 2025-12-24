<div class="min-h-screen bg-gray-50 font-sans text-gray-900">
    <!-- Clean Header Section -->
    <section class="relative bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <!-- Breadcrumb -->
            <div class="max-w-7xl mb-6 lg:mb-8">
                <nav class="flex items-center" aria-label="Breadcrumb">
                    <ol class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 overflow-x-auto whitespace-nowrap px-4 py-2.5 border border-gray-100 rounded-2xl bg-white shadow-sm w-full sm:w-auto">
                        <li>
                            <a href="{{ route('landing') }}"
                                class="inline-flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-gray-50 text-gray-600 hover:text-emerald-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                                <span class="font-medium">Home</span>
                            </a>
                        </li>
                        <li class="flex items-center shrink-0 text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </li>
                        <li>
                            <span class="inline-flex items-center gap-2 px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-semibold">
                                Properties
                            </span>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Clean Header -->
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-8">
                <div class="max-w-2xl">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3 tracking-tight">
                        Find Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">Perfect</span> Property
                    </h1>
                    <p class="text-lg text-gray-600 leading-relaxed">Discover premium properties across Nigeria's most desirable locations.</p>
                </div>

                <!-- Results Count -->
                <div class="flex items-center space-x-3 bg-white border border-gray-100 px-5 py-2.5 rounded-2xl shadow-sm">
                    <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></div>
                    <span class="font-bold text-gray-900">{{ $properties->total() }}</span>
                    <span class="text-gray-500 font-medium">properties found</span>
                </div>
            </div>

            <!-- Clean Search Bar -->
            <div class="max-w-4xl mx-auto relative mb-8 z-30">
                @if ($isRateLimited)
                    <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        {{ $rateLimitMessage }}
                    </div>
                @endif
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full opacity-20 group-hover:opacity-30 blur transition duration-200"></div>
                    <div class="relative flex items-center bg-white rounded-full shadow-lg">
                        <div class="pl-6 text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.500ms="searchQuery" wire:focus="updateSuggestions"
                            placeholder="Search by location, property type, or features..."
                            class="w-full pl-4 pr-16 py-4 text-lg bg-transparent border-none focus:ring-0 text-gray-900 placeholder-gray-400 rounded-full"
                            autocomplete="off">
                        
                        <div class="pr-2">
                            <button class="bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white p-3 rounded-full transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search Suggestions -->
                @if ($showSuggestions && count($suggestions) > 0)
                    <div class="absolute w-full mt-3 bg-white border border-gray-100 rounded-2xl shadow-xl max-h-96 overflow-y-auto z-50 overflow-hidden">
                        @foreach ($suggestions as $suggestion)
                            <div wire:click="selectSuggestion({{ json_encode($suggestion) }})"
                                class="flex items-center px-5 py-3.5 hover:bg-gray-50 cursor-pointer transition-colors duration-200 border-b border-gray-50 last:border-b-0 group">
                                <div class="shrink-0 w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mr-4 group-hover:bg-emerald-100 group-hover:scale-110 transition-all duration-200">
                                    @if ($suggestion['icon'] === 'location-dot')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">{{ $suggestion['text'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $suggestion['category'] }}</div>
                                </div>
                                <div class="text-gray-300 group-hover:text-emerald-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Clean Filters -->
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <!-- Active Filters Display -->
                @if (count($activeFilters) > 0)
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mr-2">Active filters:</span>
                        @foreach ($activeFilters as $key => $filter)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                {{ $filter['label'] }}
                                <button wire:click="removeFilter('{{ $key }}')"
                                    class="ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full text-emerald-500 hover:bg-emerald-200 hover:text-emerald-800 transition-colors duration-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </span>
                        @endforeach
                        <button wire:click="clearAllFilters"
                            class="text-xs font-medium text-gray-500 hover:text-red-600 transition-colors duration-200 ml-2 underline decoration-gray-300 hover:decoration-red-300">
                            Clear all
                        </button>
                    </div>
                @endif

                <!-- Filter Controls -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <!-- Filter Toggle -->
                    <button wire:click="toggleFilters"
                        class="inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 {{ $showFilters ? 'bg-gray-900 text-white shadow-md' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300' }}">
                        <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z">
                            </path>
                        </svg>
                        {{ $showFilters ? 'Hide Filters' : 'Show Filters' }}
                        @if (count($activeFilters) > 0)
                            <span class="ml-2 bg-white/20 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                                {{ count($activeFilters) }}
                            </span>
                        @endif
                    </button>

                    <!-- Sort Options -->
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-600">Sort by:</span>
                        <div class="relative">
                            <select wire:model.live="sortBy"
                                class="appearance-none pl-4 pr-10 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-medium text-gray-900 cursor-pointer hover:bg-white transition-colors">
                                <option value="relevance">Relevance</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                                <option value="newest">Newest First</option>
                                <option value="popular">Most Popular</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expanded Filters Panel -->
                @if ($showFilters)
                    <div class="mt-6 pt-6 border-t border-gray-100" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                            <!-- Listing Type -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Listing Type</label>
                                <div class="space-y-2.5">
                                    @foreach ($filterOptions['listing_type'] as $type)
                                        <label class="flex items-center group cursor-pointer">
                                            <div class="relative flex items-center">
                                                <input type="checkbox" wire:model.live="selectedListingTypes"
                                                    value="{{ $type }}"
                                                    class="peer h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 transition duration-150 ease-in-out">
                                            </div>
                                            <span class="ml-3 text-sm text-gray-700 group-hover:text-emerald-700 transition-colors">{{ ucfirst($type) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Property Type -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Property Type</label>
                                <div class="relative">
                                    <select wire:model.live="selectedPropertyType"
                                        class="w-full pl-4 pr-10 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 appearance-none cursor-pointer hover:bg-white transition-colors">
                                        <option value="">All Types</option>
                                        @foreach ($filterOptions['property_type'] as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Bedrooms -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Bedrooms</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($filterOptions['bedrooms'] as $beds)
                                        <button wire:click="toggleFilter('bedrooms', '{{ $beds }}')"
                                            class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ in_array($beds, $selectedBedrooms ?? []) ? 'bg-emerald-600 text-white shadow-md' : 'bg-gray-50 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 border border-transparent hover:border-emerald-200' }}">
                                            {{ $beds }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Price Range</label>
                                <div class="space-y-2.5">
                                    @foreach ($filterOptions['price_range'] as $range)
                                        <label class="flex items-center group cursor-pointer">
                                            <div class="relative flex items-center">
                                                <input type="checkbox" wire:model.live="selectedPriceRanges"
                                                    value="{{ $range }}"
                                                    class="peer h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 transition duration-150 ease-in-out">
                                            </div>
                                            <span class="ml-3 text-sm text-gray-700 group-hover:text-emerald-700 transition-colors">
                                                @switch($range)
                                                    @case('0-500000')
                                                        Under ₦500K
                                                    @break

                                                    @case('500000-1000000')
                                                        ₦500K - ₦1M
                                                    @break

                                                    @case('1000000-2000000')
                                                        ₦1M - ₦2M
                                                    @break

                                                    @case('2000000+')
                                                        Over ₦2M
                                                    @break
                                                @endswitch
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Clean Property Grid Section -->
    <section class="py-8 lg:py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($properties->count() > 0)
                <!-- Properties Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
                    @foreach ($properties as $property)
                        <a href="{{ route('property.show', $property->slug ?? $property->id) }}"
                            class="group block bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">

                            <!-- Property Image -->
                            <div class="relative h-64 overflow-hidden">
                                @if ($property->getMedia('featured')->count() > 0)
                                    <img src="{{ $property->getFirstMedia('featured')->getUrl() }}"
                                        alt="{{ $property->title }}"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                @elseif($property->getMedia('gallery')->count() > 0)
                                    <img src="{{ $property->getMedia('gallery')->first()->getUrl() }}"
                                        alt="{{ $property->title }}"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                @else
                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Overlay Gradient -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                <!-- Verified Badge -->
                                @if ($property->is_verified)
                                    <div class="absolute top-3 left-3 z-10">
                                        <div class="inline-flex items-center space-x-1 bg-white/95 backdrop-blur-sm px-2.5 py-1 rounded-lg shadow-sm">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-xs font-bold text-gray-800">Verified</span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Listing Type Badge -->
                                <div class="absolute top-3 right-3 z-10">
                                    @php
                                        $badgeColor = match($property->listing_type) {
                                            'rent' => 'bg-emerald-500',
                                            'sale' => 'bg-blue-600',
                                            'lease' => 'bg-purple-600',
                                            'shortlet' => 'bg-amber-500',
                                            default => 'bg-gray-600'
                                        };
                                    @endphp
                                    <div class="{{ $badgeColor }} text-white text-xs font-bold px-2.5 py-1 rounded-lg shadow-sm">
                                        {{ ucfirst($property->listing_type === 'shortlet' ? 'Short Let' : $property->listing_type) }}
                                    </div>
                                </div>

                                <!-- Heart Icon -->
                                <button wire:click.stop="toggleSaveProperty({{ $property->id }})"
                                    onclick="event.stopPropagation(); event.preventDefault();"
                                    class="absolute bottom-3 right-3 p-2.5 bg-white/90 backdrop-blur-sm rounded-full text-gray-400 hover:text-red-500 hover:bg-white transition-all shadow-sm z-10 group/btn">
                                    @if ($this->isPropertySaved($property->id))
                                        <svg class="w-5 h-5 text-red-500 transition-transform group-hover/btn:scale-110" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 transition-transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    @endif
                                </button>
                            </div>

                            <!-- Property Details -->
                            <div class="p-5">
                                <!-- Price -->
                                <div class="mb-2">
                                    <span class="text-xl font-bold text-emerald-600 block">
                                        ₦{{ number_format($property->price) }}<span class="text-sm font-normal text-gray-500">{{ $property->listing_type === 'rent' ? '/yr' : '' }}</span>
                                    </span>
                                </div>

                                <!-- Title -->
                                <h3 class="text-base font-bold text-gray-900 mb-2 line-clamp-1 group-hover:text-emerald-600 transition-colors">
                                    {{ $property->title }}
                                </h3>

                                <!-- Location -->
                                <div class="flex items-center text-gray-500 text-sm mb-4">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="truncate">
                                        {{ $property->city->name ?? 'Unknown' }}, {{ $property->state->name ?? 'Unknown' }}
                                    </span>
                                </div>

                                <!-- Features -->
                                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                                    @if ($property->bedrooms)
                                        <div class="flex items-center text-gray-600 text-xs font-medium bg-gray-50 px-2.5 py-1.5 rounded-lg">
                                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                            {{ $property->bedrooms }} Beds
                                        </div>
                                    @endif
                                    <div class="text-xs text-gray-400 ml-auto">
                                        {{ $property->created_at->diffForHumans(null, true) }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Custom Pagination -->
                <div class="mt-12 lg:mt-16 flex justify-center">
                    @if ($properties->hasPages())
                        <nav class="flex items-center space-x-2" role="navigation" aria-label="Pagination Navigation">
                            {{-- Previous Page Link --}}
                            @if ($properties->onFirstPage())
                                <span class="flex items-center justify-center w-10 h-10 text-gray-300 bg-gray-50 border border-gray-100 rounded-xl cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $properties->previousPageUrl() }}"
                                    class="flex items-center justify-center w-10 h-10 text-gray-700 bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-xl transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </a>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($properties->getUrlRange(1, $properties->lastPage()) as $page => $url)
                                @if ($page == $properties->currentPage())
                                    <span class="flex items-center justify-center w-10 h-10 text-white font-bold bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl shadow-md transform scale-105">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @else
                                <span class="flex items-center justify-center w-10 h-10 text-gray-300 bg-gray-50 border border-gray-100 rounded-xl cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            @endif
                        </nav>
                    @endif
                </div>
            @else
                <!-- No Results -->
                <div class="text-center py-20">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-50 rounded-full mb-6">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No properties found</h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto">We couldn't find any properties matching your search criteria. Try adjusting your filters or search terms.</p>
                    <button wire:click="clearAllFilters"
                        class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-emerald-500/30">
                        Browse All Properties
                    </button>
                </div>
            @endif
        </div>
    </section>

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-4 left-4 right-4 sm:left-auto sm:right-4 z-50 space-y-2"></div>
</div>

@push('styles')
    <style>
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

                toast.className =
                    `flex items-center space-x-3 ${bgColor} text-white px-6 py-4 rounded-xl shadow-lg transform translate-x-full transition-transform duration-300`;
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
                const searchInput = document.querySelector(
                    'input[wire\\:model\\.live\\.debounce\\.500ms="searchQuery"]');
                const suggestionsDropdown = document.querySelector('.absolute.z-50');

                if (searchInput && !searchInput.contains(e.target) &&
                    (!suggestionsDropdown || !suggestionsDropdown.contains(e.target))) {
                    @this.hideSuggestions();
                }
            });
        });
    </script>
@endpush
