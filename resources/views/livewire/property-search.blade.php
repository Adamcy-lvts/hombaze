<div class="min-h-screen {{ $isDarkMode ? 'bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 theme-dark' : 'bg-gradient-to-br from-gray-50 via-white to-gray-100 theme-light' }} relative overflow-hidden transition-all duration-700">
    <!-- Premium Background Elements -->
    <div class="absolute inset-0">
        <!-- Dynamic Gradient Mesh -->
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br {{ $isDarkMode ? 'from-emerald-500/20 to-teal-500/10' : 'from-emerald-500/10 to-teal-500/5' }} rounded-full blur-3xl animate-pulse transition-all duration-700"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-br {{ $isDarkMode ? 'from-blue-500/15 to-indigo-500/10' : 'from-blue-500/8 to-indigo-500/5' }} rounded-full blur-3xl animate-pulse transition-all duration-700" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 right-1/4 w-64 h-64 bg-gradient-to-br {{ $isDarkMode ? 'from-purple-500/10 to-pink-500/5' : 'from-purple-500/5 to-pink-500/3' }} rounded-full blur-2xl animate-pulse transition-all duration-700" style="animation-delay: 2s;"></div>
        
        <!-- Floating Particles -->
        <div class="floating-particles">
            <div class="particle absolute top-20 left-10 w-1.5 h-1.5 {{ $isDarkMode ? 'bg-emerald-400/40' : 'bg-emerald-400/20' }} rounded-full transition-all duration-700"></div>
            <div class="particle absolute top-40 right-16 w-1 h-1 {{ $isDarkMode ? 'bg-blue-400/30' : 'bg-blue-400/15' }} rounded-full transition-all duration-700"></div>
            <div class="particle absolute bottom-32 left-20 w-2 h-2 {{ $isDarkMode ? 'bg-purple-400/50' : 'bg-purple-400/25' }} rounded-full transition-all duration-700"></div>
            <div class="particle absolute bottom-20 right-10 w-1.5 h-1.5 {{ $isDarkMode ? 'bg-teal-400/40' : 'bg-teal-400/20' }} rounded-full transition-all duration-700"></div>
        </div>
    </div>
    <!-- Premium Header Section -->
    <div class="relative z-10 {{ $isDarkMode ? 'bg-white/5 border-white/10' : 'bg-white/80 border-gray-200/60' }} backdrop-blur-2xl border-b shadow-2xl transition-all duration-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
            <!-- Premium Breadcrumb -->
            <nav class="flex items-center space-x-3 text-sm {{ $isDarkMode ? 'text-slate-300' : 'text-gray-600' }} mb-8 transition-colors duration-700">
                <a href="{{ route('landing') }}" wire:navigate class="group flex items-center space-x-2 {{ $isDarkMode ? 'hover:text-emerald-400' : 'hover:text-emerald-600' }} transition-colors duration-300">
                    <div class="w-8 h-8 {{ $isDarkMode ? 'bg-white/10 group-hover:bg-emerald-500/20' : 'bg-gray-100/60 group-hover:bg-emerald-100/60' }} backdrop-blur-sm rounded-lg flex items-center justify-center transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <span class="font-medium">Home</span>
                </a>
                <svg class="w-4 h-4 {{ $isDarkMode ? 'text-slate-500' : 'text-gray-400' }} transition-colors duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="font-bold {{ $isDarkMode ? 'text-white' : 'text-gray-900' }} transition-colors duration-700">Property Search</span>
            </nav>

            <!-- Premium Search Header -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-black {{ $isDarkMode ? 'text-white' : 'text-gray-900' }} mb-3 transition-colors duration-700">
                        Find Your
                        <span class="bg-gradient-to-r from-emerald-600 via-teal-500 to-blue-600 bg-clip-text text-transparent">Perfect</span>
                        Property
                    </h1>
                    <p class="{{ $isDarkMode ? 'text-slate-300' : 'text-gray-600' }} text-lg leading-relaxed transition-colors duration-700">Discover premium properties across Nigeria's most desirable locations</p>
                </div>
                
                <!-- Premium Controls: Results Count + Theme Toggle -->
                <div class="flex items-center space-x-4">
                    <!-- Premium Results Count -->
                    <div class="inline-flex items-center space-x-3 {{ $isDarkMode ? 'bg-white/10 border-white/20 text-white' : 'bg-white/60 border-gray-200/60 text-gray-900' }} backdrop-blur-xl border px-6 py-3 rounded-2xl shadow-xl transition-all duration-700">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></div>
                        <span class="font-bold text-lg">{{ $properties->total() }}</span>
                        <span class="{{ $isDarkMode ? 'text-slate-300' : 'text-gray-600' }} transition-colors duration-700">properties found</span>
                    </div>

                    <!-- Premium Theme Toggle Button -->
                    <button 
                        wire:click="toggleTheme"
                        class="group relative inline-flex items-center justify-center w-14 h-14 {{ $isDarkMode ? 'bg-white/10 border-white/20 hover:bg-white/20' : 'bg-white/80 border-gray-200/60 hover:bg-white/95' }} backdrop-blur-xl border rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-700 hover:scale-110"
                        title="{{ $isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode' }}"
                    >
                        <!-- Sun Icon (Light Mode) -->
                        <div class="absolute inset-0 flex items-center justify-center transition-all duration-700 {{ $isDarkMode ? 'opacity-0 rotate-180 scale-50' : 'opacity-100 rotate-0 scale-100' }}">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        
                        <!-- Moon Icon (Dark Mode) -->
                        <div class="absolute inset-0 flex items-center justify-center transition-all duration-700 {{ $isDarkMode ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 -rotate-180 scale-50' }}">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </div>
                        
                        <!-- Animated Background Effect -->
                        <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-amber-400/20 via-orange-400/20 to-rose-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-700 {{ $isDarkMode ? 'hidden' : 'block' }}"></div>
                        <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-400/20 via-indigo-400/20 to-purple-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-700 {{ $isDarkMode ? 'block' : 'hidden' }}"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Search & Filter Section -->
    <div class="relative z-[100] max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <!-- Premium Main Search Bar -->
        <div class="relative mb-8 z-[9999]">
            <div class="relative group">
                <input 
                    type="text" 
                    wire:model.live.debounce.500ms="searchQuery"
                    wire:focus="updateSuggestions"
                    placeholder="Search by location, property type, or features..."
                    class="w-full pl-16 pr-20 py-6 text-lg {{ $isDarkMode ? 'bg-white/10 border-white/20 text-white placeholder-slate-300 hover:bg-white/15 focus:bg-white/15' : 'bg-white/90 border-gray-200/60 text-gray-900 placeholder-gray-400 hover:bg-white/95 focus:bg-white/95' }} backdrop-blur-2xl rounded-3xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 shadow-2xl transition-all duration-700"
                    autocomplete="off"
                >
                <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                    <div class="w-8 h-8 {{ $isDarkMode ? 'bg-emerald-500/20' : 'bg-emerald-100/60' }} backdrop-blur-sm rounded-xl flex items-center justify-center transition-all duration-700">
                        <!-- Search Icon (default state) -->
                        <svg wire:loading.remove wire:target="searchQuery" class="w-5 h-5 {{ $isDarkMode ? 'text-emerald-400' : 'text-emerald-600' }} transition-all duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <!-- Loading Spinner (loading state) -->
                        <svg wire:loading wire:target="searchQuery" class="animate-spin w-5 h-5 {{ $isDarkMode ? 'text-emerald-400' : 'text-emerald-600' }} transition-colors duration-700" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Premium Search Suggestions -->
            @if($showSuggestions && count($suggestions) > 0)
                <div class="absolute w-full mt-4 {{ $isDarkMode ? 'bg-slate-800/95 border-emerald-400/30' : 'bg-white/95 border-gray-200/60' }} backdrop-blur-2xl border rounded-2xl shadow-2xl max-h-96 overflow-y-auto transition-all duration-700" 
                     style="z-index: 999999 !important;">
                    @foreach($suggestions as $suggestion)
                        <div 
                            wire:click="selectSuggestion({{ json_encode($suggestion) }})"
                            class="group flex items-center px-6 py-4 {{ $isDarkMode ? 'hover:bg-emerald-500/20 border-slate-700/50' : 'hover:bg-emerald-50/60 border-gray-200/50' }} cursor-pointer transition-all duration-300 border-b last:border-b-0"
                        >
                            <div class="flex-shrink-0 w-12 h-12 {{ $isDarkMode ? 'bg-emerald-500/20 border-emerald-400/30' : 'bg-emerald-100/60 border-emerald-200/60' }} backdrop-blur-sm rounded-2xl flex items-center justify-center group-hover:scale-110 transition-all duration-700 border">
                                @if($suggestion['icon'] === 'location-dot')
                                    <svg class="w-6 h-6 {{ $isDarkMode ? 'text-emerald-400' : 'text-emerald-600' }} transition-colors duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 {{ $isDarkMode ? 'text-emerald-400' : 'text-emerald-600' }} transition-colors duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="font-bold {{ $isDarkMode ? 'text-white group-hover:text-emerald-300' : 'text-gray-900 group-hover:text-emerald-700' }} transition-colors duration-700">{{ $suggestion['text'] }}</div>
                                <div class="text-sm {{ $isDarkMode ? 'text-slate-400 group-hover:text-slate-300' : 'text-gray-500 group-hover:text-gray-600' }} transition-colors duration-700">{{ $suggestion['category'] }}</div>
                            </div>
                            <svg class="w-5 h-5 {{ $isDarkMode ? 'text-slate-500 group-hover:text-emerald-400' : 'text-gray-400 group-hover:text-emerald-600' }} group-hover:translate-x-1 transition-all duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Premium Active Filters -->
        @if(count($activeFilters) > 0)
            <div class="flex flex-wrap items-center gap-3 mb-8 p-6 {{ $isDarkMode ? 'bg-white/5 border-white/10' : 'bg-white/60 border-gray-200/40' }} backdrop-blur-xl border rounded-2xl transition-all duration-700">
                <span class="text-sm font-bold {{ $isDarkMode ? 'text-white' : 'text-gray-900' }} flex items-center space-x-2 transition-colors duration-700">
                    <div class="w-5 h-5 {{ $isDarkMode ? 'bg-emerald-500/20' : 'bg-emerald-100/60' }} rounded-lg flex items-center justify-center transition-all duration-700">
                        <svg class="w-3 h-3 {{ $isDarkMode ? 'text-emerald-400' : 'text-emerald-600' }} transition-colors duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                        </svg>
                    </div>
                    <span>Active Filters:</span>
                </span>
                @foreach($activeFilters as $key => $filter)
                    <span class="group inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold {{ $isDarkMode ? 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30 hover:bg-emerald-500/30' : 'bg-emerald-100/60 text-emerald-700 border-emerald-200/60 hover:bg-emerald-200/60' }} border backdrop-blur-sm transition-all duration-700">
                        {{ $filter['label'] }}
                        <button 
                            wire:click="removeFilter('{{ $key }}')"
                            class="ml-2 inline-flex items-center justify-center w-5 h-5 rounded-full {{ $isDarkMode ? 'text-emerald-300 hover:bg-emerald-400/20 hover:text-white' : 'text-emerald-600 hover:bg-emerald-300/60 hover:text-emerald-800' }} transition-all duration-700 group-hover:scale-110"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </span>
                @endforeach
                <button 
                    wire:click="clearAllFilters"
                    class="group inline-flex items-center space-x-2 text-sm {{ $isDarkMode ? 'text-slate-300 hover:text-white hover:bg-white/10' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100/60' }} font-semibold px-4 py-2 rounded-xl transition-all duration-700"
                >
                    <!-- Clear Icon (default) -->
                    <svg wire:loading.remove wire:target="clearAllFilters" class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <!-- Loading Spinner -->
                    <svg wire:loading wire:target="clearAllFilters" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Clear All</span>
                </button>
            </div>
        @endif

        <!-- Premium Filter & Sort Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 mb-8">
            <!-- Premium Filter Toggle -->
            <button 
                wire:click="toggleFilters"
                class="group inline-flex items-center px-6 py-4 {{ $isDarkMode ? 'bg-white/10 border-white/20 text-white hover:bg-white/20 hover:border-white/30' : 'bg-white/80 border-gray-200/60 text-gray-900 hover:bg-white/90 hover:border-gray-300/60' }} backdrop-blur-xl border rounded-2xl font-semibold transition-all duration-700 shadow-xl hover:shadow-2xl hover:scale-105"
            >
                <div class="w-8 h-8 {{ $isDarkMode ? 'bg-blue-500/20' : 'bg-blue-100/60' }} rounded-xl flex items-center justify-center mr-3 group-hover:scale-110 transition-all duration-700">
                    <!-- Filter Icon (default) -->
                    <svg wire:loading.remove wire:target="toggleFilters" class="w-4 h-4 {{ $isDarkMode ? 'text-blue-400' : 'text-blue-600' }} transition-colors duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                    </svg>
                    <!-- Loading Spinner -->
                    <svg wire:loading wire:target="toggleFilters" class="animate-spin w-4 h-4 {{ $isDarkMode ? 'text-blue-400' : 'text-blue-600' }} transition-colors duration-700" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <span>Advanced Filters</span>
                @if(count($activeFilters) > 0)
                    <span class="ml-3 {{ $isDarkMode ? 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30' : 'bg-emerald-100/60 text-emerald-700 border-emerald-200/60' }} text-xs font-bold px-3 py-1 rounded-full border transition-all duration-700">
                        {{ count($activeFilters) }}
                    </span>
                @endif
                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Premium Sort Options -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-3 {{ $isDarkMode ? 'text-white' : 'text-gray-900' }} font-semibold transition-colors duration-700">
                    <div class="w-8 h-8 {{ $isDarkMode ? 'bg-purple-500/20' : 'bg-purple-100/60' }} rounded-xl flex items-center justify-center transition-all duration-700">
                        <!-- Sort Icon (default) -->
                        <svg wire:loading.remove wire:target="sortBy" class="w-4 h-4 {{ $isDarkMode ? 'text-purple-400' : 'text-purple-600' }} transition-colors duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                        <!-- Loading Spinner -->
                        <svg wire:loading wire:target="sortBy" class="animate-spin w-4 h-4 {{ $isDarkMode ? 'text-purple-400' : 'text-purple-600' }} transition-colors duration-700" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <span>Sort by:</span>
                </div>
                <div class="relative">
                    <select 
                        wire:model.live="sortBy"
                        class="{{ $isDarkMode ? 'bg-white/10 border-white/20 text-white hover:bg-white/20' : 'bg-white/90 border-gray-200/60 text-gray-900 hover:bg-white/95' }} backdrop-blur-xl border rounded-xl px-4 py-3 font-medium focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all duration-700 shadow-xl appearance-none bg-no-repeat bg-right-4 bg-center pr-10"
                        style="background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; fill=&quot;{{ $isDarkMode ? 'white' : 'black' }}&quot; viewBox=&quot;0 0 20 20&quot;><path d=&quot;M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z&quot;/></svg>'); background-size: 16px;"
                    >
                        <option value="relevance" style="background-color: {{ $isDarkMode ? '#1e293b' : '#ffffff' }}; color: {{ $isDarkMode ? 'white' : '#1f2937' }};">Relevance</option>
                        <option value="price_low" style="background-color: {{ $isDarkMode ? '#1e293b' : '#ffffff' }}; color: {{ $isDarkMode ? 'white' : '#1f2937' }};">Price: Low to High</option>
                        <option value="price_high" style="background-color: {{ $isDarkMode ? '#1e293b' : '#ffffff' }}; color: {{ $isDarkMode ? 'white' : '#1f2937' }};">Price: High to Low</option>
                        <option value="newest" style="background-color: {{ $isDarkMode ? '#1e293b' : '#ffffff' }}; color: {{ $isDarkMode ? 'white' : '#1f2937' }};">Newest First</option>
                        <option value="popular" style="background-color: {{ $isDarkMode ? '#1e293b' : '#ffffff' }}; color: {{ $isDarkMode ? 'white' : '#1f2937' }};">Most Popular</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Premium Filters Panel -->
        @if($showFilters)
            <div class="bg-white/60 backdrop-blur-2xl border border-gray-200/40 rounded-3xl p-8 mb-8 shadow-2xl">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <!-- Premium Listing Type -->
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-6 h-6 bg-emerald-100/60 rounded-lg flex items-center justify-center">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <label class="block text-sm font-bold text-gray-900">Listing Type</label>
                        </div>
                        <div class="space-y-3">
                            @foreach($filterOptions['listing_type'] as $type)
                                <button 
                                    wire:click="addFilter('listing_type', '{{ $type }}')"
                                    class="group w-full text-left px-4 py-3 text-sm {{ $isDarkMode ? 'bg-white/5 border-white/20 hover:bg-white/10 hover:border-emerald-400/50 text-white' : 'bg-white/60 border-gray-200/40 hover:bg-white/80 hover:border-emerald-500/50 text-gray-900' }} border rounded-xl transition-all duration-700 font-medium {{ collect($activeFilters)->contains('type', 'listing_type') && collect($activeFilters)->contains('value', $type) ? ($isDarkMode ? 'bg-emerald-500/20 border-emerald-400/50 text-emerald-300' : 'bg-emerald-100/60 border-emerald-500/50 text-emerald-700') : '' }}"
                                >
                                    <div class="flex items-center justify-between">
                                        <span>{{ ucfirst($type) }}</span>
                                        @if(collect($activeFilters)->contains('type', 'listing_type') && collect($activeFilters)->contains('value', $type))
                                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-900 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Premium Property Type -->
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-6 h-6 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                            </div>
                            <label class="block text-sm font-bold text-white">Property Type</label>
                        </div>
                        <div class="space-y-3 max-h-48 overflow-y-auto custom-scrollbar">
                            @foreach($filterOptions['property_type'] as $id => $name)
                                <button 
                                    wire:click="addFilter('property_type', '{{ $id }}')"
                                    class="group w-full text-left px-4 py-3 text-sm bg-white/60 border border-gray-200/40 rounded-xl hover:bg-white/80 hover:border-blue-500/50 transition-all duration-300 text-gray-900 font-medium {{ collect($activeFilters)->contains('type', 'property_type') && collect($activeFilters)->contains('value', $id) ? 'bg-blue-100/60 border-blue-500/50 text-blue-700' : '' }}"
                                >
                                    <div class="flex items-center justify-between">
                                        <span>{{ $name }}</span>
                                        @if(collect($activeFilters)->contains('type', 'property_type') && collect($activeFilters)->contains('value', $id))
                                            <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-900 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Premium Bedrooms -->
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-6 h-6 bg-purple-100/60 rounded-lg flex items-center justify-center">
                                <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                </svg>
                            </div>
                            <label class="block text-sm font-bold text-white">Bedrooms</label>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach($filterOptions['bedrooms'] as $beds)
                                <button 
                                    wire:click="addFilter('bedrooms', '{{ $beds }}')"
                                    class="group px-3 py-3 text-sm bg-white/60 border border-gray-200/40 rounded-xl hover:bg-white/80 hover:border-purple-500/50 transition-all duration-300 text-center text-gray-900 font-semibold {{ collect($activeFilters)->contains('type', 'bedrooms') && collect($activeFilters)->contains('value', $beds) ? 'bg-purple-100/60 border-purple-500/50 text-purple-700' : '' }}"
                                >
                                    {{ $beds }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Premium Price Range -->
                    <div>
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-6 h-6 bg-amber-100/60 rounded-lg flex items-center justify-center">
                                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <label class="block text-sm font-bold text-white">Price Range</label>
                        </div>
                        <div class="space-y-3">
                            @foreach($filterOptions['price_range'] as $range)
                                <button 
                                    wire:click="addFilter('price_range', '{{ $range }}')"
                                    class="group w-full text-left px-4 py-3 text-sm bg-white/60 border border-gray-200/40 rounded-xl hover:bg-white/80 hover:border-amber-500/50 transition-all duration-300 text-gray-900 font-medium {{ collect($activeFilters)->contains('type', 'price_range') && collect($activeFilters)->contains('value', $range) ? 'bg-amber-100/60 border-amber-500/50 text-amber-700' : '' }}"
                                >
                                    <div class="flex items-center justify-between">
                                        <span>
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
                                        @if(collect($activeFilters)->contains('type', 'price_range') && collect($activeFilters)->contains('value', $range))
                                            <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-900 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Premium Results Section -->
    <div class="relative z-[1] max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        @if($properties->count() > 0)
            <!-- Premium Property Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @foreach($properties as $property)
                    <a href="{{ route('property.show', $property->slug ?? $property->id) }}" 
                       class="group block relative bg-white/80 backdrop-blur-2xl rounded-3xl border border-gray-200/40 overflow-hidden hover:bg-white/90 hover:border-gray-300/40 transition-all duration-700 transform hover:scale-[1.02] hover:-translate-y-2 shadow-2xl hover:shadow-3xl">
                        
                        <!-- Premium Property Image -->
                        <div class="relative h-64 lg:h-72 overflow-hidden">
                            @if($property->getMedia('featured')->count() > 0)
                                <img src="{{ $property->getFirstMedia('featured')->getUrl() }}" 
                                     alt="{{ $property->title }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @elseif($property->getMedia('gallery')->count() > 0)
                                <img src="{{ $property->getMedia('gallery')->first()->getUrl() }}" 
                                     alt="{{ $property->title }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                    <div class="w-20 h-20 bg-white/40 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Premium Overlay Gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-60 group-hover:opacity-80 transition-opacity duration-500"></div>
                            
                            <!-- Premium Verified Badge -->
                            @if($property->is_verified)
                                <div class="absolute top-4 left-4">
                                    <div class="inline-flex items-center space-x-2 bg-blue-100/60 backdrop-blur-xl border border-blue-300/40 text-blue-700 px-3 py-1.5 rounded-xl font-bold text-xs shadow-xl">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span>Verified</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Premium Listing Type Badge -->
                            <div class="absolute top-4 right-4">
                                @if($property->listing_type === 'rent')
                                    <div class="inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-emerald-100/60 to-green-100/60 backdrop-blur-xl border border-emerald-300/40 text-emerald-700 text-xs font-bold rounded-xl shadow-2xl">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                        </svg>
                                        <span>For Rent</span>
                                    </div>
                                @elseif($property->listing_type === 'sale')
                                    <div class="inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-blue-100/60 to-indigo-100/60 backdrop-blur-xl border border-blue-300/40 text-blue-700 text-xs font-bold rounded-xl shadow-2xl">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                        <span>For Sale</span>
                                    </div>
                                @else
                                    <div class="inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-purple-100/60 to-pink-100/60 backdrop-blur-xl border border-purple-300/40 text-purple-700 text-xs font-bold rounded-xl shadow-2xl capitalize">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span>{{ $property->listing_type }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Compact Property Details -->
                        <div class="p-4 bg-gradient-to-br from-white/60 via-white/80 to-white/60 backdrop-blur-sm">
                            
                            <!-- Compact Header: Title + Location -->
                            <div class="mb-3">
                                <h4 class="font-bold text-gray-900 text-base mb-1.5 line-clamp-1 group-hover:text-emerald-600 transition-colors duration-300">
                                    {{ $property->title }}
                                </h4>
                                <div class="flex items-center text-xs font-medium text-gray-600">
                                    <svg class="w-3 h-3 mr-1.5 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="truncate">{{ $property->city->name ?? 'Unknown' }}, {{ $property->state->name ?? 'Unknown' }}</span>
                                </div>
                            </div>

                            <!-- Compact Features Row - Only show if there's actual data -->
                            @if($property->bedrooms || $property->bathrooms || $property->floor_area)
                            <div class="flex items-center space-x-3 mb-3">
                                @if($property->bedrooms)
                                <div class="flex items-center space-x-1.5 text-xs font-semibold text-gray-900">
                                    <div class="w-5 h-5 bg-emerald-100/60 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    </div>
                                    <span>{{ $property->bedrooms }} bed</span>
                                </div>
                                @endif

                                @if($property->bedrooms && $property->bathrooms)
                                <div class="w-px h-4 bg-gray-400"></div>
                                @endif

                                @if($property->bathrooms)
                                <div class="flex items-center space-x-1.5 text-xs font-semibold text-gray-900">
                                    <div class="w-5 h-5 bg-blue-100/60 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                        </svg>
                                    </div>
                                    <span>{{ $property->bathrooms }} bath</span>
                                </div>
                                @endif

                                @if($property->floor_area && ($property->bedrooms || $property->bathrooms))
                                <div class="w-px h-4 bg-gray-400"></div>
                                @endif

                                @if($property->floor_area)
                                <div class="flex items-center space-x-1.5 text-xs font-semibold text-gray-600">
                                    <span>{{ number_format($property->floor_area) }}m²</span>
                                </div>
                                @endif
                            </div>
                            @endif

                            <!-- Compact Price + Action Row -->
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="text-lg font-black bg-gradient-to-r from-emerald-600 via-teal-500 to-blue-600 bg-clip-text text-transparent">
                                        ₦{{ number_format($property->price) }}
                                    </div>
                                    @if($property->listing_type === 'rent')
                                        <div class="text-xs font-medium text-gray-500">/year</div>
                                    @endif
                                </div>
                                
                                <!-- Compact Action Buttons -->
                                <div class="flex items-center space-x-2">
                                    <button
                                        wire:click.stop="toggleSaveProperty({{ $property->id }})"
                                        wire:target="toggleSaveProperty({{ $property->id }})"
                                        onclick="event.stopPropagation(); event.preventDefault();"
                                        class="group/heart p-2.5 {{ $this->isPropertySaved($property->id) ? 'bg-red-50 border-red-300 hover:bg-red-100' : 'bg-white/60 hover:bg-white/80 border-gray-200/40 hover:border-emerald-500/50' }} backdrop-blur-sm rounded-lg border transition-all duration-300 hover:scale-110"
                                        title="{{ $this->isPropertySaved($property->id) ? 'Remove from saved' : 'Save property' }}"
                                    >
                                        <!-- Loading spinner -->
                                        <svg wire:loading wire:target="toggleSaveProperty({{ $property->id }})" class="animate-spin w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>

                                        <div wire:loading.remove wire:target="toggleSaveProperty({{ $property->id }})">
                                            @if($this->isPropertySaved($property->id))
                                                <!-- Filled heart for saved properties -->
                                                <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                </svg>
                                            @else
                                                <!-- Outline heart for unsaved properties -->
                                                <svg class="w-3.5 h-3.5 text-gray-500 group-hover/heart:text-emerald-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </button>
                                    <button class="group/btn bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-xs font-bold py-2.5 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                        <span class="flex items-center space-x-1.5">
                                            <span>View</span>
                                            <svg class="w-3 h-3 group-hover/btn:translate-x-0.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $properties->links() }}
            </div>
        @else
            <!-- No Results -->
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No properties found</h3>
                <p class="text-gray-600 mb-4">Try adjusting your search criteria or clearing some filters.</p>
                @if(count($activeFilters) > 0)
                    <button 
                        wire:click="clearAllFilters"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 transition-colors"
                    >
                        Clear All Filters
                    </button>
                @endif
            </div>
        @endif

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Premium Custom Styles -->
    <style>
    /* Theme-aware CSS Variables */
    .theme-light {
        --bg-primary: rgba(249, 250, 251, 1);
        --bg-secondary: rgba(255, 255, 255, 0.9);
        --bg-tertiary: rgba(255, 255, 255, 0.6);
        --text-primary: rgb(17, 24, 39);
        --text-secondary: rgb(75, 85, 99);
        --text-muted: rgb(107, 114, 128);
        --border-primary: rgba(229, 231, 235, 0.6);
        --border-secondary: rgba(229, 231, 235, 0.4);
        --shadow-color: rgba(0, 0, 0, 0.1);
    }
    
    .theme-dark {
        --bg-primary: rgba(15, 23, 42, 1);
        --bg-secondary: rgba(255, 255, 255, 0.1);
        --bg-tertiary: rgba(255, 255, 255, 0.05);
        --text-primary: rgb(255, 255, 255);
        --text-secondary: rgb(203, 213, 225);
        --text-muted: rgb(148, 163, 184);
        --border-primary: rgba(255, 255, 255, 0.2);
        --border-secondary: rgba(255, 255, 255, 0.1);
        --shadow-color: rgba(0, 0, 0, 0.3);
    }
    
    /* Universal Theme Transitions */
    * {
        transition: background-color 0.7s ease, color 0.7s ease, border-color 0.7s ease, opacity 0.7s ease;
    }
    
    /* Smooth Theme Toggle Animation */
    .theme-transition {
        transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
    }
    /* Floating Particles Animation */
    .floating-particles .particle {
        animation: float 8s ease-in-out infinite;
    }
    
    .floating-particles .particle:nth-child(1) { animation-delay: 0s; }
    .floating-particles .particle:nth-child(2) { animation-delay: 2s; }
    .floating-particles .particle:nth-child(3) { animation-delay: 4s; }
    .floating-particles .particle:nth-child(4) { animation-delay: 6s; }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
            opacity: 0.4;
        }
        50% {
            transform: translateY(-30px) rotate(180deg);
            opacity: 0.8;
        }
    }
    
    /* Premium Glass Morphism */
    .backdrop-blur-2xl {
        backdrop-filter: blur(40px);
    }
    
    /* Enhanced Shadows */
    .shadow-3xl {
        box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.4);
    }
    
    /* Line Clamp Utility */
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Smooth Scroll */
    html {
        scroll-behavior: smooth;
    }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: rgba(243, 244, 246, 0.8);
    }
    
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #10b981, #3b82f6);
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #059669, #2563eb);
    }
    
    /* Custom scrollbar for filter sections */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(229, 231, 235, 0.6);
        border-radius: 3px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #10b981, #3b82f6);
        border-radius: 3px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #059669, #2563eb);
    }
    
    /* Search Input Focus Glow */
    input:focus {
        box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.4), 0 0 30px rgba(16, 185, 129, 0.3);
    }
    
    /* Property Card Hover Effects */
    .group:hover .particle {
        animation-duration: 4s;
    }
    
    /* Mobile Responsive Adjustments */
    @media (max-width: 640px) {
        .backdrop-blur-2xl {
            backdrop-filter: blur(20px);
        }
    }
