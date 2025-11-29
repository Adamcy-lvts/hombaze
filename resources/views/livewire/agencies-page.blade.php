<div class="min-h-screen bg-gray-50 font-sans text-gray-900">
    <!-- Clean Header Section -->
    <section class="relative bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <!-- Breadcrumb -->
            <div class="max-w-7xl mb-6 lg:mb-8">
                <nav class="flex items-center" aria-label="Breadcrumb">
                    <ol class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 overflow-x-auto whitespace-nowrap px-4 py-2.5 border border-gray-100 rounded-2xl bg-white shadow-sm w-full sm:w-auto">
                        <li>
                            <a href="{{ route('landing') }}" wire:navigate
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
                                Real Estate Agencies
                            </span>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Clean Header -->
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-8">
                <div class="max-w-2xl">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3 tracking-tight">
                        Top Real Estate <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">Agencies</span>
                    </h1>
                    <p class="text-lg text-gray-600 leading-relaxed">Browse verified agencies with extensive property portfolios.</p>
                </div>

                <!-- Results Count -->
                <div class="flex items-center space-x-3 bg-white border border-gray-100 px-5 py-2.5 rounded-2xl shadow-sm">
                    <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></div>
                    <span class="font-bold text-gray-900">{{ $agencies->total() }}</span>
                    <span class="text-gray-500 font-medium">agencies found</span>
                </div>
            </div>

            <!-- Clean Search Bar -->
            <div class="max-w-4xl mx-auto relative mb-8 z-30">
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full opacity-20 group-hover:opacity-30 blur transition duration-200"></div>
                    <div class="relative flex items-center bg-white rounded-full shadow-lg">
                        <div class="pl-6 text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.500ms="searchQuery"
                            placeholder="Search by agency name or location..."
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
            </div>

            <!-- Clean Filters -->
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
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
                    </button>

                    <!-- Sort Options -->
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-600">Sort by:</span>
                        <div class="relative">
                            <select wire:model.live="sortBy"
                                class="appearance-none pl-4 pr-10 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-medium text-gray-900 cursor-pointer hover:bg-white transition-colors">
                                <option value="name">Agency Name</option>
                                <option value="properties_count">Most Properties</option>
                                <option value="agents_count">Most Agents</option>
                                <option value="newest">Newest First</option>
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
                            <!-- Verification -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Verification</label>
                                <div class="space-y-2.5">
                                    <label class="flex items-center group cursor-pointer">
                                        <div class="relative flex items-center">
                                            <input type="checkbox" wire:model.live="verifiedOnly" class="peer h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 transition duration-150 ease-in-out">
                                        </div>
                                        <span class="ml-3 text-sm text-gray-700 group-hover:text-emerald-700 transition-colors">Verified agencies only</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Clean Agencies Grid Section -->
    <section class="py-8 lg:py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($agencies->count() > 0)
                <!-- Agencies Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    @foreach($agencies as $agency)
                        <a href="{{ route('agency.show', $agency) }}" wire:navigate
                           class="group block transition-all duration-300 hover:-translate-y-1">

                            <!-- Agency Card -->
                            <div class="relative bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group-hover:shadow-xl transition-shadow duration-300 h-full flex flex-col">
                                <!-- Agency Cover/Logo Area -->
                                <div class="relative h-48 bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-8">
                                    @if($agency->logo_url)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($agency->logo_url) }}"
                                             alt="{{ $agency->name }}"
                                             class="max-w-full max-h-full object-contain filter drop-shadow-sm group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-24 h-24 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-gray-100 group-hover:scale-105 transition-transform duration-300">
                                            <span class="text-gray-400 font-bold text-3xl">{{ substr($agency->name, 0, 1) }}</span>
                                        </div>
                                    @endif

                                    <!-- Verified Badge -->
                                    @if($agency->is_verified)
                                        <div class="absolute top-3 right-3">
                                            <div class="inline-flex items-center space-x-1 bg-white/95 backdrop-blur-sm px-2.5 py-1 rounded-lg shadow-sm">
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="text-xs font-bold text-gray-800">Verified</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Agency Details -->
                                <div class="p-6 flex-1 flex flex-col">
                                    <!-- Name -->
                                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-emerald-600 transition-colors duration-200 mb-2">
                                        {{ $agency->name }}
                                    </h3>

                                    <!-- Description -->
                                    <p class="text-gray-500 text-sm line-clamp-2 mb-4 flex-1">
                                        {{ $agency->description ?? 'No description available.' }}
                                    </p>

                                    <!-- Stats Grid -->
                                    <div class="grid grid-cols-2 gap-4 py-4 border-t border-gray-50">
                                        <div class="text-center p-2 bg-gray-50 rounded-xl">
                                            <span class="block text-lg font-bold text-gray-900">{{ $agency->properties_count ?? 0 }}</span>
                                            <span class="text-xs text-gray-500 font-medium uppercase tracking-wide">Properties</span>
                                        </div>
                                        <div class="text-center p-2 bg-gray-50 rounded-xl">
                                            <span class="block text-lg font-bold text-gray-900">{{ $agency->agents_count ?? 0 }}</span>
                                            <span class="text-xs text-gray-500 font-medium uppercase tracking-wide">Agents</span>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="mt-4">
                                        <div class="w-full bg-white border border-gray-200 text-gray-700 py-2.5 rounded-xl text-sm font-bold text-center group-hover:bg-emerald-600 group-hover:text-white group-hover:border-emerald-600 transition-all duration-200 shadow-sm">
                                            View Agency
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Custom Pagination -->
                <div class="mt-12 lg:mt-16 flex justify-center">
                    @if($agencies->hasPages())
                        <nav class="flex items-center space-x-2" role="navigation" aria-label="Pagination Navigation">
                            {{-- Previous Page Link --}}
                            @if ($agencies->onFirstPage())
                                <span class="flex items-center justify-center w-10 h-10 text-gray-300 bg-gray-50 border border-gray-100 rounded-xl cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $agencies->previousPageUrl() }}"
                                   class="flex items-center justify-center w-10 h-10 text-gray-700 bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-xl transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </a>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($agencies->getUrlRange(1, $agencies->lastPage()) as $page => $url)
                                @if ($page == $agencies->currentPage())
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
                            @if ($agencies->hasMorePages())
                                <a href="{{ $agencies->nextPageUrl() }}"
                                   class="flex items-center justify-center w-10 h-10 text-gray-700 bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-xl transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @else
                                <span class="flex items-center justify-center w-10 h-10 text-gray-300 bg-gray-50 border border-gray-100 rounded-xl cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No agencies found</h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto">We couldn't find any agencies matching your search criteria. Try adjusting your filters or search terms.</p>
                    <button wire:click="clearAllFilters"
                        class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-emerald-500/30">
                        Browse All Agencies
                    </button>
                </div>
            @endif
        </div>
    </section>
</div>