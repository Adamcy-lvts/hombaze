<div class="min-h-screen bg-gray-50">
    <!-- Clean Header Section -->
    <section class="relative bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
                <a href="{{ route('landing') }}" wire:navigate class="hover:text-emerald-600 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="font-medium text-gray-900">Real Estate Agents</span>
            </nav>

            <!-- Clean Header -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-6">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-semibold text-gray-900 mb-2">
                        Find Your <span class="text-emerald-600">Perfect</span> Agent
                    </h1>
                    <p class="text-gray-600">Connect with trusted real estate professionals across Nigeria</p>
                </div>

                <!-- Results Count -->
                <div class="flex items-center space-x-3 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                    <span class="font-semibold text-gray-900">{{ $agents->total() }}</span>
                    <span class="text-gray-600">agents found</span>
                </div>
            </div>

            <!-- Clean Search Bar -->
            <div class="max-w-3xl mx-auto relative mb-6">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="searchQuery"
                        placeholder="Search by agent name, agency, or location..."
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
            <div class="bg-white/90 backdrop-blur-sm border border-gray-200/50 rounded-xl p-3 mb-4 shadow-sm">
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
                            <option value="name">Agent Name</option>
                            <option value="rating">Highest Rated</option>
                            <option value="experience">Most Experienced</option>
                            <option value="listings">Most Listings</option>
                        </select>
                    </div>
                </div>

                <!-- Expanded Filters Panel -->
                @if($showFilters)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Agent Type -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-2">Agent Type</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.live="selectedAgentTypes" value="independent" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-2 text-sm text-gray-700">Independent</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.live="selectedAgentTypes" value="agency" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-2 text-sm text-gray-700">Agency</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Experience Level -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-2">Experience</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.live="selectedExperience" value="0-2" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-2 text-sm text-gray-700">New (0-2 years)</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.live="selectedExperience" value="3-5" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-2 text-sm text-gray-700">Experienced (3-5 years)</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.live="selectedExperience" value="5+" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-2 text-sm text-gray-700">Expert (5+ years)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Rating -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-2">Minimum Rating</label>
                                <div class="flex flex-wrap gap-2">
                                    @for($i = 5; $i >= 3; $i--)
                                        <button
                                            wire:click="toggleFilter('rating', '{{ $i }}')"
                                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 {{ $selectedRating == $i ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-yellow-100' }}"
                                        >
                                            {{ $i }}+ ⭐
                                        </button>
                                    @endfor
                                </div>
                            </div>

                            <!-- Verification -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-2">Verification</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.live="verifiedOnly" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="ml-2 text-sm text-gray-700">Verified agents only</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Clean Agents Grid Section -->
    <section class="py-6 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($agents->count() > 0)
                <!-- Agents Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($agents as $agent)
                        <a href="{{ route('agent.profile', $agent) }}" wire:navigate
                           class="group block transition-all duration-200 hover:scale-105">

                            <!-- Agent Card -->
                            <div class="relative bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <!-- Agent Avatar -->
                                <div class="relative h-48 bg-gradient-to-br from-emerald-50 to-blue-50 flex items-center justify-center">
                                    @if($agent->agentProfile && $agent->agentProfile->profile_photo_url)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($agent->agentProfile->profile_photo_url) }}"
                                             alt="{{ $agent->name }}"
                                             class="w-24 h-24 rounded-full border-4 border-white shadow-lg object-cover">
                                    @else
                                        <div class="w-24 h-24 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                                            <span class="text-white font-bold text-2xl">{{ substr($agent->name, 0, 1) }}</span>
                                        </div>
                                    @endif

                                    <!-- Verified Badge -->
                                    @if($agent->agentProfile && $agent->agentProfile->is_verified)
                                        <div class="absolute top-3 right-3">
                                            <div class="inline-flex items-center space-x-1 bg-blue-100/90 backdrop-blur-sm border border-blue-300/50 text-blue-700 px-2 py-1 rounded-lg font-bold text-xs shadow-lg">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                <span>Verified</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Agent Details -->
                                <div class="p-4 space-y-2">
                                    <!-- Name -->
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-emerald-600 transition-colors duration-200 truncate">
                                        {{ $agent->name }}
                                    </h3>

                                    <!-- Agency/Type -->
                                    <p class="text-sm text-gray-600">
                                        @if($agent->agentProfile && $agent->agentProfile->agency)
                                            <span class="text-blue-600 font-medium">{{ $agent->agentProfile->agency->name }}</span>
                                        @else
                                            <span class="text-emerald-600 font-medium">Independent Agent</span>
                                        @endif
                                    </p>

                                    <!-- Rating & Stats -->
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <div class="flex items-center space-x-1">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= ($agent->agentProfile->average_rating ?? 0) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="font-medium">{{ number_format($agent->agentProfile->average_rating ?? 0, 1) }}</span>
                                        </div>
                                        <span>{{ $agent->agentProfile->years_experience ?? 0 }}y exp</span>
                                    </div>

                                    <!-- Experience & Listings -->
                                    <div class="flex items-center justify-between text-xs text-gray-500 pt-2">
                                        <span class="flex items-center space-x-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            <span>{{ $agent->agentProfile->properties_count ?? 0 }} listings</span>
                                        </span>
                                        <div class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-md text-xs font-medium">
                                            View Profile
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Custom Pagination -->
                <div class="mt-12 flex justify-center">
                    @if($agents->hasPages())
                        <nav class="flex items-center space-x-2" role="navigation" aria-label="Pagination Navigation">
                            {{-- Previous Page Link --}}
                            @if ($agents->onFirstPage())
                                <span class="flex items-center justify-center w-10 h-10 text-gray-400 bg-gray-100/80 border border-gray-200/50 rounded-xl cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $agents->previousPageUrl() }}"
                                   class="flex items-center justify-center w-10 h-10 text-gray-700 bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-xl transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </a>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($agents->getUrlRange(1, $agents->lastPage()) as $page => $url)
                                @if ($page == $agents->currentPage())
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
                            @if ($agents->hasMorePages())
                                <a href="{{ $agents->nextPageUrl() }}"
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No agents found</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your search criteria or browse all available agents.</p>
                    <button
                        wire:click="clearAllFilters"
                        class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors duration-200"
                    >
                        Browse All Agents
                    </button>
                </div>
            @endif
        </div>
    </section>
</div>