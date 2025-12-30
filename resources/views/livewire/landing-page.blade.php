@push('head')
    @if (app()->environment('production'))
        @include('components.analytics.google-tag')
    @endif
@endpush

<div class="min-h-screen bg-gray-50">
    <!-- Modern Hero Section -->
    <!-- Modern Hero Section -->
    <section class="relative z-20 min-h-[85vh] flex flex-col justify-center items-center">
        <!-- Background Image & Overlay -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=2075&q=80" 
                 alt="Background" 
                 class="w-full h-full object-cover transform scale-105 hover:scale-100 transition-transform duration-[20s] ease-out">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/40 to-gray-50/90"></div>
        </div>

        <!-- Hero Content -->
        <div class="relative z-30 w-full max-w-5xl mx-auto px-4 text-center mt-12 sm:-mt-12 hero-content">
            <div class="inline-block px-4 py-1.5 mb-6 rounded-full bg-white/10 backdrop-blur-md border border-white/20">
                <span class="text-emerald-300 font-medium text-sm tracking-wide uppercase">The Future of Living</span>
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold text-white mb-6 tracking-tight drop-shadow-sm leading-tight sm:leading-tight">
                Discover your <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">perfect sanctuary</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-200 mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                Seamlessly connect with premium properties and top-rated agents in your preferred locations.
            </p>
            
            <!-- Glassmorphism Search Bar -->
            <div class="max-w-3xl mx-auto mt-8">
                <livewire:components.search-bar
                    :placeholder="'Search city, area, or property type...'"
                    :auto-focus="false"
                />
            </div>
        </div>
    </section>



    <!-- Previous Featured Sections Commented Out -->
    {{-- 
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
        <!-- ... existing director's cut logic ... -->
    </section>
    @endif
    --}}

    <!-- The Featured Collection Interactive List (Minimalist Luxury Gallery) -->
    @if($this->featuredProperties->count() > 0)
    <section class="py-0 lg:py-24 bg-slate-950 lg:bg-gray-50 overflow-hidden" id="featured-gallery-wrapper">
        <div class="max-w-none lg:max-w-[1550px] mx-auto px-0 lg:px-8">
            <div class="relative bg-white h-screen lg:h-[800px] overflow-hidden group/dc flex flex-col lg:flex-row rounded-none lg:rounded-[3rem] shadow-none lg:shadow-[0_40px_80px_-20px_rgba(0,0,0,0.08)] border-0 lg:border lg:border-gray-200/50 transition-all duration-700" id="directors-cut-section">
        
        <!-- Background Visual Deck (Top on mobile, Right on desktop) -->
        <div class="relative w-full h-[45vh] lg:absolute lg:inset-0 lg:left-[40%] lg:w-[60%] lg:h-full z-10 lg:z-0 overflow-hidden shrink-0">
            @foreach($this->featuredProperties as $index => $property)
                <div 
                    class="visual-item absolute inset-0 opacity-0 transition-opacity duration-1000 ease-in-out"
                    id="property-visual-{{ $index }}"
                >
                    <!-- Background Image -->
                    <div class="absolute inset-0 overflow-hidden">
                        @if($property->getMedia('featured')->count() > 0)
                            <img src="{{ $property->getFirstMedia('featured')->getUrl() }}" 
                                 class="w-full h-full object-cover lg:object-cover visual-img" 
                                 alt="{{ $property->title }}">
                        @else
                            <div class="w-full h-full bg-slate-900 flex items-center justify-center">
                                <svg class="w-16 h-16 lg:w-20 lg:h-20 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                     <!-- Cinematic Gradients -->
                     <div class="absolute inset-0 bg-slate-950/40 hidden lg:block"></div>
                     <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/60 to-transparent hidden lg:block"></div>
                     <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent hidden lg:block"></div>
                 </div>

                    <!-- Visual Info Anchor (Desktop only) -->
                    <div class="hidden lg:flex absolute inset-0 flex-col justify-end p-20 z-20">
                        <div class="visual-meta opacity-0 translate-y-8 select-none">
                            <div class="inline-flex items-center gap-2 mb-4">
                                <span class="w-12 h-[1px] bg-emerald-500/50"></span>
                                <span class="text-emerald-400 font-black text-3xl tracking-tighter tabular-nums drop-shadow-sm">₦{{ number_format($property->price) }}</span>
                            </div>
                            <h4 class="text-white text-5xl font-black mb-10 leading-tight max-w-xl drop-shadow-2xl">
                                {{ $property->title }}
                            </h4>
                            <div class="flex items-center gap-6">
                                <a href="{{ route('property.show', $property->slug ?? $property->id) }}" 
                                   class="relative group/btn-v overflow-hidden px-10 py-5 bg-white text-slate-900 font-black text-xs uppercase tracking-[0.2em] shadow-2xl transition-all duration-500">
                                    <span class="relative z-10">Explore Detail</span>
                                    <div class="absolute inset-0 bg-emerald-500 translate-y-full group-hover/btn-v:translate-y-0 transition-transform duration-500 ease-out"></div>
                                </a>
                                <div class="w-12 h-12 rounded-full border border-white/20 flex items-center justify-center text-white/50 hover:text-white transition-colors cursor-pointer group/arrow">
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <!-- Mobile Price/Badge Overlay (Minimalist) -->
            <div class="lg:hidden absolute bottom-4 right-4 z-30">
                <div id="mobile-price-badge" class="bg-emerald-500 text-white px-4 py-2 font-black text-xs rounded-lg shadow-2xl transition-all duration-500 scale-0">
                    ₦0
                </div>
            </div>
        </div>

        <!-- Foreground Content Pane (Bottom on mobile, Left 40% on desktop) -->
        <div class="relative z-20 w-full flex-1 lg:flex-none lg:w-[40%] lg:h-full flex flex-col bg-slate-950 lg:bg-white overflow-hidden">
            <!-- Fixed Header (Responsive) -->
            <div class="shrink-0 p-6 lg:p-16 border-b border-white/5 lg:border-slate-50">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-8 h-[2px] bg-emerald-600 font-bold"></span>
                    <span class="text-emerald-500 lg:text-emerald-600 font-extrabold text-[8px] uppercase tracking-[0.5em]">Luxury Portfolio</span>
                </div>
                <h2 class="text-2xl lg:text-5xl font-black text-white lg:text-slate-900 tracking-tight leading-tight">
                    Featured <span class="text-emerald-400 lg:text-transparent lg:bg-clip-text lg:bg-gradient-to-r lg:from-emerald-600 lg:to-teal-500">Collection</span>
                </h2>
            </div>

            <!-- Scrollable List Container -->
            <div 
                id="property-list-container"
                class="flex-1 overflow-y-auto scrollbar-hide snap-y snap-mandatory bg-transparent lg:bg-white"
            >
                <!-- Mobile Entrance Padding -->
                <div class="h-10 lg:h-0"></div>

                <div class="px-6 lg:px-16 space-y-0">
                    @foreach($this->featuredProperties as $index => $property)
                        <div 
                            class="property-trigger group cursor-pointer py-4 lg:py-3 border-b border-white/5 lg:border-slate-100 last:border-0 transition-all duration-500 snap-center min-h-[80px] lg:min-h-0 flex items-center"
                            data-index="{{ $index }}"
                            data-price="₦{{ number_format($property->price) }}"
                            onmouseenter="switchVisual({{ $index }})"
                        >
                            <div class="flex items-center gap-4 lg:gap-8 w-full">
                                <span class="text-white/20 lg:text-white/10 font-mono lg:font-black text-base lg:text-3xl tracking-normal lg:tracking-tighter group-hover:text-emerald-500 transition-colors duration-500 shrink-0 w-8 lg:w-16">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <!-- Mobile Listing Type Badge -->
                                    <div class="mb-1.5 lg:hidden">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-[4px] text-[9px] font-black uppercase tracking-widest leading-none {{ $property->listing_type === 'sale' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20' : 'bg-blue-500/20 text-blue-400 border border-blue-500/20' }}">
                                            {{ $property->listing_type === 'sale' ? 'Sale' : ($property->listing_type === 'rent' ? 'Rent' : 'Short Let') }}
                                        </span>
                                    </div>

                                    <h3 class="text-sm sm:text-lg lg:text-2xl font-bold lg:font-black text-white/60 lg:text-slate-300 group-hover:text-white lg:group-hover:text-slate-900 transition-all duration-700 leading-tight line-clamp-2 lg:line-clamp-1 mb-1 lg:mb-1.5 lg:uppercase lg:tracking-tighter">
                                        {{ $property->title }}
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-x-3 lg:gap-x-4 gap-y-1">
                                        <span class="text-emerald-500 font-extrabold lg:font-black text-xs sm:text-sm lg:text-base tabular-nums">₦{{ number_format($property->price) }}</span>
                                        <div class="flex items-center gap-2">
                                            <span class="w-1 h-1 rounded-full bg-slate-700 hidden lg:block"></span>
                                            <p class="text-white/40 lg:text-slate-400 text-[8px] lg:text-[10px] uppercase tracking-[0.2em] lg:tracking-[0.3em] font-bold lg:font-black truncate">
                                                {{ $property->city->name ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Desktop Link Arrow -->
                                <div class="opacity-0 group-hover:opacity-100 transition-all duration-500 transform lg:-translate-x-4 group-hover:translate-x-0 hidden lg:block">
                                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </div>
                                <!-- Mobile Action Link (Obvious Button) -->
                                <a href="{{ route('property.show', $property->slug ?? $property->id) }}" class="lg:hidden flex items-center justify-center px-3 py-1.5 bg-white/10 border border-white/10 rounded-lg text-white text-[10px] font-bold uppercase tracking-wider hover:bg-emerald-600 hover:border-emerald-600 transition-all duration-300 shrink-0">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="h-20 lg:h-32"></div>
            </div>

            <!-- Fixed Footer (Responsive) -->
            <div class="shrink-0 p-6 lg:p-16 border-t border-white/5 lg:border-slate-50">
                <a href="/properties?is_featured=1" class="group flex items-center justify-between lg:justify-start gap-4 text-white/50 lg:text-slate-900 font-bold text-[9px] uppercase tracking-[0.4em] hover:text-emerald-400 transition-colors">
                    <span>Explore Collection</span>
                    <span class="w-10 h-10 rounded-full bg-white/5 lg:bg-slate-100 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </span>
                </a>
            </div>
        </div>
    </div>
    </section>
    @endif

    <!-- Premium Search & Filter Hub -->
    <section class="relative z-40 -mt-8 px-4 sm:px-6 lg:px-8 pb-12" x-data="{ showFilters: false }">
        <div class="max-w-6xl mx-auto">
            <!-- Main Filter Bar -->
            <div class="relative bg-white/90 backdrop-blur-xl rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-white/20 p-3 sm:p-4 z-40">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    
                    <!-- Listing Type Selector (Pill Style) -->
                    <div class="relative flex bg-gray-100/50 p-1.5 rounded-full w-full md:w-auto">
                        @foreach($filterOptions['listing_types'] as $listingType)
                            <button
                                wire:click="setListingType('{{ $listingType['value'] }}')"
                                class="relative z-10 flex-1 md:flex-none px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-widest transition-all duration-300 {{ $selectedListingType === $listingType['value'] ? 'text-emerald-700' : 'text-gray-500 hover:text-gray-900' }}"
                            >
                                @if($selectedListingType === $listingType['value'])
                                    <div class="absolute inset-0 bg-white rounded-full shadow-sm -z-10"></div>
                                @endif
                                {{ $listingType['label'] }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Filter Actions -->
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <!-- Advanced Filter Toggle -->
                        <button 
                            @click="showFilters = !showFilters; if(showFilters) $dispatch('filter-revealed')"
                            class="flex-1 md:flex-none flex items-center justify-center space-x-3 px-8 py-3.5 bg-slate-900 text-white rounded-full hover:bg-slate-800 transition-all duration-300 group shadow-lg shadow-slate-200"
                        >
                            <div class="relative w-5 h-5">
                                <svg class="absolute inset-0 w-5 h-5 transition-all duration-300 transform" :class="{ 'opacity-0 rotate-90': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                                <svg class="absolute inset-0 w-5 h-5 transition-all duration-300 transform opacity-0 scale-50" :class="{ 'opacity-100 scale-100 rotate-0': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <span class="font-bold text-xs uppercase tracking-[0.2em]" x-text="showFilters ? 'Hide Options' : 'Refine Search'">Refine Search</span>
                        </button>
                    </div>
                </div>

                <!-- Advanced Filters Panel -->
                <div 
                    x-show="showFilters" 
                    x-collapse
                    class="relative z-50"
                >
                    <div class="mt-6 pt-8 border-t border-gray-100/50">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 px-4 pb-4">
                            <!-- Bedrooms Selection -->
                            <div class="space-y-4">
                                <label class="flex items-center text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
                                    <span class="w-4 h-[1px] bg-emerald-500 mr-2"></span>
                                    Bedrooms
                                </label>
                                <div class="relative group" x-data="{ 
                                    open: false, 
                                    selected: @entangle('selectedBedrooms'),
                                    get label() {
                                        if(!this.selected) return 'Any Capacity';
                                        return this.selected === '5' ? '5+ Bedrooms' : (this.selected + (this.selected == 1 ? ' Bedroom' : ' Bedrooms'));
                                    }
                                }">
                                    <button 
                                        @click="open = !open" 
                                        type="button"
                                        class="w-full bg-gray-50/50 border-0 text-slate-900 text-sm font-bold py-4 px-5 rounded-2xl focus:ring-2 focus:ring-emerald-500 flex items-center justify-between transition-all hover:bg-gray-100/50"
                                        :class="{ 'ring-2 ring-emerald-500 bg-white': open }"
                                    >
                                        <span x-text="label">Any Capacity</span>
                                        <div class="text-slate-400 group-hover:text-emerald-500 transition-transform" :class="{ 'rotate-180': open }">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </button>
                                    
                                    <div 
                                        x-show="open" 
                                        @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                        class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden"
                                        style="display: none;"
                                    >
                                        <div class="py-2 max-h-64 overflow-y-auto premium-scrollbar">
                                            <button @click="selected = ''; open = false" class="w-full text-left px-5 py-3 text-sm font-bold transition-all flex items-center justify-between" :class="selected === '' ? 'bg-emerald-50 text-emerald-600' : 'text-slate-600 hover:bg-gray-50 hover:text-emerald-600'">
                                                <span>Any Capacity</span>
                                                <template x-if="selected === ''">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </template>
                                            </button>
                                            @foreach($filterOptions['bedrooms'] as $bedroom)
                                                <button 
                                                    @click="selected = '{{ $bedroom['value'] }}'; open = false" 
                                                    class="w-full text-left px-5 py-3 text-sm font-bold transition-all flex items-center justify-between"
                                                    :class="selected == '{{ $bedroom['value'] }}' ? 'bg-emerald-50 text-emerald-600' : 'text-slate-600 hover:bg-gray-50 hover:text-emerald-600'"
                                                >
                                                    <span>{{ $bedroom['label'] }} {{ $bedroom['value'] == 1 ? 'Bedroom' : 'Bedrooms' }}</span>
                                                    <template x-if="selected == '{{ $bedroom['value'] }}'">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    </template>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Property Type -->
                            <div class="space-y-4">
                                <label class="flex items-center text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
                                    <span class="w-4 h-[1px] bg-emerald-500 mr-2"></span>
                                    Property Style
                                </label>
                                <div class="relative group" x-data="{ 
                                    open: false, 
                                    selected: @entangle('selectedPropertyType'),
                                    options: @js($filterOptions['property_types']),
                                    get label() {
                                        if(!this.selected) return 'All Categories';
                                        const opt = this.options.find(o => o.value == this.selected);
                                        return opt ? opt.label : 'All Categories';
                                    }
                                }">
                                    <button 
                                        @click="open = !open" 
                                        type="button"
                                        class="w-full bg-gray-50/50 border-0 text-slate-900 text-sm font-bold py-4 px-5 rounded-2xl focus:ring-2 focus:ring-emerald-500 flex items-center justify-between transition-all hover:bg-gray-100/50"
                                        :class="{ 'ring-2 ring-emerald-500 bg-white': open }"
                                    >
                                        <span x-text="label">All Categories</span>
                                        <div class="text-slate-400 group-hover:text-emerald-500 transition-transform" :class="{ 'rotate-180': open }">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </button>
                                    
                                    <div 
                                        x-show="open" 
                                        @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                        class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden"
                                        style="display: none;"
                                    >
                                        <div class="py-2 max-h-64 overflow-y-auto premium-scrollbar">
                                            <button @click="selected = ''; open = false" class="w-full text-left px-5 py-3 text-sm font-bold transition-all flex items-center justify-between" :class="selected === '' ? 'bg-emerald-50 text-emerald-600' : 'text-slate-600 hover:bg-gray-50 hover:text-emerald-600'">
                                                <span>All Categories</span>
                                                <template x-if="selected === ''">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </template>
                                            </button>
                                            @foreach($filterOptions['property_types'] as $type)
                                                <button 
                                                    @click="selected = '{{ $type['value'] }}'; open = false" 
                                                    class="w-full text-left px-5 py-3 text-sm font-bold transition-all flex items-center justify-between"
                                                    :class="selected == '{{ $type['value'] }}' ? 'bg-emerald-50 text-emerald-600' : 'text-slate-600 hover:bg-gray-50 hover:text-emerald-600'"
                                                >
                                                    <span>{{ $type['label'] }}</span>
                                                    <template x-if="selected == '{{ $type['value'] }}'">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    </template>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- State/Location -->
                            <div class="space-y-4">
                                <label class="flex items-center text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
                                    <span class="w-4 h-[1px] bg-emerald-500 mr-2"></span>
                                    Location
                                </label>
                                <div class="relative group" x-data="{ 
                                    open: false, 
                                    selected: @entangle('selectedState'),
                                    options: @js($locationOptions['states']->map(fn($s) => ['id' => $s->id, 'name' => $s->name])),
                                    get label() {
                                        if(!this.selected) return 'Search Region';
                                        const opt = this.options.find(o => o.id == this.selected);
                                        return opt ? opt.name : 'Search Region';
                                    }
                                }">
                                    <button 
                                        @click="open = !open" 
                                        type="button"
                                        class="w-full bg-gray-50/50 border-0 text-slate-900 text-sm font-bold py-4 px-5 rounded-2xl focus:ring-2 focus:ring-emerald-500 flex items-center justify-between transition-all hover:bg-gray-100/50"
                                        :class="{ 'ring-2 ring-emerald-500 bg-white': open }"
                                    >
                                        <span x-text="label">Search Region</span>
                                        <div class="text-slate-400 group-hover:text-emerald-500 transition-transform" :class="{ 'rotate-180': open }">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </button>
                                    
                                    <div 
                                        x-show="open" 
                                        @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                        class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden"
                                        style="display: none;"
                                    >
                                        <div class="py-2 max-h-64 overflow-y-auto premium-scrollbar">
                                            <button @click="selected = ''; open = false" class="w-full text-left px-5 py-3 text-sm font-bold transition-all flex items-center justify-between" :class="selected === '' ? 'bg-emerald-50 text-emerald-600' : 'text-slate-600 hover:bg-gray-50 hover:text-emerald-600'">
                                                <span>Search Region</span>
                                                <template x-if="selected === ''">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </template>
                                            </button>
                                            @foreach($locationOptions['states'] as $state)
                                                <button 
                                                    @click="selected = '{{ $state->id }}'; open = false" 
                                                    class="w-full text-left px-5 py-3 text-sm font-bold transition-all flex items-center justify-between"
                                                    :class="selected == '{{ $state->id }}' ? 'bg-emerald-50 text-emerald-600' : 'text-slate-600 hover:bg-gray-50 hover:text-emerald-600'"
                                                >
                                                    <span>{{ $state->name }}</span>
                                                    <template x-if="selected == '{{ $state->id }}'">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    </template>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Price Dynamics -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
                                        <span class="w-4 h-[1px] bg-emerald-500 mr-2"></span>
                                        Budget Cap
                                    </label>
                                    <span class="text-xs font-black text-emerald-600 tabular-nums">
                                        {{ $selectedPriceRange > 0 ? '₦' . number_format($selectedPriceRange / 1000000) . 'M' : 'Unlimited' }}
                                    </span>
                                </div>
                                <div class="pt-2 px-1">
                                    <input
                                        type="range"
                                        wire:model.live="selectedPriceRange"
                                        min="0"
                                        max="100000000"
                                        step="5000000"
                                        class="premium-slider w-full h-1.5 bg-gray-100 rounded-full appearance-none cursor-pointer accent-emerald-500"
                                    >
                                    <div class="flex justify-between mt-2 text-[8px] font-bold text-slate-400 uppercase tracking-widest">
                                        <span>Min</span>
                                        <span>50M</span>
                                        <span>100M+</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reset Action -->
                        @if($selectedPropertyType || $selectedBedrooms || $selectedState || $selectedCity || $selectedArea || $selectedPriceRange || $searchQuery)
                            <div class="flex justify-center pb-6">
                                <button
                                    wire:click="clearAllFilters"
                                    class="group flex items-center gap-3 px-6 py-2.5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-red-500 transition-all"
                                >
                                    <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center group-hover:bg-red-50 group-hover:rotate-90 transition-all duration-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                    Reset Selection
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

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

/* Premium Range Slider Styling */
.premium-slider::-webkit-slider-thumb {
    appearance: none;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #ffffff;
    border: 4px solid #10b981;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    transition: all 0.3s ease;
}

.premium-slider::-webkit-slider-thumb:hover {
    transform: scale(1.15);
    box-shadow: 0 6px 15px rgba(16, 185, 129, 0.4);
}

.premium-slider::-moz-range-thumb {
    height: 18px;
    width: 18px;
    border-radius: 50%;
    background: #ffffff;
    border: 4px solid #10b981;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    transition: all 0.3s ease;
}

.premium-slider::-webkit-slider-track {
    height: 6px;
    background: #f1f5f9;
    border-radius: 10px;
}

.premium-slider::-moz-range-track {
    height: 6px;
    background: #f1f5f9;
    border-radius: 10px;
}

/* Scrollbar Hide Utility */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Featured Collection Transitions */
.visual-img {
    transform: scale(1.1);
    transition: transform 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
}

.visual-item.active .visual-img {
    transform: scale(1);
}

.visual-item {
    pointer-events: none;
    z-index: 0;
}

.visual-item.active {
    opacity: 1 !important;
    z-index: 10;
    pointer-events: auto;
}

.property-trigger {
    position: relative;
    overflow: hidden;
}

.property-trigger::before {
    content: '';
    position: absolute;
    left: -1px;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 0;
    background: #10b981;
    transition: height 0.5s cubic-bezier(0.19, 1, 0.22, 1);
}

.property-trigger.active::before {
    height: 70%;
}

.property-trigger.active h3 {
    color: white !important;
    padding-left: 8px;
}

@media (min-width: 1024px) {
    .property-trigger.active h3 {
        color: #0f172a !important; /* slate-900 */
    }
}

.property-trigger.active .text-white\/20,
.property-trigger.active .text-slate-200 {
    color: #10b981 !important; /* emerald-500 */
}

.premium-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.premium-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.premium-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 20px;
}
.premium-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}

/* Kinetic Metadata Reveal */
.char-reveal {
    display: inline-block;
    overflow: hidden;
    vertical-align: bottom;
}

.char-inner {
    display: inline-block;
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

    // Watch for Filter Toggle
    document.addEventListener('filter-revealed', () => {
        gsap.fromTo(".grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4 > div", 
            { opacity: 0, y: 30, filter: 'blur(10px)' },
            { 
                opacity: 1, 
                y: 0, 
                filter: 'blur(0px)', 
                duration: 0.6, 
                stagger: 0.08, 
                ease: "back.out(1.2)", 
                delay: 0.1 
            }
        );
    });

    // Featured Collection Logic
    let currentIdx = -1;

    window.switchVisual = (index) => {
        if (currentIdx === index) return;
        currentIdx = index;

        const visualItems = document.querySelectorAll('.visual-item');
        const triggers = document.querySelectorAll('.property-trigger');
        const mobileBadge = document.getElementById('mobile-price-badge');

        // Remove active from all
        visualItems.forEach(item => item.classList.remove('active'));
        triggers.forEach(trigger => trigger.classList.remove('active'));

        // Add to target
        const targetVisual = document.getElementById(`property-visual-${index}`);
        const targetTrigger = document.querySelector(`[data-index="${index}"]`);
        
        if (targetVisual) {
            targetVisual.classList.add('active');
            
            // GSAP Cinematic Reveal for Metadata
            const meta = targetVisual.querySelector('.visual-meta');
            if (meta) {
                gsap.fromTo(meta, 
                    { opacity: 0, y: 30, filter: 'blur(10px)' },
                    { opacity: 1, y: 0, filter: 'blur(0px)', duration: 0.8, ease: "back.out(1.7)", delay: 0.2 }
                );

                // Target inner elements for staggered reveal
                const title = meta.querySelector('h4');
                const price = meta.querySelector('span');
                const btn = meta.querySelector('a');

                gsap.fromTo([price, title, btn],
                    { opacity: 0, x: -20 },
                    { opacity: 1, x: 0, duration: 0.6, stagger: 0.1, ease: "power2.out", delay: 0.3 }
                );
            }
        }

        if (targetTrigger) {
            targetTrigger.classList.add('active');
            
            // Update mobile badge with animation
            if (mobileBadge) {
                const newPrice = targetTrigger.getAttribute('data-price');
                if (mobileBadge.textContent !== newPrice) {
                    gsap.to(mobileBadge, { scale: 0.8, opacity: 0.5, duration: 0.2, onComplete: () => {
                        mobileBadge.textContent = newPrice;
                        gsap.to(mobileBadge, { scale: 1, opacity: 1, duration: 0.4, ease: "back.out(2)" });
                    }});
                }
                mobileBadge.classList.remove('scale-0');
            }
        }
    };

    const initLuxuryAnimation = () => {
        const triggers = gsap.utils.toArray('.property-trigger');
        
        // Desktop Metadata Tilt Effect
        const section = document.getElementById('directors-cut-section');
        section.addEventListener('mousemove', (e) => {
            if (window.innerWidth < 1024) return;
            
            const metas = document.querySelectorAll('.visual-meta');
            const { clientX, clientY } = e;
            const centerX = window.innerWidth * 0.7; // Approx center of visual deck
            const centerY = window.innerHeight / 2;
            
            const moveX = (clientX - centerX) / 50;
            const moveY = (clientY - centerY) / 50;
            
            metas.forEach(meta => {
                if (meta.parentElement.parentElement.classList.contains('active')) {
                    gsap.to(meta, {
                        rotateY: moveX,
                        rotateX: -moveY,
                        x: moveX * 2,
                        y: moveY * 2,
                        duration: 0.5,
                        ease: "power2.out"
                    });
                }
            });
        });

        // Mobile Split View Center Detection via ScrollTrigger
        triggers.forEach((trigger, i) => {
            ScrollTrigger.create({
                scroller: "#property-list-container",
                trigger: trigger,
                start: "top 60%", 
                end: "bottom 40%",
                onToggle: self => {
                    if (self.isActive) switchVisual(i);
                }
            });
        });

        // Initialize first one
        if (triggers.length > 0) {
            switchVisual(0);
        }

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
