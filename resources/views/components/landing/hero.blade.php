<!-- Premium Hero Component -->
<section class="relative min-h-screen overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900" id="hero" x-data="heroComponent()">
    <!-- Sophisticated Background with Multiple Layers -->
    <div class="absolute inset-0">
        <!-- Main Background Image -->
        <div class="w-full h-full bg-cover bg-center bg-no-repeat transform scale-105 transition-transform duration-[20s] ease-out"
            style="background-image: url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=2075&q=80');">
        </div>
        <!-- Premium Gradient Overlays -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/70 to-slate-800/60"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-slate-900/20"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/20 via-transparent to-blue-900/20"></div>
    </div>

    <!-- Animated Geometric Elements -->
    <div class="absolute inset-0 opacity-30">
        <!-- Large Floating Orbs -->
        <div class="floating-element absolute top-1/4 right-1/4 w-40 h-40 bg-gradient-to-br from-emerald-400/40 to-teal-500/30 rounded-full blur-3xl"></div>
        <div class="floating-element absolute bottom-1/3 left-1/4 w-56 h-56 bg-gradient-to-br from-blue-400/25 to-indigo-500/20 rounded-full blur-3xl"></div>
        <div class="floating-element absolute top-1/2 right-1/3 w-32 h-32 bg-gradient-to-br from-amber-400/30 to-orange-500/25 rounded-full blur-2xl"></div>
        
        <!-- Small Accent Dots -->
        <div class="floating-element absolute top-1/3 left-1/2 w-4 h-4 bg-emerald-400/60 rounded-full blur-sm"></div>
        <div class="floating-element absolute bottom-1/4 right-1/2 w-6 h-6 bg-blue-400/50 rounded-full blur-sm"></div>
        <div class="floating-element absolute top-3/4 left-1/3 w-3 h-3 bg-amber-400/70 rounded-full blur-sm"></div>
    </div>

    <!-- Premium Mesh Gradient -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-gradient-to-br from-emerald-400 to-transparent rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-tl from-blue-400 to-transparent rounded-full blur-3xl"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-30 flex flex-col justify-center min-h-screen pt-24 pb-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

            <!-- Mobile-Optimized Premium Badge -->
            <div class="premium-badge inline-flex items-center px-4 py-2 md:px-8 md:py-4 bg-white/5 backdrop-blur-xl rounded-xl md:rounded-2xl border border-white/10 shadow-2xl mb-6 md:mb-12 group hover:bg-white/10 transition-all duration-500">
                <div class="flex items-center space-x-2 md:space-x-3">
                    <div class="w-4 h-4 md:w-6 md:h-6 bg-gradient-to-r from-yellow-400 to-amber-500 rounded-full flex items-center justify-center">
                        <svg class="w-2.5 h-2.5 md:w-4 md:h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <span class="text-xs md:text-sm font-semibold text-white tracking-wide">#1 Real Estate Platform in Nigeria</span>
                    <div class="w-1.5 h-1.5 md:w-2 md:h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                </div>
            </div>

            <!-- Mobile-Optimized Main Heading -->
            <div class="space-y-4 md:space-y-8 mb-8 md:mb-16">
                <h1 class="hero-title relative">
                    <!-- Mobile-First Typography -->
                    <div class="space-y-2 md:space-y-4">
                        <div class="flex items-center justify-center space-x-3 md:space-x-6">
                            <span class="text-white text-3xl sm:text-4xl md:text-6xl lg:text-7xl font-black tracking-tight leading-none">
                                Find Your
                            </span>
                            <div class="relative">
                                <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-blue-400 bg-clip-text text-transparent text-3xl sm:text-4xl md:text-6xl lg:text-7xl font-black tracking-tight leading-none">
                                    Perfect
                                </span>
                                <!-- Animated underline -->
                                <div class="absolute -bottom-1 md:-bottom-2 left-0 right-0 h-0.5 md:h-1 bg-gradient-to-r from-emerald-400 to-blue-400 rounded-full transform scale-x-0 animate-pulse"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-center space-x-3 md:space-x-6">
                            <span class="text-slate-200 text-3xl sm:text-4xl md:text-6xl lg:text-7xl font-light tracking-wide">
                                Home
                            </span>
                            <div class="relative">
                                <span class="bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent text-3xl sm:text-4xl md:text-6xl lg:text-7xl font-black tracking-tight">
                                    Today
                                </span>
                                <!-- Sparkle effect -->
                                <div class="absolute -top-2 -right-2 md:-top-3 md:-right-3 w-3 h-3 md:w-6 md:h-6 bg-gradient-to-r from-yellow-400 to-amber-500 rounded-full opacity-75 animate-ping"></div>
                            </div>
                        </div>
                    </div>
                </h1>

                <!-- Mobile-Optimized Subtitle -->
                <div class="space-y-2 md:space-y-4">
                    <p class="hero-subtitle text-lg sm:text-xl md:text-3xl lg:text-4xl text-slate-300 max-w-5xl mx-auto leading-relaxed font-light">
                        Connect with Nigeria's most <span class="text-emerald-400 font-semibold">trusted agents</span> and discover 
                        <span class="text-blue-400 font-semibold">premium properties</span> in your desired location.
                    </p>
                    <p class="text-sm md:text-lg lg:text-xl text-slate-400 max-w-3xl mx-auto">
                        From Lagos to Abuja, find verified listings with transparent pricing.
                    </p>
                </div>
            </div>

            <!-- Premium Smart Search Box with Autocomplete -->
            <div class="search-container mb-6 md:mb-8" x-data="smartSearch()">
                <div class="max-w-6xl mx-auto relative">
                    <!-- Clean Search Bar -->
                    <div class="premium-search-bar bg-white/5 backdrop-blur-2xl rounded-3xl md:rounded-[2rem] shadow-2xl border border-white/20 overflow-hidden">
                        <form @submit.prevent="handleSearch" @click.stop class="p-2 md:p-3" data-search-type="enhanced" method="GET" action="/properties">
                            <div class="flex items-center bg-white/10 backdrop-blur-sm rounded-2xl md:rounded-[1.5rem] border border-white/10 transition-all duration-300 hover:border-white/20 focus-within:border-emerald-500/50 focus-within:ring-4 focus-within:ring-emerald-500/20"
                                :class="{ 'border-emerald-500/50 ring-4 ring-emerald-500/20': showSuggestions }">
                                
                                <!-- Smart Search Icon -->
                                <div class="flex-shrink-0 pl-4 md:pl-6 pr-3 md:pr-4">
                                    <div class="w-5 h-5 md:w-6 md:h-6 text-white/70 transition-colors duration-300"
                                         :class="{ 'text-emerald-400': showSuggestions || searchQuery.length > 0 }">
                                        <svg x-show="!isLoading" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" class="w-full h-full transition-all duration-300">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <!-- Loading spinner -->
                                        <svg x-show="isLoading" class="animate-spin w-full h-full" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                
                                <!-- Enhanced Search Input with Autocomplete -->
                                <div class="flex-1 relative">
                                    <input type="text" 
                                           name="q"
                                           x-model="searchQuery"
                                           @input="if(searchQuery.length < 2) { suggestions = []; showSuggestions = false; isLoading = false; }"
                                           @input.debounce.300ms="getSuggestions()"
                                           @focus="handleFocus()"
                                           @blur.debounce.150ms="handleBlur()"
                                           @keydown.arrow-down.prevent="highlightNext()"
                                           @keydown.arrow-up.prevent="highlightPrevious()"
                                           @keydown.enter.prevent="selectHighlighted()"
                                           @keydown.escape="closeSuggestions()"
                                           placeholder="Search by location, property type, price, features... (e.g., '3 bedroom Lagos', 'under 5M', 'swimming pool')"
                                           class="w-full py-4 md:py-5 bg-transparent text-base md:text-xl text-white font-medium placeholder-white/70 focus:outline-none border-0 ring-0 pr-4">
                                </div>
                                
                                <!-- Search Button -->
                                <div class="flex-shrink-0 p-1 md:p-2">
                                    <button type="submit"
                                            :disabled="isLoading"
                                            class="group relative bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-700 hover:via-emerald-600 hover:to-teal-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-3 md:py-4 px-6 md:px-8 rounded-xl md:rounded-2xl transition-all duration-500 hover:scale-105 shadow-lg hover:shadow-emerald-500/40 flex items-center justify-center relative overflow-hidden min-w-[80px] md:min-w-[120px]">
                                        <!-- Hover overlay -->
                                        <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-white/10 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                                        
                                        <!-- Button content -->
                                        <div class="relative z-10 flex items-center">
                                            <svg class="w-4 h-4 md:w-5 md:h-5 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                            <span class="hidden md:inline ml-2 text-sm font-bold tracking-wide group-hover:tracking-wider transition-all duration-300">Search</span>
                                        </div>
                                        
                                        <!-- Animated border -->
                                        <div class="absolute inset-0 rounded-xl md:rounded-2xl border-2 border-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Smart Suggestions Dropdown -->
                    <div x-show="showSuggestions && suggestions.length > 0 && !isLoading"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
                         class="search-suggestions-dropdown absolute top-full left-0 right-0 mt-2 z-[9999] bg-white/95 backdrop-blur-xl rounded-2xl md:rounded-3xl shadow-2xl border border-white/20 overflow-hidden max-h-96 overflow-y-auto">
                        
                        <!-- Suggestions Content -->
                        <div class="p-2 md:p-4">
                            <template x-for="(suggestion, index) in suggestions" :key="index">
                                <div @click="selectSuggestion(suggestion)"
                                     @mouseenter="highlightedIndex = index"
                                     :class="{ 'bg-emerald-50 border-emerald-200': index === highlightedIndex }"
                                     class="flex items-center p-3 md:p-4 hover:bg-gray-50 cursor-pointer rounded-xl transition-all duration-200 border border-transparent group">
                                    
                                    <!-- Suggestion Icon -->
                                    <div class="flex-shrink-0 mr-3 md:mr-4">
                                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg flex items-center justify-center"
                                             :class="{
                                                 'bg-emerald-100 text-emerald-600': suggestion.type === 'property',
                                                 'bg-blue-100 text-blue-600': suggestion.type === 'location',
                                                 'bg-purple-100 text-purple-600': suggestion.type === 'quick_filter',
                                                 'bg-amber-100 text-amber-600': suggestion.type === 'feature'
                                             }">
                                            <!-- Property Icon -->
                                            <svg x-show="suggestion.type === 'property'" class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                            </svg>
                                            <!-- Location Icon -->
                                            <svg x-show="suggestion.type === 'location'" class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <!-- Filter Icon -->
                                            <svg x-show="suggestion.type === 'quick_filter'" class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z" />
                                            </svg>
                                            <!-- Feature Icon -->
                                            <svg x-show="suggestion.type === 'feature'" class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Suggestion Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm md:text-base font-semibold text-gray-900 truncate group-hover:text-emerald-600 transition-colors duration-200" x-text="suggestion.label"></h4>
                                                <p class="text-xs md:text-sm text-gray-500 truncate" x-text="suggestion.subtitle"></p>
                                            </div>
                                            <div class="flex-shrink-0 ml-2 text-right">
                                                <span x-show="suggestion.price" class="text-xs md:text-sm font-bold text-emerald-600" x-text="suggestion.price"></span>
                                                <span x-show="suggestion.count" class="text-xs text-gray-400 block" x-text="suggestion.count"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- No Results Message -->
                            <div x-show="false" class="p-4 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <p class="text-sm font-medium">No suggestions found</p>
                                <p class="text-xs">Try searching for locations, property types, or features</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile-Optimized CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 md:gap-8 justify-center hero-buttons mb-12 md:mb-20">
                <!-- Primary CTA -->
                <button class="group relative inline-flex items-center px-6 py-3 md:px-12 md:py-6 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 text-white font-bold text-sm md:text-xl rounded-xl md:rounded-2xl overflow-hidden shadow-2xl hover:shadow-emerald-500/40 transition-all duration-500 transform hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                    <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span class="relative z-10 flex items-center">
                        <svg class="w-4 h-4 md:w-6 md:h-6 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Explore Properties
                        <svg class="w-4 h-4 md:w-6 md:h-6 ml-2 md:ml-3 group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </span>
                </button>

                <!-- Secondary CTA - Contact Agents -->
                <button class="group relative inline-flex items-center px-6 py-3 md:px-12 md:py-6 bg-white/5 backdrop-blur-xl text-white font-semibold text-sm md:text-xl rounded-xl md:rounded-2xl border-2 border-white/20 hover:bg-white/10 hover:border-white/30 transition-all duration-500 overflow-hidden shadow-xl hover:shadow-2xl transform hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-white/5 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                    <span class="relative z-10 flex items-center">
                        <div class="w-6 h-6 md:w-10 md:h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full flex items-center justify-center mr-2 md:mr-3 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-3 h-3 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        Contact Agents
                    </span>
                </button>
            </div>

            <!-- Mobile-Optimized Trust Signals -->
            <div class="trust-signals mb-8 md:mb-20">
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 md:gap-8 lg:gap-16">
                    <div class="group text-center p-4 md:p-6 bg-white/5 backdrop-blur-sm rounded-xl md:rounded-2xl border border-white/10 hover:bg-white/10 transition-all duration-300 hover:scale-105 w-full sm:w-auto">
                        <div class="text-2xl md:text-4xl lg:text-5xl font-black text-white counter mb-1 md:mb-2" data-target="10000">0</div>
                        <div class="text-xs md:text-sm font-semibold text-emerald-400 tracking-wide uppercase">Properties Listed</div>
                        <div class="w-full h-0.5 md:h-1 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full mt-2 md:mt-3 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                    <div class="group text-center p-4 md:p-6 bg-white/5 backdrop-blur-sm rounded-xl md:rounded-2xl border border-white/10 hover:bg-white/10 transition-all duration-300 hover:scale-105 w-full sm:w-auto">
                        <div class="text-2xl md:text-4xl lg:text-5xl font-black text-white counter mb-1 md:mb-2" data-target="5000">0</div>
                        <div class="text-xs md:text-sm font-semibold text-blue-400 tracking-wide uppercase">Happy Clients</div>
                        <div class="w-full h-0.5 md:h-1 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full mt-2 md:mt-3 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                    <div class="group text-center p-4 md:p-6 bg-white/5 backdrop-blur-sm rounded-xl md:rounded-2xl border border-white/10 hover:bg-white/10 transition-all duration-300 hover:scale-105 w-full sm:w-auto">
                        <div class="text-2xl md:text-4xl lg:text-5xl font-black text-white counter mb-1 md:mb-2" data-target="500">0</div>
                        <div class="text-xs md:text-sm font-semibold text-amber-400 tracking-wide uppercase">Verified Agents</div>
                        <div class="w-full h-0.5 md:h-1 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full mt-2 md:mt-3 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                </div>
                
                <!-- Mobile-Optimized Trust Indicators -->
                <div class="flex flex-wrap items-center justify-center gap-4 md:gap-8 mt-6 md:mt-12 text-slate-400">
                    <div class="flex items-center space-x-1 md:space-x-2">
                        <svg class="w-3 h-3 md:w-5 md:h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-xs md:text-sm font-medium">SSL Secured</span>
                    </div>
                    <div class="flex items-center space-x-1 md:space-x-2">
                        <svg class="w-3 h-3 md:w-5 md:h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-xs md:text-sm font-medium">Verified Listings</span>
                    </div>
                    <div class="flex items-center space-x-1 md:space-x-2">
                        <svg class="w-3 h-3 md:w-5 md:h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-xs md:text-sm font-medium">24/7 Support</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile-Optimized Scroll Indicator -->
    <div class="absolute bottom-4 md:bottom-6 left-1/2 transform -translate-x-1/2 z-30">
        <div class="flex flex-col items-center space-y-1 md:space-y-2 text-white/70 group hover:text-white/90 transition-colors duration-300">
            <span class="text-xs md:text-xs font-medium tracking-wider uppercase opacity-75 group-hover:opacity-100 transition-opacity duration-300 hidden md:block">Scroll to Explore</span>
            <div class="animate-bounce">
                <div class="w-6 h-8 md:w-8 md:h-12 border-2 border-white/30 rounded-full flex justify-center group-hover:border-white/50 transition-colors duration-300">
                    <div class="w-0.5 h-2 md:w-1 md:h-3 bg-white/50 rounded-full mt-1 md:mt-2 animate-pulse group-hover:bg-white/70 transition-colors duration-300"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Search Suggestions Dropdown - Always on top */
    .search-suggestions-dropdown {
        z-index: 99999 !important;
        position: absolute !important;
    }
    
    /* Search container stacking context */
    .search-container {
        position: relative;
        z-index: 1000;
    }
    
    /* Ensure hero elements don't interfere with dropdown */
    .hero-buttons,
    .trust-signals {
        position: relative;
        z-index: 10;
    }