</style>

<script>
    document.addEventListener('livewire:initialized', () => {
        let searchInput = null;
        let suggestionsDropdown = null;
        
        // Wait for DOM to be ready
        setTimeout(() => {
            searchInput = document.querySelector('input[wire\\:model\\.live\\.debounce\\.500ms="searchQuery"]');
            suggestionsDropdown = document.querySelector('.absolute.z-50');
        }, 100);
        
        // Auto-hide suggestions when clicking outside
        document.addEventListener('click', (e) => {
            // Only hide if we have references and click is outside both search input and suggestions
            if (searchInput && !searchInput.contains(e.target) && 
                (!suggestionsDropdown || !suggestionsDropdown.contains(e.target))) {
                // Use dispatch instead of call to avoid loading state
                window.Livewire.dispatch('hideSuggestions');
            }
        });
        
        // Handle escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && searchInput === document.activeElement) {
                window.Livewire.dispatch('hideSuggestions');
            }
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

            const icon = type === 'success' ?
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                type === 'error' ?
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' :
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

            toast.className = `flex items-center space-x-3 ${bgColor} text-white px-6 py-4 rounded-xl shadow-lg transform translate-x-full transition-transform duration-300 backdrop-blur-sm`;
            toast.innerHTML = `
                <div class="flex-shrink-0">${icon}</div>
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
    });
</script>
</div>