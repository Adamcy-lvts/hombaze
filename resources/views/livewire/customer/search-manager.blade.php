<div class="min-h-screen bg-gray-50/50 font-sans text-gray-900">
    <!-- Header Section -->
    <div class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-30 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">SmartSearch</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage your intelligent property alerts and hunters</p>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="hidden md:flex items-center gap-2 text-sm text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                         <a href="{{ route('dashboard') }}" class="hover:text-gray-900 transition-colors font-medium">
                            Dashboard
                        </a>
                        <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="font-medium text-emerald-600">SmartSearch</span>
                    </div>
                    <a href="{{ route('customer.searches.create') }}"
                       class="inline-flex items-center justify-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all duration-300 shadow-lg shadow-emerald-600/20 hover:shadow-emerald-600/30 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Create New Search
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 animate-in slide-in-from-top-4 duration-500 shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-sm font-medium text-emerald-900">{{ session('success') }}</p>
                <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        <!-- Job Status Indicator -->
        @if($recentlyCreatedSearchId && $searchJobStatus === 'searching')
            <div class="mb-8 overflow-hidden rounded-2xl bg-white shadow-lg border border-emerald-100/50"
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
                <div class="relative p-6 sm:p-8">
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-50/50 via-transparent to-transparent"></div>
                    <div class="relative flex items-center gap-6">
                        <div class="relative w-16 h-16 flex items-center justify-center shrink-0">
                            <div class="absolute inset-0 bg-emerald-100 rounded-full animate-ping opacity-25"></div>
                            <div class="relative bg-white rounded-full p-3 shadow-sm border border-emerald-100">
                                <svg class="w-8 h-8 text-emerald-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Hunting for properties...</h3>
                            <p class="text-gray-500 mt-1">We're scanning the entire database for your perfect match. Sit tight!</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Searches List -->
        @if($searches->count() > 0)
            <div class="space-y-8">
                @foreach($searches as $search)
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group relative">
                        <!-- Decorative Top Border -->
                        <div class="absolute top-0 inset-x-0 h-1bg-gradient-to-r from-transparent via-emerald-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="grid lg:grid-cols-12 gap-0">
                            <!-- Main Info Section -->
                            <div class="lg:col-span-7 p-6 sm:p-8 flex flex-col justify-between relative z-10">
                                <div>
                                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-all duration-300 shadow-sm">
                                                @if($search->selected_property_type == 3) <!-- Land -->
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @else
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                                @endif
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900 group-hover:text-emerald-600 transition-colors">{{ $search->name }}</h3>
                                                <div class="flex items-center gap-2 mt-1.5">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide {{ $search->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-600 border border-gray-200' }}">
                                                        {{ $search->is_active ? 'Active Hunting' : 'Paused' }}
                                                    </span>
                                                    @if($search->is_default)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide bg-amber-50 text-amber-700 border border-amber-100">
                                                            Default
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-xs font-medium text-gray-400 bg-gray-50 px-3 py-1 rounded-full">
                                            Created {{ $search->created_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    @if($search->description)
                                        <p class="text-sm text-gray-600 mb-6 leading-relaxed">{{ $search->description }}</p>
                                    @endif

                                    <!-- Criteria Tags -->
                                    <div class="flex flex-wrap gap-2 mb-8">
                                        <div class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-100 text-xs font-semibold text-gray-700 group-hover:border-emerald-100 transition-colors">
                                            <svg class="w-3.5 h-3.5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $search->formatted_budget }}
                                        </div>
                                        <div class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-100 text-xs font-semibold text-gray-700 group-hover:border-emerald-100 transition-colors">
                                            <svg class="w-3.5 h-3.5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            {{ $search->location_display }}
                                        </div>
                                        <div class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-50 border border-gray-100 text-xs font-semibold text-gray-700 group-hover:border-emerald-100 transition-colors">
                                            <svg class="w-3.5 h-3.5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            {{ $search->property_types_display }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions Bar -->
                                <div class="flex flex-wrap items-center gap-3 pt-6 border-t border-dashed border-gray-200">
                                    <button wire:click="toggleSearchStatus({{ $search->id }})"
                                            class="inline-flex items-center px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl border-2 transition-all duration-200 {{ $search->is_active ? 'border-gray-200 text-gray-600 hover:border-gray-300 hover:text-gray-800 bg-white' : 'bg-emerald-600 border-transparent text-white hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-500/20' }}">
                                        @if($search->is_active)
                                            <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Pause Search
                                        @else
                                            <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Activate Search
                                        @endif
                                    </button>

                                    <a href="{{ route('customer.searches.edit', $search) }}"
                                       class="inline-flex items-center px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl bg-gray-50 text-gray-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </a>

                                    <div class="ml-auto flex items-center gap-2">
                                        @if(!$search->is_default)
                                            <button wire:click="setDefaultSearch({{ $search->id }})" title="Make Default"
                                                    class="p-2 text-gray-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                            </button>
                                        @endif
                                        <button wire:click="deleteSearch({{ $search->id }})" 
                                                wire:confirm="Are you sure you want to delete this search?" title="Delete Search"
                                                class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Matches Side Section -->
                            <div class="lg:col-span-5 bg-gray-50/80 border-l border-gray-100 p-6 sm:p-8 flex flex-col relative overflow-hidden">
                                @if($search->has_matches)
                                    <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                                        <svg class="w-64 h-64" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                            <path fill="#10B981" d="M47.5,-73.2C61.4,-66.1,72.3,-54.6,79.5,-41.2C86.7,-27.9,90.2,-12.7,87.9,1.7C85.5,16,77.3,29.5,67.6,41.2C57.9,52.8,46.7,62.6,34,68.6C21.3,74.7,7.1,77,-6,75.4C-19.1,73.8,-31.1,68.2,-42.6,61.1C-54.1,54,-65,45.3,-72.6,33.8C-80.2,22.3,-84.5,8,-83.1,-5.7C-81.7,-19.3,-74.6,-32.3,-64.6,-42.6C-54.6,-52.9,-41.7,-60.5,-29.2,-68.4C-16.7,-76.3,-4.6,-84.5,6.5,-83.4C17.6,-82.3,33.6,-80.3,47.5,-73.2Z" transform="translate(100 100)" />
                                        </svg>
                                    </div>

                                    <div class="relative z-10 h-full flex flex-col">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="font-bold text-gray-900 flex items-center gap-2">
                                                <span class="flex h-2.5 w-2.5 relative">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                                </span>
                                                Found Matches
                                            </h4>
                                            <a href="{{ route('properties.search', ['saved_search' => $search->id]) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline">
                                                See All
                                            </a>
                                        </div>

                                        <div class="flex-1 space-y-3">
                                            @foreach($search->matched_properties as $property)
                                                @if($loop->iteration <= 2)
                                                    <a href="{{ route('property.show', $property->slug) }}" class="block bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-gray-100 group/match">
                                                        <div class="flex">
                                                            <div class="w-24 h-24 bg-gray-200 shrink-0 relative overflow-hidden">
                                                                @if($property->getFirstMediaUrl('featured', 'thumb'))
                                                                     <img src="{{ $property->getFirstMediaUrl('featured', 'thumb') }}" 
                                                                          class="w-full h-full object-cover group-hover/match:scale-110 transition-transform duration-500" 
                                                                          alt="{{ $property->title }}">
                                                                @elseif($property->getFirstMediaUrl('gallery', 'thumb'))
                                                                    <img src="{{ $property->getFirstMediaUrl('gallery', 'thumb') }}" 
                                                                         class="w-full h-full object-cover group-hover/match:scale-110 transition-transform duration-500" 
                                                                         alt="{{ $property->title }}">
                                                                @else
                                                                    <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-300">
                                                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="flex-1 p-3 min-w-0 flex flex-col justify-center">
                                                                <h5 class="text-sm font-bold text-gray-900 truncate mb-1 pr-4">{{ $property->title }}</h5>
                                                                <p class="text-xs text-gray-500 truncate mb-2">{{ $property->city->name ?? '' }}, {{ $property->state->name ?? '' }}</p>
                                                                <div class="font-bold text-emerald-600 text-sm">
                                                                    ₦{{ number_format($property->price) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endif
                                            @endforeach
                                            
                                            <a href="{{ route('properties.search', ['saved_search' => $search->id]) }}" class="block w-full text-center py-2.5 text-xs font-semibold text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-700 transition-colors">
                                                We found {{ $search->recent_matches_count }} properties that match your search criteria

                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="h-full flex flex-col items-center justify-center text-center p-6 text-gray-400">
                                        <div class="w-16 h-16 rounded-full bg-gray-100/50 flex items-center justify-center mb-3">
                                            <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </div>
                                        <p class="text-sm font-medium">No matches yet</p>
                                        <p class="text-xs mt-1 max-w-[150px]">We'll notify you as soon as properties appear.</p>
                                    </div>
                                @endif
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
            <!-- Empty State (Enhanced) -->
            <div class="text-center py-24 bg-white/60 backdrop-blur-md rounded-3xl border border-gray-200 shadow-sm relative overflow-hidden">
                 <div class="absolute inset-x-0 bottom-0 top-1/2 bg-gradient-to-t from-emerald-50/50 to-transparent"></div>
                <div class="relative z-10 max-w-lg mx-auto px-6">
                    <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm shadow-emerald-100 placeholder-wave">
                        <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Let's find your dream property</h3>
                    <p class="text-gray-500 mb-8 leading-relaxed">You haven't created any search alerts yet. Tell us what you're looking for, and we'll scour the market 24/7 to find it for you.</p>
                    
                    <a href="{{ route('customer.searches.create') }}"
                       class="inline-flex items-center justify-center px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl transition-all duration-300 shadow-xl shadow-emerald-600/30 hover:scale-105 hover:shadow-2xl hover:shadow-emerald-600/40">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                        Create Your First Search
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