</style>

<script>
    function heroComponent() {
        return {
            showDemo: false,
            searchQuery: '',

            handleSearch() {
                console.log('Search initiated:', {
                    query: this.searchQuery
                });

                // Add search loading animation
                const button = event.target.querySelector('button[type="submit"]') || event.target;
                gsap.to(button, {
                    scale: 0.95,
                    duration: 0.1,
                    yoyo: true,
                    repeat: 1
                });

                // Build search parameters for redirect
                const searchParams = new URLSearchParams();
                
                // Add main search query
                if (this.searchQuery && this.searchQuery.trim()) {
                    searchParams.set('q', this.searchQuery.trim());
                }

                // Redirect to dedicated search results page
                const searchUrl = `/properties?${searchParams.toString()}`;
                console.log('🔍 Hero Search: Redirecting to', searchUrl);
                
                // Force redirect to dedicated search page
                window.location.href = searchUrl;
            },

            init() {
                this.initializeAnimations();
                this.initializeCounters();
            },

            initializeAnimations() {
                // Premium hero entrance timeline
                const tl = gsap.timeline();
                
                // Background subtle zoom animation
                gsap.to('.bg-cover', {
                    scale: 1,
                    duration: 20,
                    ease: "none"
                });

                // Premium badge entrance
                tl.fromTo('.premium-badge', {
                    opacity: 0,
                    scale: 0.8,
                    y: -30
                }, {
                    opacity: 1,
                    scale: 1,
                    y: 0,
                    duration: 1.2,
                    ease: "back.out(1.7)"
                });

                // Main title orchestrated animation
                tl.fromTo('.hero-title > div > span:nth-child(1)', {
                    opacity: 0,
                    y: 80,
                    scale: 0.9
                }, {
                    opacity: 1,
                    y: 0,
                    scale: 1,
                    duration: 1.5,
                    ease: "power3.out"
                }, "-=0.8");

                tl.fromTo('.hero-title > div > div:nth-child(2)', {
                    opacity: 0,
                    y: 60,
                    scale: 0.95
                }, {
                    opacity: 1,
                    y: 0,
                    scale: 1,
                    duration: 1.3,
                    ease: "power3.out"
                }, "-=1.2");

                tl.fromTo('.hero-title > div > div:nth-child(3)', {
                    opacity: 0,
                    y: 40,
                    scale: 0.98
                }, {
                    opacity: 1,
                    y: 0,
                    scale: 1,
                    duration: 1.1,
                    ease: "power3.out"
                }, "-=1.0");

                // Subtitle with stagger effect
                tl.fromTo('.hero-subtitle', {
                    opacity: 0,
                    y: 40
                }, {
                    opacity: 1,
                    y: 0,
                    duration: 1.2,
                    ease: "power2.out"
                }, "-=0.8");

                // CTA buttons with bounce
                tl.fromTo('.hero-buttons', {
                    opacity: 0,
                    y: 50,
                    scale: 0.9
                }, {
                    opacity: 1,
                    y: 0,
                    scale: 1,
                    duration: 1,
                    ease: "back.out(1.4)"
                }, "-=0.6");

                // Trust signals with stagger
                tl.fromTo('.trust-signals .group', {
                    opacity: 0,
                    y: 30,
                    scale: 0.9
                }, {
                    opacity: 1,
                    y: 0,
                    scale: 1,
                    duration: 0.8,
                    ease: "power2.out",
                    stagger: 0.15
                }, "-=0.4");

                // Search bar dramatic entrance
                tl.fromTo('.search-bar', {
                    opacity: 0,
                    y: 120,
                    scale: 0.95
                }, {
                    opacity: 1,
                    y: 0,
                    scale: 1,
                    duration: 1.4,
                    ease: "power3.out"
                }, "-=0.5");

                // Enhanced floating elements
                gsap.to('.floating-element', {
                    y: (i) => `${-20 - (i * 5)}px`,
                    x: (i) => `${Math.sin(i) * 10}px`,
                    rotation: (i) => `${Math.sin(i) * 5}`,
                    duration: (i) => 6 + (i * 0.5),
                    ease: "power1.inOut",
                    yoyo: true,
                    repeat: -1,
                    stagger: 1.2
                });

                // Sparkle animation for "You."
                gsap.to('.hero-title .animate-ping', {
                    scale: 1.5,
                    opacity: 0,
                    duration: 1.5,
                    ease: "power2.out",
                    repeat: -1,
                    delay: 2
                });

                // Underline animation
                gsap.to('.hero-title .scale-x-0', {
                    scaleX: 1,
                    duration: 2,
                    ease: "power2.out",
                    delay: 2.5
                });

                // Premium search bar entrance animation
                gsap.fromTo('.premium-search-bar', {
                    opacity: 0,
                    y: 100,
                    scale: 0.95
                }, {
                    opacity: 1,
                    y: 0,
                    scale: 1,
                    duration: 1.2,
                    ease: "power3.out",
                    delay: 1.5
                });

                // Add hover effects for search elements
                const searchElements = document.querySelectorAll('.premium-search-bar input, .premium-search-bar select');
                searchElements.forEach(element => {
                    element.addEventListener('focus', () => {
                        gsap.to(element, {
                            scale: 1.02,
                            duration: 0.3,
                            ease: "power2.out"
                        });
                    });
                    
                    element.addEventListener('blur', () => {
                        gsap.to(element, {
                            scale: 1,
                            duration: 0.3,
                            ease: "power2.out"
                        });
                    });
                });
            },

            initializeCounters() {
                const counters = document.querySelectorAll('.counter');

                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target'));
                    const duration = 2.5;

                    gsap.to(counter, {
                        innerHTML: target,
                        duration: duration,
                        ease: "power2.out",
                        snap: {
                            innerHTML: 1
                        },
                        onUpdate: function() {
                            counter.innerHTML = Math.ceil(counter.innerHTML).toLocaleString();
                        },
                        scrollTrigger: {
                            trigger: counter,
                            start: "top 80%",
                            once: true
                        }
                    });
                });
            }
        }
    }

    // Smart Search Component with Advanced Autocomplete
    function smartSearch() {
        return {
            searchQuery: '',
            suggestions: [],
            showSuggestions: false,
            highlightedIndex: -1,
            isLoading: false,
            recentSearches: JSON.parse(localStorage.getItem('recentSearches') || '[]'),

            init() {
                console.log('Smart search component initialized');
                // Load popular searches on component init
                this.loadPopularSearches();
            },

            loadPopularSearches() {
                // Provide immediate popular suggestions
                this.popularSuggestions = [
                    { type: 'location', label: 'Lagos Island', value: 'Lagos Island', count: '234 properties', icon: 'map-pin' },
                    { type: 'location', label: 'Victoria Island', value: 'Victoria Island', count: '189 properties', icon: 'map-pin' },
                    { type: 'location', label: 'Ikoyi', value: 'Ikoyi', count: '156 properties', icon: 'map-pin' },
                    { type: 'property', label: '3 Bedroom Apartment', value: '3 bedroom apartment', count: 'Popular search', icon: 'home' },
                    { type: 'property', label: '2 Bedroom Flat', value: '2 bedroom flat', count: 'Popular search', icon: 'home' }
                ];
            },
            
            async getSuggestions() {
                // Clear previous suggestions
                this.suggestions = [];
                this.showSuggestions = false;
                this.isLoading = false;

                if (this.searchQuery.length < 2) {
                    this.suggestions = [];
                    this.showSuggestions = false;
                    this.isLoading = false;
                    return;
                }

                this.isLoading = true;

                try {
                    console.log('Fetching suggestions for:', this.searchQuery);

                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout

                    const response = await fetch(`/api/search/suggestions?q=${encodeURIComponent(this.searchQuery)}&limit=8`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        signal: controller.signal
                    });

                    clearTimeout(timeoutId);

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    const data = await response.json();
                    console.log('Suggestions response:', data);

                    if (data && Array.isArray(data.suggestions)) {
                        this.suggestions = data.suggestions;
                        this.highlightedIndex = -1;

                        // Only show dropdown if we have suggestions
                        this.showSuggestions = this.suggestions.length > 0;

                        // Don't show fallbacks - just hide dropdown if no results
                        if (this.suggestions.length === 0) {
                            this.showSuggestions = false;
                        }
                    } else {
                        console.warn('Invalid response format:', data);
                        this.suggestions = [];
                        this.showSuggestions = false;
                    }

                } catch (error) {
                    console.error('Search suggestions error:', error);

                    if (error.name === 'AbortError') {
                        console.warn('Search suggestion request timed out');
                    }

                    // Hide dropdown on error
                    this.suggestions = [];
                    this.showSuggestions = false;
                } finally {
                    this.isLoading = false;
                }
            },

            addFallbackSuggestions() {
                // Use popular suggestions as fallback
                const fallbackSuggestions = this.popularSuggestions || [
                    { type: 'location', label: 'Lagos Properties', value: 'Lagos', count: 'Popular location', icon: 'map-pin' },
                    { type: 'location', label: 'Abuja Properties', value: 'Abuja', count: 'Popular location', icon: 'map-pin' },
                    { type: 'property', label: '3 Bedroom Apartments', value: '3 bedroom apartment', count: 'Popular search', icon: 'home' },
                    { type: 'property', label: '2 Bedroom Flats', value: '2 bedroom flat', count: 'Popular search', icon: 'home' }
                ];

                // Filter fallbacks based on current query
                const query = this.searchQuery.toLowerCase();
                this.suggestions = fallbackSuggestions.filter(suggestion =>
                    suggestion.label.toLowerCase().includes(query) ||
                    suggestion.value.toLowerCase().includes(query)
                ).slice(0, 4); // Limit to 4 fallback suggestions

                this.showSuggestions = this.suggestions.length > 0;
                console.log('Added fallback suggestions:', this.suggestions);
            },

            handleFocus() {
                if (this.searchQuery.length >= 2) {
                    if (this.suggestions.length > 0) {
                        this.showSuggestions = true;
                    } else {
                        // Trigger new search if no suggestions
                        this.getSuggestions();
                    }
                }
                // Don't show anything for empty searches on focus
            },

            showPopularSuggestions() {
                if (this.popularSuggestions && this.popularSuggestions.length > 0) {
                    this.suggestions = this.popularSuggestions.slice(0, 5);
                    this.showSuggestions = true;
                    console.log('Showing popular suggestions');
                }
            },

            handleBlur() {
                // Delay hiding to allow clicks on suggestions
                setTimeout(() => {
                    this.showSuggestions = false;
                    this.highlightedIndex = -1;
                }, 200);
            },

            highlightNext() {
                if (this.suggestions.length === 0) return;
                
                this.highlightedIndex = this.highlightedIndex < this.suggestions.length - 1 
                    ? this.highlightedIndex + 1 
                    : 0;
            },

            highlightPrevious() {
                if (this.suggestions.length === 0) return;
                
                this.highlightedIndex = this.highlightedIndex > 0 
                    ? this.highlightedIndex - 1 
                    : this.suggestions.length - 1;
            },

            selectHighlighted() {
                if (this.highlightedIndex >= 0 && this.suggestions[this.highlightedIndex]) {
                    this.selectSuggestion(this.suggestions[this.highlightedIndex]);
                } else {
                    this.handleSearch();
                }
            },

            selectSuggestion(suggestion) {
                this.searchQuery = suggestion.value || suggestion.label;
                this.showSuggestions = false;
                this.highlightedIndex = -1;
                
                // Save to recent searches
                this.saveToRecentSearches(suggestion);
                
                // Handle different suggestion types
                switch (suggestion.type) {
                    case 'property':
                        if (suggestion.url) {
                            window.location.href = suggestion.url;
                            return;
                        }
                        break;
                    default:
                        // For location, feature, or any other suggestion, just use the value
                        this.searchQuery = suggestion.value || suggestion.label;
                        break;
                }
                
                // Trigger search
                this.handleSearch();
            },

            closeSuggestions() {
                this.showSuggestions = false;
                this.highlightedIndex = -1;
            },

            handleSearch() {
                // Prevent any other form submissions
                event.preventDefault();
                event.stopPropagation();

                // Save search query to recent searches
                if (this.searchQuery.trim()) {
                    this.saveToRecentSearches({
                        type: 'search',
                        label: this.searchQuery.trim(),
                        value: this.searchQuery.trim()
                    });
                }

                // Build search parameters
                const searchParams = new URLSearchParams();
                if (this.searchQuery.trim()) {
                    searchParams.set('q', this.searchQuery.trim());
                }

                // Navigate to dedicated search results page
                const searchUrl = `/properties?${searchParams.toString()}`;
                
                console.log('🔍 Enhanced Search: Redirecting to', searchUrl);
                
                // Force redirect to dedicated search page
                window.location.href = searchUrl;
                
                return false; // Extra prevention
            },

            saveToRecentSearches(item) {
                // Remove if already exists
                this.recentSearches = this.recentSearches.filter(search => 
                    search.label !== item.label || search.type !== item.type
                );
                
                // Add to beginning
                this.recentSearches.unshift({
                    ...item,
                    timestamp: new Date().toISOString()
                });
                
                // Keep only last 10 searches
                this.recentSearches = this.recentSearches.slice(0, 10);
                
                // Save to localStorage
                localStorage.setItem('recentSearches', JSON.stringify(this.recentSearches));
            },

            init() {
                // Add click outside handler
                document.addEventListener('click', (e) => {
                    if (!this.$el.contains(e.target)) {
                        this.showSuggestions = false;
                    }
                });

                // Add keyboard shortcuts
                document.addEventListener('keydown', (e) => {
                    // Focus search on Ctrl/Cmd + K
                    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                        e.preventDefault();
                        this.$el.querySelector('input').focus();
                    }
                });

                // Initialize animations
                this.$nextTick(() => {
                    // Add subtle animation to search bar on load
                    gsap.fromTo(this.$el, {
                        opacity: 0,
                        y: 20
                    }, {
                        opacity: 1,
                        y: 0,
                        duration: 0.8,
                        ease: "power2.out",
                        delay: 2
                    });
                });
            }
        };
    }
</script>
