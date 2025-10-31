<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20">
    <div class="w-full max-w-7xl mx-auto">
        <!-- Mobile-Optimized Header -->
        <div class="relative overflow-hidden bg-white/80 backdrop-blur-xl border-b border-white/20 shadow-sm">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-cyan-600/5"></div>
            <div class="relative px-3 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-8">
                <div class="flex flex-col space-y-4 sm:space-y-6 lg:flex-row lg:items-center lg:justify-between lg:space-y-0">
                    <div class="space-y-1 sm:space-y-2">
                        <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-purple-800 bg-clip-text text-transparent leading-tight">
                            My Searches
                        </h1>
                        <p class="text-xs sm:text-sm md:text-base text-slate-600 max-w-lg leading-relaxed">
                            Manage your saved property searches and discover your perfect home
                        </p>
                    </div>
                    <div class="w-full sm:w-auto lg:w-auto">
                        <a href="{{ route('customer.searches.create') }}"
                           class="group w-full sm:w-auto lg:w-auto inline-flex items-center justify-center px-4 py-3 sm:px-6 sm:py-3.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/25 transform hover:-translate-y-0.5">
                            <x-heroicon-o-plus class="w-4 h-4 sm:w-5 sm:h-5 mr-2 transition-transform group-hover:scale-110" />
                            Create New Search
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile-Optimized Flash Messages -->
        @if (session()->has('success'))
            <div class="mx-3 mt-4 p-4 sm:mx-6 sm:mt-6 sm:p-5 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200/60 rounded-xl sm:rounded-2xl shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                            <x-heroicon-o-check-circle class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600" />
                        </div>
                    </div>
                    <div class="ml-3 sm:ml-4">
                        <p class="text-xs sm:text-sm font-medium text-emerald-900">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Mobile-Optimized Job Status Indicator -->
        @if($recentlyCreatedSearchId && $searchJobStatus === 'searching')
            <div class="mx-3 mt-4 p-4 sm:mx-6 sm:mt-6 sm:p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/60 rounded-xl sm:rounded-2xl shadow-sm"
                 wire:poll.1s="checkJobStatus"
                 x-data="{ listening: false, timeoutId: null }"
                 x-init="
                    // Set a 30-second timeout
                    timeoutId = setTimeout(() => {
                        $wire.handleSearchTimeout();
                    }, 30000);

                    // Listen for WebSocket events
                    if (window.Echo && !listening) {
                        listening = true;
                        window.Echo.private('user.{{ Auth::id() }}')
                            .listen('search-job-completed', (e) => {
                                if (e.searchId == {{ $recentlyCreatedSearchId }}) {
                                    if (timeoutId) clearTimeout(timeoutId);
                                    $wire.onSearchJobCompleted(e.searchId, e.success, e.matchCount, e.message);
                                }
                            });
                    }
                 "
                 @start-search-timeout.window="
                    if (timeoutId) clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => {
                        $wire.handleSearchTimeout();
                    }, 30000);
                 "
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3 sm:ml-4">
                        <p class="text-sm sm:text-base font-semibold text-blue-900 mb-1">
                            🔍 Scanning all available properties for matches...
                        </p>
                        <p class="text-xs sm:text-sm text-blue-700 leading-relaxed">
                            This may take a few moments. You'll receive notifications based on your preferences when matches are found.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Mobile-Optimized Searches List -->
        @if($searches->count() > 0)
            <div class="px-3 py-4 space-y-4 sm:px-6 sm:py-6 sm:space-y-6">
                @foreach($searches as $search)
                    <div class="group bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-sm border border-gray-100/60 overflow-hidden hover:shadow-xl hover:shadow-gray-200/40 transition-all duration-300 hover:-translate-y-1">
                        <!-- Mobile-Optimized Search Header -->
                        <div class="p-4 sm:p-6 border-b border-gray-100/60">
                            <!-- Header with Icon -->
                            <div class="flex items-center justify-between mb-3 sm:mb-4">
                                <div class="flex items-center space-x-2">
                                    <div class="relative">
                                        <div class="p-1 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg group-hover:from-blue-100 group-hover:to-indigo-100 transition-colors duration-300">
                                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-blue-600" />
                                        </div>
                                        @if($search->has_matches)
                                            <div class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-emerald-500 rounded-full border border-white animate-pulse"></div>
                                        @endif
                                    </div>
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 line-clamp-1">
                                        {{ $search->name }}
                                    </h3>
                                </div>
                            </div>

                            <!-- Content Section -->
                            <div>

                                    <!-- Mobile-Optimized Status Badges -->
                                    <div class="flex flex-wrap gap-1.5 sm:gap-2 mb-3 sm:mb-4">
                                        @if($search->is_default)
                                            <span class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1.5 rounded-full text-xs font-semibold bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-800 shadow-sm">
                                                <x-heroicon-o-star class="w-3 h-3 sm:w-3.5 sm:h-3.5 mr-1 sm:mr-1.5" />
                                                Default
                                            </span>
                                        @endif
                                        @if($search->has_matches)
                                            <span class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1.5 rounded-full text-xs font-semibold bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800 shadow-sm">
                                                <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-emerald-500 rounded-full mr-1.5 sm:mr-2 animate-pulse"></div>
                                                <span class="hidden xs:inline">{{ $search->recent_matches->count() }} New </span>Match{{ $search->recent_matches->count() > 1 ? 'es' : '' }}
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1.5 rounded-full text-xs font-semibold {{ $search->is_active ? 'bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800' : 'bg-gradient-to-r from-gray-100 to-slate-100 text-slate-600' }} shadow-sm">
                                            <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 {{ $search->is_active ? 'bg-emerald-500' : 'bg-slate-400' }} rounded-full mr-1.5 sm:mr-2"></div>
                                            {{ $search->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>

                                    @if($search->description)
                                        <p class="text-xs sm:text-sm text-slate-600 mb-3 sm:mb-4 leading-relaxed">{{ $search->description }}</p>
                                    @endif

                                    <!-- Mobile-Optimized Search Criteria Cards -->
                                    <div class="space-y-2 sm:grid sm:grid-cols-1 md:grid-cols-3 sm:gap-3 sm:space-y-0 mb-3 sm:mb-4">
                                        <div class="flex items-center space-x-2 sm:space-x-3 p-2.5 sm:p-3 bg-gradient-to-r from-blue-50/50 to-cyan-50/50 rounded-lg sm:rounded-xl border border-blue-100/50">
                                            <div class="p-1 sm:p-1.5 bg-blue-100 rounded">
                                                <x-heroicon-o-currency-dollar class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-blue-600" />
                                            </div>
                                            <span class="text-xs sm:text-sm font-medium text-slate-700 truncate">{{ $search->formatted_budget }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2 sm:space-x-3 p-2.5 sm:p-3 bg-gradient-to-r from-emerald-50/50 to-green-50/50 rounded-lg sm:rounded-xl border border-emerald-100/50">
                                            <div class="p-1 sm:p-1.5 bg-emerald-100 rounded">
                                                <x-heroicon-o-map-pin class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-emerald-600" />
                                            </div>
                                            <span class="text-xs sm:text-sm font-medium text-slate-700 truncate">{{ $search->location_display }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2 sm:space-x-3 p-2.5 sm:p-3 bg-gradient-to-r from-purple-50/50 to-pink-50/50 rounded-lg sm:rounded-xl border border-purple-100/50">
                                            <div class="p-1 sm:p-1.5 bg-purple-100 rounded">
                                                <x-heroicon-o-home class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-purple-600" />
                                            </div>
                                            <span class="text-xs sm:text-sm font-medium text-slate-700 truncate">{{ $search->property_types_display }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center text-xs text-slate-500 bg-slate-50/50 rounded-lg px-2.5 py-1.5 sm:px-3 sm:py-2">
                                        <x-heroicon-o-clock class="w-3 h-3 sm:w-3.5 sm:h-3.5 mr-1.5 sm:mr-2 flex-shrink-0" />
                                        <span class="truncate">Created {{ $search->created_at->diffForHumans() }}</span>
                                        @if($search->updated_at != $search->created_at)
                                            <span class="mx-1 sm:mx-2 hidden sm:inline">•</span>
                                            <span class="hidden sm:inline truncate">Updated {{ $search->updated_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                            </div>
                        </div>

                        <!-- Mobile-Optimized Match Indicator Section -->
                        @if($search->has_matches)
                            <div class="relative overflow-hidden bg-gradient-to-r from-emerald-50/80 via-green-50/60 to-teal-50/80 border-b border-emerald-100/60">
                                <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/5 to-green-500/5"></div>
                                <div class="relative p-4 sm:p-6">
                                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                                        <div class="flex items-center space-x-2 sm:space-x-3">
                                            <div class="flex items-center space-x-1.5 sm:space-x-2">
                                                <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-emerald-500 rounded-full animate-pulse shadow-sm"></div>
                                                <span class="text-sm sm:text-base font-bold text-emerald-900">
                                                    {{ $search->recent_matches->count() }} <span class="hidden xs:inline">recent </span>match{{ $search->recent_matches->count() > 1 ? 'es' : '' }}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="text-xs text-emerald-700 font-semibold bg-white/60 px-2 py-0.5 sm:px-3 sm:py-1 rounded-full">
                                            {{ $search->recent_matches->first()->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    @if($search->latest_matched_property)
                                        <div class="bg-white/90 backdrop-blur-sm rounded-lg sm:rounded-xl p-3 sm:p-4 border border-emerald-200/60 shadow-sm">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1 min-w-0 pr-3 sm:pr-4">
                                                    <h4 class="text-xs sm:text-sm font-bold text-gray-900 mb-2 line-clamp-2">
                                                        {{ $search->latest_matched_property['title'] }}
                                                    </h4>
                                                    <div class="space-y-1.5 sm:space-y-2">
                                                        <div class="flex items-center space-x-1.5 sm:space-x-2">
                                                            <div class="p-0.5 sm:p-1 bg-slate-100 rounded">
                                                                <x-heroicon-o-map-pin class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-slate-500" />
                                                            </div>
                                                            <span class="text-xs text-slate-600 truncate">{{ $search->latest_matched_property['area'] }}, {{ $search->latest_matched_property['city'] }}</span>
                                                        </div>
                                                        <div class="flex items-center space-x-1.5 sm:space-x-2">
                                                            <div class="p-0.5 sm:p-1 bg-emerald-100 rounded">
                                                                <x-heroicon-o-currency-dollar class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-emerald-600" />
                                                            </div>
                                                            <span class="text-xs sm:text-sm font-bold text-emerald-800">₦{{ number_format($search->latest_matched_property['price']) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a href="{{ route('property.show', $search->latest_matched_property['slug']) }}"
                                                   class="flex-shrink-0 inline-flex items-center px-3 py-2 sm:px-4 sm:py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white text-xs font-semibold rounded-lg sm:rounded-xl transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                                                    <x-heroicon-o-eye class="w-3 h-3 sm:w-3.5 sm:h-3.5 mr-1 sm:mr-1.5" />
                                                    <span class="hidden xs:inline">View</span>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Mobile-Optimized Actions Section -->
                        <div class="bg-gradient-to-r from-slate-50/80 to-gray-50/60 px-4 py-3 sm:px-6 sm:py-4">
                            <!-- Primary Actions -->
                            <div class="flex flex-col xs:flex-row gap-2 sm:gap-3 mb-3 sm:mb-4">
                                @if($search->has_matches)
                                    <a href="{{ route('properties.search') }}?saved_search={{ $search->id }}"
                                       class="group flex-1 inline-flex items-center justify-center px-4 py-2.5 sm:px-5 sm:py-2.5 text-xs sm:text-sm font-semibold rounded-lg sm:rounded-xl bg-gradient-to-r from-emerald-600 to-green-600 text-white hover:from-emerald-700 hover:to-green-700 transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                                        <x-heroicon-o-eye class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 transition-transform group-hover:scale-110" />
                                        View All Matches
                                    </a>
                                @endif

                                <button wire:click="toggleSearchStatus({{ $search->id }})"
                                        class="group flex-1 inline-flex items-center justify-center px-4 py-2.5 sm:px-5 sm:py-2.5 text-xs sm:text-sm font-semibold rounded-lg sm:rounded-xl {{ $search->is_active ? 'bg-gradient-to-r from-orange-100 to-amber-100 text-orange-700 hover:from-orange-200 hover:to-amber-200' : 'bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-700 hover:from-emerald-200 hover:to-green-200' }} transition-all duration-300 shadow-sm hover:shadow-md">
                                    @if($search->is_active)
                                        <x-heroicon-o-pause class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 transition-transform group-hover:scale-110" />
                                        Pause
                                    @else
                                        <x-heroicon-o-play class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5 sm:mr-2 transition-transform group-hover:scale-110" />
                                        Activate
                                    @endif
                                </button>
                            </div>

                            <!-- Secondary Actions -->
                            <div class="flex flex-wrap gap-1.5 sm:gap-2">
                                @if(!$search->is_default)
                                    <button wire:click="setDefaultSearch({{ $search->id }})"
                                            class="group inline-flex items-center px-2.5 py-1.5 sm:px-3 sm:py-2 text-xs font-semibold rounded-lg bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-700 hover:from-amber-200 hover:to-yellow-200 transition-all duration-300 shadow-sm">
                                        <x-heroicon-o-star class="w-3 h-3 sm:w-3.5 sm:h-3.5 mr-1 sm:mr-1.5 transition-transform group-hover:scale-110" />
                                        <span class="hidden xs:inline">Make </span>Default
                                    </button>
                                @endif

                                <a href="{{ route('customer.searches.edit', $search) }}"
                                   class="group inline-flex items-center px-2.5 py-1.5 sm:px-3 sm:py-2 text-xs font-semibold rounded-lg bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 hover:from-blue-200 hover:to-indigo-200 transition-all duration-300 shadow-sm">
                                    <x-heroicon-o-pencil class="w-3 h-3 sm:w-3.5 sm:h-3.5 mr-1 sm:mr-1.5 transition-transform group-hover:scale-110" />
                                    Edit
                                </a>

                                <button wire:click="deleteSearch({{ $search->id }})"
                                        wire:confirm="Are you sure you want to delete this search?"
                                        class="group inline-flex items-center px-2.5 py-1.5 sm:px-3 sm:py-2 text-xs font-semibold rounded-lg bg-gradient-to-r from-red-100 to-rose-100 text-red-700 hover:from-red-200 hover:to-rose-200 transition-all duration-300 shadow-sm">
                                    <x-heroicon-o-trash class="w-3 h-3 sm:w-3.5 sm:h-3.5 mr-1 sm:mr-1.5 transition-transform group-hover:scale-110" />
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Mobile-Optimized Pagination -->
            @if($searches->hasPages())
                <div class="px-3 py-6 sm:px-6 sm:py-8">
                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-3 sm:p-4 border border-gray-100/60">
                        {{ $searches->links() }}
                    </div>
                </div>
            @endif
        @else
            <!-- Mobile-Optimized Empty State -->
            <div class="flex flex-col items-center justify-center px-4 py-16 sm:py-20 text-center">
                <div class="relative mb-6 sm:mb-8">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-blue-100 via-indigo-100 to-purple-100 rounded-2xl sm:rounded-3xl flex items-center justify-center shadow-lg">
                        <x-heroicon-o-magnifying-glass class="w-10 h-10 sm:w-12 sm:h-12 text-blue-600" />
                    </div>
                    <div class="absolute -top-1 -right-1 sm:-top-2 sm:-right-2 w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center shadow-lg">
                        <x-heroicon-o-plus class="w-3 h-3 sm:w-4 sm:h-4 text-white" />
                    </div>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3 sm:mb-4">No searches yet</h3>
                <p class="text-sm sm:text-base lg:text-lg text-slate-600 mb-8 sm:mb-10 max-w-sm sm:max-w-md leading-relaxed px-2">
                    Create your first saved search to get personalized property recommendations and stay updated with new listings.
                </p>
                <a href="{{ route('customer.searches.create') }}"
                   class="group w-full xs:w-auto inline-flex items-center justify-center px-6 py-3 sm:px-8 sm:py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white text-sm sm:text-base font-semibold rounded-xl sm:rounded-2xl transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/25 transform hover:-translate-y-1">
                    <x-heroicon-o-plus class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3 transition-transform group-hover:scale-110" />
                    Create Your First Search
                </a>
            </div>
        @endif
    </div>
</div>