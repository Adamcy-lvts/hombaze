<div class="min-h-screen bg-linear-to-br from-gray-50 via-white to-gray-100 relative overflow-hidden">
    <!-- Premium Background Elements -->
    <div class="absolute inset-0">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-linear-to-br from-blue-500/10 to-indigo-500/5 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-linear-to-br from-emerald-500/8 to-teal-500/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 right-1/4 w-64 h-64 bg-linear-to-br from-purple-500/5 to-pink-500/3 rounded-full blur-2xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- Premium Header Section -->
    <div class="relative z-10 bg-white/80 backdrop-blur-2xl border-b border-gray-200/60 shadow-2xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <!-- Premium Breadcrumb -->
            <nav class="flex items-center space-x-3 text-sm text-gray-600 mb-8">
                <a href="{{ route('landing') }}" wire:navigate class="group flex items-center space-x-2 hover:text-emerald-600 transition-colors duration-300">
                    <div class="w-8 h-8 bg-gray-100/60 backdrop-blur-xs rounded-lg flex items-center justify-center group-hover:bg-emerald-100/60 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <span class="font-medium">Home</span>
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="font-bold text-gray-900">Find Agents</span>
            </nav>

            <!-- Premium Page Header -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-black text-gray-900 mb-3">
                        Find Your Perfect
                        <span class="bg-linear-to-r from-blue-600 via-indigo-500 to-purple-600 bg-clip-text text-transparent">Real Estate Agent</span>
                    </h1>
                    <p class="text-gray-600 text-lg leading-relaxed">Connect with verified, experienced agents across Nigeria</p>
                </div>
                
                <!-- Stats Display -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white/60 backdrop-blur-xl border border-gray-200/60 rounded-2xl p-4 text-center shadow-xl">
                        <div class="text-2xl font-black text-blue-600">{{ number_format($stats['total_agents']) }}</div>
                        <div class="text-sm text-gray-600 font-medium">Active Agents</div>
                    </div>
                    <div class="bg-white/60 backdrop-blur-xl border border-gray-200/60 rounded-2xl p-4 text-center shadow-xl">
                        <div class="text-2xl font-black text-emerald-600">{{ number_format($stats['verified_agents']) }}</div>
                        <div class="text-sm text-gray-600 font-medium">Verified</div>
                    </div>
                    <div class="bg-white/60 backdrop-blur-xl border border-gray-200/60 rounded-2xl p-4 text-center shadow-xl">
                        <div class="text-2xl font-black text-purple-600">{{ number_format($stats['avg_experience'], 1) }}</div>
                        <div class="text-sm text-gray-600 font-medium">Avg Experience</div>
                    </div>
                    <div class="bg-white/60 backdrop-blur-xl border border-gray-200/60 rounded-2xl p-4 text-center shadow-xl">
                        <div class="text-2xl font-black text-orange-600">{{ number_format($stats['total_properties']) }}</div>
                        <div class="text-sm text-gray-600 font-medium">Properties Listed</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="relative z-100 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <!-- Premium Search Bar -->
        <div class="relative mb-8">
            <div class="relative group">
                <input 
                    type="text" 
                    wire:model.live.debounce.500ms="searchQuery"
                    placeholder="Search agents by name, specialization, or location..."
                    class="w-full pl-16 pr-20 py-6 text-lg bg-white/90 backdrop-blur-2xl border border-gray-200/60 rounded-3xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 text-gray-900 placeholder-gray-400 shadow-2xl transition-all duration-500 hover:bg-white/95 focus:bg-white/95"
                    autocomplete="off"
                >
                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                    <div class="w-8 h-8 bg-blue-100/60 backdrop-blur-xs rounded-xl flex items-center justify-center">
                        <svg wire:loading.remove wire:target="searchQuery" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <svg wire:loading wire:target="searchQuery" class="animate-spin w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Sort Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 mb-8">
            <button 
                wire:click="toggleFilters"
                class="group inline-flex items-center px-6 py-4 bg-white/80 backdrop-blur-xl border border-gray-200/60 rounded-2xl text-gray-900 font-semibold hover:bg-white/90 hover:border-gray-300/60 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105"
            >
                <div class="w-8 h-8 bg-purple-100/60 rounded-xl flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                    </svg>
                </div>
                <span>Advanced Filters</span>
            </button>

            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-3 text-gray-900 font-semibold">
                    <div class="w-8 h-8 bg-blue-100/60 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </div>
                    <span>Sort by:</span>
                </div>
                <select 
                    wire:model.live="sortBy"
                    class="bg-white/90 backdrop-blur-xl border border-gray-200/60 rounded-xl px-4 py-3 text-gray-900 font-medium focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 hover:bg-white/95 transition-all duration-300 shadow-xl"
                >
                    <option value="rating">Highest Rated</option>
                    <option value="experience">Most Experienced</option>
                    <option value="properties">Most Properties</option>
                    <option value="newest">Newest Agents</option>
                </select>
            </div>
        </div>

        <!-- Filters Panel -->
        @if($showFilters)
            <div class="bg-white/60 backdrop-blur-2xl border border-gray-200/40 rounded-3xl p-8 mb-8 shadow-2xl">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Location Filter -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-4">Location</label>
                        <select wire:model.live="selectedLocation" class="w-full bg-white/80 border border-gray-200/60 rounded-xl px-4 py-3 text-gray-900">
                            <option value="">All Locations</option>
                            @foreach($locationOptions as $state)
                                <option value="{{ $state->id }}">{{ $state->name }} ({{ $state->agents_count }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Experience Filter -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-4">Experience Level</label>
                        <select wire:model.live="experienceFilter" class="w-full bg-white/80 border border-gray-200/60 rounded-xl px-4 py-3 text-gray-900">
                            <option value="">All Experience Levels</option>
                            <option value="junior">Junior (0-2 years)</option>
                            <option value="mid">Mid-Level (3-7 years)</option>
                            <option value="senior">Senior (8+ years)</option>
                        </select>
                    </div>

                    <!-- Specialization Filter -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-4">Specialization</label>
                        <select wire:model.live="specializationFilter" class="w-full bg-white/80 border border-gray-200/60 rounded-xl px-4 py-3 text-gray-900">
                            <option value="">All Specializations</option>
                            <option value="residential">Residential</option>
                            <option value="commercial">Commercial</option>
                            <option value="luxury">Luxury Properties</option>
                            <option value="investment">Investment Properties</option>
                            <option value="land">Land & Development</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button 
                        wire:click="clearFilters"
                        class="inline-flex items-center space-x-2 text-sm text-gray-600 hover:text-gray-900 font-semibold px-4 py-2 rounded-xl hover:bg-gray-100/60 transition-all duration-300"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Clear All Filters</span>
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Results Section -->
    <div class="relative z-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        @if($agents->count() > 0)
            <!-- Agents Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @foreach($agents as $agent)
                    <a href="{{ route('agent.profile', $agent) }}" wire:navigate class="group block relative bg-white/80 backdrop-blur-2xl rounded-3xl border border-gray-200/40 overflow-hidden hover:bg-white/90 hover:border-gray-300/40 transition-all duration-700 transform hover:scale-[1.02] hover:-translate-y-2 shadow-2xl hover:shadow-3xl">
                        
                        <!-- Agent Profile Header -->
                        <div class="relative h-64 bg-linear-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                            @if($agent->avatar)
                                <img src="{{ Storage::url($agent->avatar) }}" 
                                     alt="{{ $agent->name }}"
                                     class="w-32 h-32 rounded-full border-4 border-white/20 shadow-2xl object-cover">
                            @else
                                <div class="w-32 h-32 bg-white/20 backdrop-blur-xs rounded-full flex items-center justify-center border-4 border-white/20">
                                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif

                            <!-- Verification Badge -->
                            @if($agent->agentProfile->is_verified)
                                <div class="absolute top-4 right-4">
                                    <div class="inline-flex items-center space-x-2 bg-emerald-500/20 backdrop-blur-xl border border-emerald-400/30 text-emerald-200 px-3 py-1.5 rounded-xl font-bold text-xs shadow-xl">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span>Verified</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Agent Details -->
                        <div class="p-6">
                            <div class="mb-4">
                                <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $agent->name }}</h3>
                                <p class="text-blue-600 font-semibold text-sm">{{ $agent->agentProfile->agency->name ?? 'Independent Agent' }}</p>
                            </div>

                            <!-- Rating and Experience -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $agent->agentProfile->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                    <span class="text-sm text-gray-600 ml-2">({{ $agent->agentProfile->total_reviews }})</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">{{ $agent->agentProfile->years_experience }} years exp.</span>
                            </div>

                            <!-- Bio -->
                            @if($agent->agentProfile->bio)
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $agent->agentProfile->bio }}</p>
                            @endif

                            <!-- View Profile Button -->
                            <div class="w-full bg-linear-to-r from-blue-500 to-indigo-500 group-hover:from-blue-600 group-hover:to-indigo-600 text-white font-bold py-3 px-4 rounded-xl transition-all duration-300 transform group-hover:scale-105 shadow-lg group-hover:shadow-xl text-center">
                                View Profile & Reviews
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $agents->links() }}
            </div>
        @else
            <!-- No Results -->
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No agents found</h3>
                <p class="text-gray-600 mb-4">Try adjusting your search criteria or clearing some filters.</p>
                <button 
                    wire:click="clearFilters"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors"
                >
                    Clear All Filters
                </button>
            </div>
        @endif
    </div>

    <style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    </style>
</div>