<div class="min-h-screen bg-gray-50 font-sans text-gray-900">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-100 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">My Searches</h1>
                    <p class="text-sm text-gray-500">Manage your saved property searches and alerts</p>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="flex items-center gap-3">
                         <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                            Dashboard
                        </a>
                        <span class="text-gray-300">/</span>
                        <span class="text-sm font-medium text-emerald-600">My Searches</span>
                    </div>
                    <a href="{{ route('customer.searches.create') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                        <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                        Create New Search
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl flex items-start gap-3">
                <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                <p class="text-sm font-medium text-emerald-900">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Job Status Indicator -->
        @if($recentlyCreatedSearchId && $searchJobStatus === 'searching')
            <div class="mb-6 p-6 bg-white border border-emerald-100 rounded-2xl shadow-sm"
                 wire:poll.1s="checkJobStatus"
                 x-data="{ listening: false, timeoutId: null }"
                 x-init="
                    timeoutId = setTimeout(() => { $wire.handleSearchTimeout(); }, 30000);
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
                    timeoutId = setTimeout(() => { $wire.handleSearchTimeout(); }, 30000);
                 ">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-emerald-50 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Scanning properties...</h3>
                        <p class="text-sm text-gray-500 mt-1">We're looking for matches based on your criteria. This may take a moment.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Searches List -->
        @if($searches->count() > 0)
            <div class="space-y-6">
                @foreach($searches as $search)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group">
                        <!-- Search Header -->
                        <div class="p-6 border-b border-gray-50">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-600 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                                        <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">{{ $search->name }}</h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($search->is_default)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                                    Default
                                                </span>
                                            @endif
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $search->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-600 border border-gray-100' }}">
                                                {{ $search->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-400 font-medium">
                                    Created {{ $search->created_at->diffForHumans() }}
                                </div>
                            </div>

                            @if($search->description)
                                <p class="text-sm text-gray-500 mb-4">{{ $search->description }}</p>
                            @endif

                            <!-- Criteria Tags -->
                            <div class="flex flex-wrap gap-2">
                                <div class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-100 text-xs font-medium text-gray-600">
                                    <x-heroicon-o-currency-dollar class="w-3.5 h-3.5 mr-1.5 text-gray-400" />
                                    {{ $search->formatted_budget }}
                                </div>
                                <div class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-100 text-xs font-medium text-gray-600">
                                    <x-heroicon-o-map-pin class="w-3.5 h-3.5 mr-1.5 text-gray-400" />
                                    {{ $search->location_display }}
                                </div>
                                <div class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-100 text-xs font-medium text-gray-600">
                                    <x-heroicon-o-home class="w-3.5 h-3.5 mr-1.5 text-gray-400" />
                                    {{ $search->property_types_display }}
                                </div>
                            </div>
                        </div>

                        <!-- Match Indicator -->
                        @if($search->has_matches)
                            <div class="bg-emerald-50/50 border-b border-emerald-100 p-4 sm:px-6">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="relative flex h-2.5 w-2.5">
                                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                        </span>
                                        <span class="text-sm font-semibold text-emerald-900">
                                            {{ $search->recent_matches->count() }} New Matches
                                        </span>
                                    </div>
                                    <a href="{{ route('properties.search') }}?saved_search={{ $search->id }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                                        View All &rarr;
                                    </a>
                                </div>

                                @if($search->latest_matched_property)
                                    <div class="bg-white rounded-xl p-3 border border-emerald-100 shadow-sm flex items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <h4 class="text-sm font-bold text-gray-900 truncate">{{ $search->latest_matched_property['title'] }}</h4>
                                            <p class="text-xs text-gray-500 truncate">{{ $search->latest_matched_property['area'] }}, {{ $search->latest_matched_property['city'] }}</p>
                                        </div>
                                        <span class="text-sm font-bold text-emerald-600 whitespace-nowrap">₦{{ number_format($search->latest_matched_property['price']) }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="bg-gray-50 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex gap-2">
                                <button wire:click="toggleSearchStatus({{ $search->id }})"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors {{ $search->is_active ? 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' : 'bg-emerald-600 border-transparent text-white hover:bg-emerald-700' }}">
                                    @if($search->is_active)
                                        <x-heroicon-o-pause class="w-3.5 h-3.5 mr-1.5" /> Pause
                                    @else
                                        <x-heroicon-o-play class="w-3.5 h-3.5 mr-1.5" /> Activate
                                    @endif
                                </button>
                                
                                @if(!$search->is_default)
                                    <button wire:click="setDefaultSearch({{ $search->id }})"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors">
                                        <x-heroicon-o-star class="w-3.5 h-3.5 mr-1.5" /> Make Default
                                    </button>
                                @endif
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('customer.searches.edit', $search) }}"
                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 transition-colors">
                                    <x-heroicon-o-pencil class="w-3.5 h-3.5 mr-1.5" /> Edit
                                </a>
                                <button wire:click="deleteSearch({{ $search->id }})"
                                        wire:confirm="Are you sure you want to delete this search?"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-100 transition-colors">
                                    <x-heroicon-o-trash class="w-3.5 h-3.5 mr-1.5" /> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($searches->hasPages())
                <div class="mt-8">
                    {{ $searches->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-heroicon-o-magnifying-glass class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No searches yet</h3>
                <p class="text-gray-500 max-w-sm mx-auto mb-8">Create your first saved search to get personalized property recommendations and stay updated with new listings.</p>
                <a href="{{ route('customer.searches.create') }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                    Create Your First Search
                </a>
            </div>
        @endif
    </div>
</div>