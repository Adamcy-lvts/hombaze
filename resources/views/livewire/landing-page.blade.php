@push('head')
    @if (app()->environment('production'))
        @include('components.analytics.google-tag')
    @endif
@endpush

<div class="min-h-screen bg-gray-50">
    <!-- Modern Hero Section -->
    <!-- Modern Hero Section -->
    <section class="relative min-h-[85vh] flex flex-col justify-center items-center overflow-hidden">
        <!-- Background Image & Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=2075&q=80" 
                 alt="Background" 
                 class="w-full h-full object-cover transform scale-105 hover:scale-100 transition-transform duration-[20s] ease-out">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/40 to-gray-50/90"></div>
        </div>

        <!-- Hero Content -->
        <div class="relative z-10 w-full max-w-5xl mx-auto px-4 text-center -mt-12 hero-content">
            <div class="inline-block px-4 py-1.5 mb-6 rounded-full bg-white/10 backdrop-blur-md border border-white/20">
                <span class="text-emerald-300 font-medium text-sm tracking-wide uppercase">The Future of Living</span>
            </div>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-white mb-6 tracking-tight drop-shadow-sm leading-tight">
                Discover your <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">perfect sanctuary</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-200 mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                Seamlessly connect with premium properties and top-rated agents in your preferred locations.
            </p>
            
            <!-- Glassmorphism Search Bar -->
            <div class="relative max-w-3xl mx-auto mt-8">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full blur opacity-25 group-hover:opacity-60 transition duration-500"></div>
                    <div class="relative flex items-center bg-white/5 backdrop-blur-xl border border-white/10 rounded-full p-2 shadow-xl transition-all duration-300 hover:bg-white/10 ring-1 ring-white/20">
                        <div class="pl-5 text-emerald-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input
                            type="text"
                            wire:model.live.debounce.500ms="searchQuery"
                            wire:focus="updateSuggestions"
                            placeholder="Search city, state, or property type..."
                            class="w-full bg-transparent border-none focus:ring-0 text-white placeholder-gray-300/80 text-lg px-4 py-3 font-medium"
                            autocomplete="off"
                        >
                        <button
                            onclick="window.location.href='/properties?q=' + encodeURIComponent(document.querySelector('input[wire\\:model\\.live\\.debounce\\.500ms=\"searchQuery\"]').value)"
                            class="bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white px-8 py-3 rounded-full font-bold text-base transition-all duration-200 shadow-lg hover:shadow-emerald-500/20 transform hover:-translate-y-0.5"
                        >
                            Search
                        </button>
                    </div>
                </div>

                <!-- Search Suggestions -->
                @if($showSuggestions && count($suggestions) > 0)
                    <div class="absolute w-full mt-2 bg-white/95 backdrop-blur-xl border border-white/20 rounded-xl shadow-xl max-h-60 overflow-y-auto z-50 text-left">
                        @foreach($suggestions as $suggestion)
                            <div
                                wire:click="selectSuggestion({{ json_encode($suggestion) }})"
                                class="flex items-center px-4 py-3 hover:bg-emerald-50/50 cursor-pointer transition-colors duration-200 border-b border-gray-100 last:border-b-0 group"
                            >
                                <div class="shrink-0 w-8 h-8 bg-emerald-100/50 text-emerald-600 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200">
                                    @if($suggestion['icon'] === 'location-dot')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-sm text-gray-900 group-hover:text-emerald-700 transition-colors">{{ $suggestion['text'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $suggestion['category'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Floating Filters Section (Collapsible) -->
    <section class="relative z-20 -mt-12 px-4 sm:px-6 lg:px-8 pb-8" x-data="{ showFilters: false }">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 backdrop-blur-sm bg-white/95">
                
                <!-- Top Row: Listing Types & Toggle -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <!-- Listing Types (Always Visible) -->
                    <div class="flex items-center bg-gray-100/80 p-1 rounded-lg">
                        @foreach($filterOptions['listing_types'] as $listingType)
                            <button
                                wire:click="updateFilter('listing_type', '{{ $listingType['value'] }}')"
                                class="px-4 py-2 rounded-md text-sm font-semibold transition-all duration-200 {{ $selectedListingType === $listingType['value'] ? 'bg-white text-emerald-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}"
                            >
                                {{ $listingType['label'] }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Filter Toggle Button -->
                    <button 
                        @click="showFilters = !showFilters"
                        class="flex items-center space-x-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-emerald-200 transition-all duration-200"
                        :class="{ 'border-emerald-500 ring-1 ring-emerald-500 bg-emerald-50': showFilters }"
                    >
                        <svg class="w-5 h-5 text-gray-500" :class="{ 'text-emerald-600': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        <span class="font-medium text-sm">Filters</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>

                <!-- Advanced Filters Grid (Collapsible) -->
                <div 
                    x-show="showFilters" 
                    x-collapse
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
                    style="display: none;"
                >
                    <!-- Bedrooms -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Bedrooms
                        </label>
                        <select wire:model.live="selectedBedrooms" class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-sm py-2.5 px-3 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Any</option>
                            @foreach($filterOptions['bedrooms'] as $bedroom)
                                <option value="{{ $bedroom['value'] }}">{{ $bedroom['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Property Type -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Type
                        </label>
                        <select wire:model.live="selectedPropertyType" class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-sm py-2.5 px-3 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All Types</option>
                            @foreach($filterOptions['property_types'] as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            State
                        </label>
                        <select wire:model.live="selectedState" class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-sm py-2.5 px-3 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All States</option>
                            @foreach($filterOptions['states'] as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center justify-between">
                            <span>Max Price</span>
                            <span class="text-emerald-600">{{ $selectedPriceRange > 0 ? '₦' . number_format($selectedPriceRange / 1000000) . 'M' : 'Any' }}</span>
                        </label>
                        <input
                            type="range"
                            wire:model.live="selectedPriceRange"
                            min="0"
                            max="100000000"
                            step="5000000"
                            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider accent-emerald-500 mt-2"
                        >
                    </div>
                </div>

                <!-- Active Filters & Clear -->
                @if($selectedPropertyType || $selectedBedrooms || $selectedState || $selectedCity || $selectedArea || $selectedPriceRange || $searchQuery)
                    <div class="mt-4 pt-3 border-t border-gray-100 flex justify-center">
                        <button
                            wire:click="clearAllFilters"
                            class="group inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-500 hover:text-red-500 bg-gray-50 hover:bg-red-50 rounded-lg transition-all duration-200"
                        >
                            <svg class="w-3.5 h-3.5 mr-1.5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear All Filters
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Simple Featured Grid Section -->
    @if($this->featuredProperties->count() > 0)
    <section class="py-24 bg-slate-900 overflow-hidden relative" id="featured-grid-section">
        <!-- Abstract Background Elements -->
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-emerald-500/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-[500px] h-[500px] bg-blue-500/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-8 h-[1px] bg-emerald-500"></span>
                        <span class="text-emerald-400 text-[10px] font-black uppercase tracking-[0.3em]">Premium Selection</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight">Featured <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">Residences</span></h2>
                    <p class="text-slate-400 text-base mt-3 max-w-xl">A curated collection of our most prestigious properties, selected for their exceptional quality and prime locations.</p>
                </div>
                <a href="/properties?is_featured=1" class="group flex items-center gap-3 px-8 py-4 bg-white/5 hover:bg-white/10 backdrop-blur-md border border-white/10 text-white rounded-2xl font-bold text-sm transition-all duration-300">
                    <span>Explore All</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                @foreach($this->featuredProperties->take(6) as $property)
                    <a href="{{ route('property.show', $property->slug ?? $property->id) }}"
                       class="group relative h-[500px] rounded-[2.5rem] overflow-hidden shadow-2xl transition-all duration-500 hover:-translate-y-2">
                        
                        <!-- Premium Background Image -->
                        <div class="absolute inset-0">
                            @if($property->getMedia('featured')->count() > 0)
                                <img src="{{ $property->getFirstMedia('featured')->getUrl() }}"
                                     alt="{{ $property->title }}"
                                     class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                            @else
                                <div class="w-full h-full bg-slate-800 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <!-- Multi-layer Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
                            <div class="absolute inset-0 bg-slate-950/20 group-hover:bg-transparent transition-colors duration-500"></div>
                        </div>

                        <!-- Card Content -->
                        <div class="absolute inset-0 p-8 flex flex-col justify-between z-20">
                            <div class="flex justify-between items-start">
                                <span class="bg-white/10 backdrop-blur-md border border-white/20 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest">
                                    {{ $property->listing_type === 'sale' ? 'For Sale' : 'For Rent' }}
                                </span>
                                <div class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white/80 hover:bg-emerald-500 hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </div>
                            </div>

                            <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                <div class="flex items-baseline gap-2 mb-2">
                                    <span class="text-3xl font-black text-white tabular-nums">
                                        ₦{{ number_format($property->price) }}
                                    </span>
                                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest">
                                        {{ $property->propertyType->name ?? 'Luxury' }}
                                    </span>
                                </div>
                                <h3 class="text-2xl font-extrabold text-white mb-2 leading-tight">
                                    {{ $property->title }}
                                </h3>
                                <div class="flex items-center text-slate-300 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                                    <svg class="w-4 h-4 mr-1.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    {{ $property->city->name ?? '' }}, {{ $property->state->name ?? '' }}
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Editorial Bento Featured Section (Hidden for now) -->
    @if(false && $this->featuredProperties->count() > 0)
    <section class="py-24 bg-white overflow-hidden" id="featured-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-10 mb-16">
                <div class="max-w-2xl">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-12 h-[2px] bg-emerald-500"></span>
                        <span class="text-emerald-600 font-bold text-xs uppercase tracking-[0.3em]">Signature Collection</span>
                    </div>
                    <h2 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tight leading-[0.95] mb-8">
                        The <span class="text-transparent bg-clip-text bg-gradient-to-br from-emerald-600 to-blue-700">Editorial</span><br class="hidden md:block" /> List
                    </h2>
                    <p class="text-slate-600 text-lg md:text-xl font-light leading-relaxed">
                        A curated selection of properties that define modern luxury. Each residence is handpicked for its architectural excellence and superior lifestyle offering.
                    </p>
                </div>
                <div>
                    <a href="/properties?is_featured=1" class="group flex items-center gap-4 px-10 py-5 bg-slate-900 text-white rounded-[2rem] font-bold text-sm transition-all duration-500 hover:bg-emerald-600 hover:shadow-2xl hover:shadow-emerald-200">
                        <span>View All Featured</span>
                        <svg class="w-5 h-5 transform group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>

            <!-- Director's Cut: Synched Viewfinder Layout -->
            <div id="directors-cut-container" 
                 x-data="{ 
                    activeId: {{ $this->featuredProperties[0]->id ?? 0 }},
                    init() {
                        window.addEventListener('active-property-changed', (e) => {
                            this.activeId = e.detail.id;
                        });
                    }
                 }"
                 class="flex flex-col md:flex-row gap-8 lg:gap-16 relative items-stretch bg-white">
                
                <!-- Left: Minimalist Property List (The Scrollable Core) -->
                <div class="w-full md:w-[45%] py-4 lg:py-8" id="property-list-viewport">
                    <div class="flex flex-col space-y-4" id="property-list-content">
                        @foreach($this->featuredProperties as $index => $property)
                            <div 
                                id="property-item-{{ $property->id }}"
                                data-id="{{ $property->id }}"
                                @mouseenter="$dispatch('active-property-changed', { id: {{ $property->id }} })"
                                @click="window.location.href = '{{ route('property.show', $property->slug ?? $property->id) }}'"
                                :class="activeId == {{ $property->id }} ? 'border-emerald-600 opacity-100 translate-x-4' : 'border-slate-100 opacity-40 hover:opacity-100 hover:translate-x-2'"
                                class="featured-list-item relative py-6 md:py-10 border-b transition-all duration-500 cursor-pointer origin-left"
                            >
                            <a href="{{ route('property.show', $property->slug ?? $property->id) }}" class="absolute inset-0 z-10"></a>
                            
                            <div class="flex items-start justify-between gap-6 md:gap-12 pointer-events-none pr-4 md:pr-8">
                                <div class="flex gap-4 md:gap-6 flex-1">
                                    <span 
                                        :class="activeId === {{ $property->id }} ? 'text-emerald-500' : 'text-slate-300'"
                                        class="text-[10px] md:text-sm font-black mt-1.5 md:mt-2 tracking-widest transition-colors uppercase flex-shrink-0 tabular-nums"
                                    >
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <div class="flex-1">
                                        <!-- Mobile Thumbnail (visible only on mobile) -->
                                        <div class="md:hidden mb-4 rounded-2xl overflow-hidden aspect-video w-full shadow-lg h-0 group-hover/item:h-48 transition-all duration-500"
                                             :class="activeId === {{ $property->id }} ? 'h-48 mb-6' : 'h-0 mb-0'">
                                            @if($property->getMedia('featured')->count() > 0)
                                                <img src="{{ $property->getFirstMedia('featured')->getUrl() }}" class="w-full h-full object-cover" alt="">
                                            @endif
                                        </div>

                                        <h3 :class="activeId === {{ $property->id }} ? 'text-emerald-700' : 'text-slate-900'"
                                            class="text-lg md:text-2xl lg:text-3xl font-black leading-tight transition-all duration-500">
                                            {{ $property->title }}
                                        </h3>
                                        <p class="text-slate-400 text-xs md:text-sm mt-1 md:mt-2 font-medium">
                                            {{ $property->city->name ?? '' }}, {{ $property->state->name ?? '' }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="text-right flex flex-col items-end shrink-0">
                                    <span class="text-base md:text-xl lg:text-2xl font-black text-slate-900 tabular-nums">
                                        ₦{{ number_format($property->price) }}
                                    </span>
                                    <span class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                                        {{ $property->propertyType->name ?? 'Luxury' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="hidden md:block w-full md:w-[55%] sticky top-24 self-start" id="visual-deck-viewport">
                    <div class="w-full h-[600px] lg:h-[750px] relative overflow-hidden bg-slate-100 rounded-[2.5rem] lg:rounded-[3.5rem] shadow-2xl">
                        @foreach($this->featuredProperties as $index => $property)
                            <div 
                                class="absolute inset-0 z-10 transition-opacity duration-700 ease-in-out"
                                :class="activeId == {{ $property->id }} ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
                            >
                                @if($property->getMedia('featured')->count() > 0)
                                    <img src="{{ $property->getFirstMedia('featured')->getUrl() }}" class="w-full h-full object-cover" alt="{{ $property->title }}">
                                @endif
                                
                                <!-- Premium Overlays -->
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                                
                                <!-- Floating Info -->
                                <div class="absolute bottom-6 lg:bottom-12 left-6 lg:left-12 right-6 lg:right-12 flex items-end justify-between text-white z-20">
                                    <div class="max-w-xs">
                                        <div class="flex items-center gap-3 mb-4">
                                            <span class="px-4 py-1.5 bg-emerald-500 rounded-full text-[9px] font-black uppercase tracking-[0.2em] shadow-lg shadow-emerald-500/20">
                                                Elite Piece
                                            </span>
                                        </div>
                                        <p class="text-xs md:text-sm font-light text-slate-200 leading-relaxed drop-shadow-md">
                                            Architecture masterpiece in {{ $property->city->name ?? 'Nigeria' }}. Optimized for luxury living.
                                        </p>
                                    </div>
                                    
                                    <button wire:click.stop.prevent="toggleSaveProperty({{ $property->id }})" class="group/save w-14 h-14 lg:w-16 lg:h-16 rounded-2xl lg:rounded-3xl bg-white/10 backdrop-blur-3xl border border-white/20 flex items-center justify-center hover:bg-emerald-500 hover:border-emerald-500 transition-all duration-500 shadow-2xl">
                                        @if($this->isPropertySaved($property->id))
                                            <svg class="w-6 h-6 lg:w-7 lg:h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        @else
                                            <svg class="w-6 h-6 lg:w-7 lg:h-7 transform group-hover/save:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                        @endif
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Property Grid Section -->
    <section class="py-6 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($properties->count() > 0)
                <!-- Properties Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($properties as $property)
                        <a href="{{ route('property.show', $property->slug ?? $property->id) }}"
                           class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border border-gray-100 flex flex-col h-full">

                            <!-- Property Image -->
                            <div class="relative h-52 shrink-0 overflow-hidden">
                                @if($property->getMedia('featured')->count() > 0)
                                    <img src="{{ $property->getFirstMedia('featured')->getUrl() }}"
                                         alt="{{ $property->title }}"
                                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                @elseif($property->getMedia('gallery')->count() > 0)
                                    <img src="{{ $property->getMedia('gallery')->first()->getUrl() }}"
                                         alt="{{ $property->title }}"
                                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                @else
                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Overlay Gradient (Subtle) -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                <!-- Top Badges -->
                                <div class="absolute top-3 left-3 flex gap-2 z-10">
                                    @if($property->is_verified)
                                        <div class="flex items-center space-x-1 bg-white/95 backdrop-blur-sm text-blue-600 px-2.5 py-1 rounded-md text-xs font-bold shadow-sm">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span>Verified</span>
                                        </div>
                                    @endif
                                    <div class="bg-emerald-500/95 backdrop-blur-sm text-white px-2.5 py-1 rounded-md text-xs font-bold shadow-sm">
                                        {{ $property->listing_type === 'sale' ? 'For Sale' : ($property->listing_type === 'rent' ? 'For Rent' : 'Short Let') }}
                                    </div>
                                </div>

                                <!-- Save Button (Top Right) -->
                                <button
                                    wire:click.stop="toggleSaveProperty({{ $property->id }})"
                                    onclick="event.stopPropagation(); event.preventDefault();"
                                    class="absolute top-3 right-3 p-2 bg-white/90 backdrop-blur-sm rounded-full text-gray-400 hover:text-red-500 hover:bg-white transition-all shadow-sm z-10 group/btn"
                                >
                                    @if($this->isPropertySaved($property->id))
                                        <svg class="w-5 h-5 text-red-500 transition-transform group-hover/btn:scale-110" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-400 group-hover/btn:text-red-500 transition-colors transition-transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    @endif
                                </button>
                            </div>

                            <!-- Property Details -->
                            <div class="p-4 flex flex-col flex-grow">
                                <!-- Price -->
                                <div class="mb-1.5">
                                    <span class="text-lg font-bold text-emerald-600 block">
                                        ₦{{ number_format($property->price) }}<span class="text-xs font-normal text-gray-500">{{ $property->listing_type === 'rent' ? '/yr' : '' }}</span>
                                    </span>
                                </div>

                                <!-- Title -->
                                <h3 class="text-sm font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-emerald-600 transition-colors">
                                    {{ $property->title }}
                                </h3>
                                
                                <!-- Location -->
                                <div class="flex items-center text-gray-500 text-[10px] mb-3">
                                    <svg class="w-3 h-3 mr-1 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="truncate">
                                        {{ $property->city->name ?? '' }}, {{ $property->state->name ?? '' }}
                                    </span>
                                </div>

                                <!-- Footer (Features & Date) -->
                                <div class="mt-auto pt-2.5 border-t border-gray-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3 text-gray-600 text-[10px] font-medium">
                                        <div class="flex items-center bg-gray-50 px-1.5 py-0.5 rounded-md">
                                            <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                            {{ $property->bedrooms }} Bedrooms
                                        </div>
                                    </div>
                                    <div class="text-[10px] text-gray-400">
                                        {{ $property->created_at->diffForHumans(null, true) }}
                                    </div>
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
    <div id="toast-container" class="fixed top-4 left-4 right-4 sm:left-auto sm:right-4 z-50 space-y-2"></div>
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

/* Scrollbar Hide Utility */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Bento Item Transitions */
.bento-item {
    will-change: transform, opacity;
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script>
document.addEventListener('livewire:initialized', () => {
    // Register GSAP plugins
    gsap.registerPlugin(ScrollTrigger);

    // Initial Animation & Carousel Logic
    const initLuxuryAnimation = () => {
        // Simple entrance for hero - less aggressive
        gsap.to('.hero-content', {
            y: 0,
            opacity: 1,
            duration: 1,
            ease: "power2.out"
        });
    };

    // Run animations - wait a tick to ensure DOM is ready
    setTimeout(initLuxuryAnimation, 100);

    // Handle Resize (Reload to switch modes if crossing breakpoint)
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            // Only reload if crossing the 768px threshold
            // For now, simpler to just re-init or reload if needed. 
            // In a real app we might destroy/create timelines. 
            // For this quick fix, we'll leave it as is, or use a reload to be safe:
            // window.location.reload(); 
        }, 250);
    });

    // Re-run on Livewire updates
    Livewire.hook('morph.updated', ({ el, component }) => { 
       // ...
    });

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
