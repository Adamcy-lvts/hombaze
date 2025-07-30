<!-- Premium How It Works Section - Mobile First -->
<section class="relative min-h-screen py-16 lg:py-24 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 overflow-hidden" x-data="premiumHowItWorksComponent()">
    <!-- Dynamic Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <!-- Animated Gradient Mesh -->
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-emerald-500/30 to-teal-500/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-br from-blue-500/25 to-indigo-500/15 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-gradient-to-br from-purple-500/20 to-pink-500/10 rounded-full blur-2xl animate-pulse" style="animation-delay: 2s;"></div>
        
        <!-- Floating Particles -->
        <div class="floating-particles">
            <div class="particle absolute top-20 left-10 w-2 h-2 bg-emerald-400/60 rounded-full"></div>
            <div class="particle absolute top-40 right-16 w-1.5 h-1.5 bg-blue-400/50 rounded-full"></div>
            <div class="particle absolute bottom-32 left-20 w-1 h-1 bg-purple-400/70 rounded-full"></div>
            <div class="particle absolute bottom-20 right-10 w-2.5 h-2.5 bg-teal-400/40 rounded-full"></div>
            <div class="particle absolute top-60 left-1/3 w-1 h-1 bg-amber-400/60 rounded-full"></div>
            <div class="particle absolute bottom-60 right-1/3 w-1.5 h-1.5 bg-rose-400/50 rounded-full"></div>
        </div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Premium Section Header -->
        <div class="text-center mb-16 lg:mb-24" x-intersect.once="$el.classList.add('animate-fade-up')">
            <!-- Premium Badge -->
            <div class="inline-flex items-center space-x-3 bg-white/5 backdrop-blur-2xl border border-white/10 text-white px-6 py-3 rounded-2xl text-sm font-semibold mb-8 shadow-2xl">
                <div class="w-6 h-6 bg-gradient-to-r from-emerald-400 to-teal-400 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <span>Premium Experience</span>
                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-ping"></div>
            </div>
            
            <!-- Main Heading -->
            <h2 class="text-4xl sm:text-5xl lg:text-7xl font-black text-white mb-6 lg:mb-8 leading-tight">
                How 
                <span class="relative inline-block">
                    <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-blue-400 bg-clip-text text-transparent">HomeBaze</span>
                    <div class="absolute -bottom-2 left-0 right-0 h-1 bg-gradient-to-r from-emerald-400 to-blue-400 rounded-full transform scale-x-0 animate-scale-x"></div>
                </span>
                <br class="hidden sm:block">
                <span class="text-slate-300 font-light">Works</span>
            </h2>
            
            <!-- Subtitle -->
            <p class="text-lg sm:text-xl lg:text-2xl text-slate-300 max-w-4xl mx-auto leading-relaxed font-light">
                Your journey to premium Nigerian real estate starts here.
                <span class="text-emerald-400 font-medium">Simple steps</span>, 
                <span class="text-blue-400 font-medium">trusted process</span>, 
                <span class="text-purple-400 font-medium">guaranteed results</span>.
            </p>
        </div>

        <!-- Premium Process Steps - Mobile First -->
        <div class="relative mb-20 lg:mb-32">
            <!-- Dynamic Connection Line -->
            <div class="hidden md:block absolute top-16 left-1/2 transform -translate-x-1/2 w-5/6 max-w-4xl">
                <svg class="w-full h-4" viewBox="0 0 800 40" fill="none">
                    <defs>
                        <linearGradient id="connectionGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:#10b981;stop-opacity:0.6" />
                            <stop offset="50%" style="stop-color:#3b82f6;stop-opacity:0.8" />
                            <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:0.6" />
                        </linearGradient>
                        <filter id="glow">
                            <feGaussianBlur stdDeviation="2" result="coloredBlur"/>
                            <feMerge> 
                                <feMergeNode in="coloredBlur"/>
                                <feMergeNode in="SourceGraphic"/>
                            </feMerge>
                        </filter>
                    </defs>
                    <path d="M20 20 Q200 5 400 20 T780 20" stroke="url(#connectionGradient)" stroke-width="3" fill="none" filter="url(#glow)" stroke-linecap="round"/>
                    <circle cx="80" cy="20" r="4" fill="#10b981" class="animate-pulse">
                        <animate attributeName="opacity" values="0.4;1;0.4" dur="2s" repeatCount="indefinite"/>
                    </circle>
                    <circle cx="400" cy="20" r="4" fill="#3b82f6" class="animate-pulse" style="animation-delay: 0.7s;">
                        <animate attributeName="opacity" values="0.4;1;0.4" dur="2s" repeatCount="indefinite" begin="0.7s"/>
                    </circle>
                    <circle cx="720" cy="20" r="4" fill="#8b5cf6" class="animate-pulse" style="animation-delay: 1.4s;">
                        <animate attributeName="opacity" values="0.4;1;0.4" dur="2s" repeatCount="indefinite" begin="1.4s"/>
                    </circle>
                </svg>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-16 relative z-10">
                
                <!-- Step 1: Search & Discover -->
                <div class="group relative" x-intersect.once="$el.classList.add('animate-slide-up')" style="animation-delay: 0.2s;">
                    <!-- Glass Card -->
                    <div class="relative p-6 lg:p-8 bg-white/5 backdrop-blur-2xl rounded-3xl border border-white/10 shadow-2xl hover:shadow-3xl transition-all duration-700 hover:bg-white/10 hover:border-emerald-400/30 hover:scale-105">
                        <!-- Step Number Floating -->
                        <div class="absolute -top-6 left-6 w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-xl border-4 border-white/20 group-hover:scale-110 transition-transform duration-500">
                            <span class="text-white text-lg font-black">1</span>
                        </div>
                        
                        <!-- Premium Icon Container -->
                        <div class="mb-8 mt-4">
                            <div class="w-20 h-20 lg:w-24 lg:h-24 bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-700 shadow-2xl border border-emerald-400/20">
                                <svg class="w-10 h-10 lg:w-12 lg:h-12 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="text-center">
                            <h3 class="text-xl lg:text-2xl font-black text-white mb-4 group-hover:text-emerald-400 transition-colors duration-500">
                                Search & Discover
                            </h3>
                            <p class="text-slate-300 leading-relaxed text-sm lg:text-base group-hover:text-slate-200 transition-colors duration-500">
                                Browse thousands of verified premium properties across Nigeria with our intelligent search filters and AI recommendations.
                            </p>
                        </div>
                        
                        <!-- Hover Glow Effect -->
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/0 to-emerald-600/0 group-hover:from-emerald-500/5 group-hover:to-emerald-600/5 rounded-3xl transition-all duration-700"></div>
                    </div>
                </div>

                <!-- Step 2: Connect & Engage -->
                <div class="group relative" x-intersect.once="$el.classList.add('animate-slide-up')" style="animation-delay: 0.4s;">
                    <!-- Glass Card -->
                    <div class="relative p-6 lg:p-8 bg-white/5 backdrop-blur-2xl rounded-3xl border border-white/10 shadow-2xl hover:shadow-3xl transition-all duration-700 hover:bg-white/10 hover:border-blue-400/30 hover:scale-105">
                        <!-- Step Number Floating -->
                        <div class="absolute -top-6 left-6 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-xl border-4 border-white/20 group-hover:scale-110 transition-transform duration-500">
                            <span class="text-white text-lg font-black">2</span>
                        </div>
                        
                        <!-- Premium Icon Container -->
                        <div class="mb-8 mt-4">
                            <div class="w-20 h-20 lg:w-24 lg:h-24 bg-gradient-to-br from-blue-500/20 to-blue-600/10 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-700 shadow-2xl border border-blue-400/20">
                                <svg class="w-10 h-10 lg:w-12 lg:h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="text-center">
                            <h3 class="text-xl lg:text-2xl font-black text-white mb-4 group-hover:text-blue-400 transition-colors duration-500">
                                Connect & Engage
                            </h3>
                            <p class="text-slate-300 leading-relaxed text-sm lg:text-base group-hover:text-slate-200 transition-colors duration-500">
                                Connect instantly with verified agents and property owners. Schedule virtual tours and get personalized assistance.
                            </p>
                        </div>
                        
                        <!-- Hover Glow Effect -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-blue-600/0 group-hover:from-blue-500/5 group-hover:to-blue-600/5 rounded-3xl transition-all duration-700"></div>
                    </div>
                </div>

                <!-- Step 3: Secure & Move -->
                <div class="group relative" x-intersect.once="$el.classList.add('animate-slide-up')" style="animation-delay: 0.6s;">
                    <!-- Glass Card -->
                    <div class="relative p-6 lg:p-8 bg-white/5 backdrop-blur-2xl rounded-3xl border border-white/10 shadow-2xl hover:shadow-3xl transition-all duration-700 hover:bg-white/10 hover:border-purple-400/30 hover:scale-105">
                        <!-- Step Number Floating -->
                        <div class="absolute -top-6 left-6 w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-xl border-4 border-white/20 group-hover:scale-110 transition-transform duration-500">
                            <span class="text-white text-lg font-black">3</span>
                        </div>
                        
                        <!-- Premium Icon Container -->
                        <div class="mb-8 mt-4">
                            <div class="w-20 h-20 lg:w-24 lg:h-24 bg-gradient-to-br from-purple-500/20 to-purple-600/10 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-700 shadow-2xl border border-purple-400/20">
                                <svg class="w-10 h-10 lg:w-12 lg:h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="text-center">
                            <h3 class="text-xl lg:text-2xl font-black text-white mb-4 group-hover:text-purple-400 transition-colors duration-500">
                                Secure & Move
                            </h3>
                            <p class="text-slate-300 leading-relaxed text-sm lg:text-base group-hover:text-slate-200 transition-colors duration-500">
                                Complete secure transactions with our escrow protection. Get your keys and move into your dream home with confidence.
                            </p>
                        </div>
                        
                        <!-- Hover Glow Effect -->
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/0 to-purple-600/0 group-hover:from-purple-500/5 group-hover:to-purple-600/5 rounded-3xl transition-all duration-700"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Premium Trust Indicators -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-8 mb-20 lg:mb-32" x-intersect.once="$el.classList.add('animate-fade-up')">
            <!-- Verified Properties -->
            <a href="#property-verification" class="group block p-4 lg:p-6 bg-white/5 backdrop-blur-2xl rounded-2xl border border-white/10 shadow-xl hover:shadow-2xl hover:bg-white/10 hover:border-emerald-400/30 transition-all duration-500 hover:scale-105">
                <div class="text-center">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 rounded-2xl flex items-center justify-center mx-auto mb-3 lg:mb-4 group-hover:scale-110 transition-transform duration-500 border border-emerald-400/20">
                        <svg class="w-6 h-6 lg:w-8 lg:h-8 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4 8-8z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm lg:text-base font-bold text-white mb-1 lg:mb-2 group-hover:text-emerald-400 transition-colors duration-300">Verified Properties</h3>
                    <p class="text-xs lg:text-sm text-slate-400 group-hover:text-slate-300 transition-colors duration-300">100% authentic listings</p>
                </div>
            </a>

            <!-- Trusted Agents -->
            <a href="#agent-verification" class="group block p-4 lg:p-6 bg-white/5 backdrop-blur-2xl rounded-2xl border border-white/10 shadow-xl hover:shadow-2xl hover:bg-white/10 hover:border-blue-400/30 transition-all duration-500 hover:scale-105">
                <div class="text-center">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 bg-gradient-to-br from-blue-500/20 to-blue-600/10 rounded-2xl flex items-center justify-center mx-auto mb-3 lg:mb-4 group-hover:scale-110 transition-transform duration-500 border border-blue-400/20">
                        <svg class="w-6 h-6 lg:w-8 lg:h-8 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 6L13.5 7.5C13.1 7.9 12.6 8 12 8S10.9 7.9 10.5 7.5L9 6L3 7V9H5V20H7V13H9V20H11V9H13V20H15V13H17V20H19V9H21Z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm lg:text-base font-bold text-white mb-1 lg:mb-2 group-hover:text-blue-400 transition-colors duration-300">Trusted Agents</h3>
                    <p class="text-xs lg:text-sm text-slate-400 group-hover:text-slate-300 transition-colors duration-300">Licensed professionals</p>
                </div>
            </a>

            <!-- Secure Transactions -->
            <div class="group p-4 lg:p-6 bg-white/5 backdrop-blur-2xl rounded-2xl border border-white/10 shadow-xl hover:shadow-2xl hover:bg-white/10 hover:border-amber-400/30 transition-all duration-500 hover:scale-105">
                <div class="text-center">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 bg-gradient-to-br from-amber-500/20 to-amber-600/10 rounded-2xl flex items-center justify-center mx-auto mb-3 lg:mb-4 group-hover:scale-110 transition-transform duration-500 border border-amber-400/20">
                        <svg class="w-6 h-6 lg:w-8 lg:h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm lg:text-base font-bold text-white mb-1 lg:mb-2 group-hover:text-amber-400 transition-colors duration-300">Secure Transactions</h3>
                    <p class="text-xs lg:text-sm text-slate-400 group-hover:text-slate-300 transition-colors duration-300">Escrow protection</p>
                </div>
            </div>

            <!-- 24/7 Support -->
            <div class="group p-4 lg:p-6 bg-white/5 backdrop-blur-2xl rounded-2xl border border-white/10 shadow-xl hover:shadow-2xl hover:bg-white/10 hover:border-purple-400/30 transition-all duration-500 hover:scale-105">
                <div class="text-center">
                    <div class="w-12 h-12 lg:w-16 lg:h-16 bg-gradient-to-br from-purple-500/20 to-purple-600/10 rounded-2xl flex items-center justify-center mx-auto mb-3 lg:mb-4 group-hover:scale-110 transition-transform duration-500 border border-purple-400/20">
                        <svg class="w-6 h-6 lg:w-8 lg:h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm lg:text-base font-bold text-white mb-1 lg:mb-2 group-hover:text-purple-400 transition-colors duration-300">24/7 Support</h3>
                    <p class="text-xs lg:text-sm text-slate-400 group-hover:text-slate-300 transition-colors duration-300">Always here to help</p>
                </div>
            </div>
        </div>

        <!-- Premium CTA Section -->
        <div class="text-center" x-intersect.once="$el.classList.add('animate-fade-up')">
            <div class="max-w-3xl mx-auto">
                <h3 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white mb-6">
                    Ready to Find Your 
                    <span class="bg-gradient-to-r from-emerald-400 to-blue-400 bg-clip-text text-transparent">Dream Home?</span>
                </h3>
                <p class="text-lg lg:text-xl text-slate-300 mb-10 leading-relaxed">
                    Join thousands of satisfied clients who found their perfect home through our premium platform
                </p>
                
                <!-- Premium Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                    <a href="{{ route('properties.search') }}" 
                       class="group inline-flex items-center justify-center space-x-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold py-4 px-8 lg:px-10 rounded-2xl transition-all duration-500 shadow-2xl hover:shadow-3xl hover:scale-105 border border-emerald-400/20">
                        <span class="text-lg">Browse Properties</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                    <a href="/register" 
                       class="group inline-flex items-center justify-center space-x-3 bg-white/10 hover:bg-white/20 backdrop-blur-2xl text-white font-bold py-4 px-8 lg:px-10 rounded-2xl border border-white/20 hover:border-white/30 transition-all duration-500 shadow-2xl hover:shadow-3xl hover:scale-105">
                        <span class="text-lg">Get Started Free</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
                
                <!-- Verification Links -->
                <div class="flex flex-wrap gap-6 justify-center items-center text-sm text-slate-400">
                    <span class="font-medium">Learn about our verification:</span>
                    <div class="flex flex-wrap gap-4 justify-center">
                        <a href="#property-verification" class="inline-flex items-center space-x-2 text-emerald-400 hover:text-emerald-300 font-semibold transition-colors duration-300 hover:underline">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Property Process</span>
                        </a>
                        <a href="#agent-verification" class="inline-flex items-center space-x-2 text-blue-400 hover:text-blue-300 font-semibold transition-colors duration-300 hover:underline">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                            </svg>
                            <span>Agent Process</span>
                        </a>
                        <a href="{{ route('properties.search', ['type' => 'sale']) }}" class="inline-flex items-center space-x-2 text-purple-400 hover:text-purple-300 font-semibold transition-colors duration-300 hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m0 0V11a1 1 0 011-1h2a1 1 0 011 1v10m0 0h3a1 1 0 001-1V10M9 21h6"></path>
                            </svg>
                            <span>For Sale</span>
                        </a>
                        <a href="{{ route('properties.search', ['type' => 'rent']) }}" class="inline-flex items-center space-x-2 text-amber-400 hover:text-amber-300 font-semibold transition-colors duration-300 hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            <span>For Rent</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Premium Animations */
    .animate-fade-up {
        animation: fadeUp 1s ease-out forwards;
    }
    
    .animate-slide-up {
        animation: slideUp 0.8s ease-out forwards;
    }
    
    .animate-scale-x {
        animation: scaleX 1.5s ease-out forwards 0.5s;
    }
    
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(60px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    @keyframes scaleX {
        from {
            transform: scaleX(0);
        }
        to {
            transform: scaleX(1);
        }
    }
    
    /* Floating Particles Animation */
    .floating-particles .particle {
        animation: float 6s ease-in-out infinite;
    }
    
    .floating-particles .particle:nth-child(1) { animation-delay: 0s; }
    .floating-particles .particle:nth-child(2) { animation-delay: 1s; }
    .floating-particles .particle:nth-child(3) { animation-delay: 2s; }
    .floating-particles .particle:nth-child(4) { animation-delay: 3s; }
    .floating-particles .particle:nth-child(5) { animation-delay: 4s; }
    .floating-particles .particle:nth-child(6) { animation-delay: 5s; }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
            opacity: 0.6;
        }
        50% {
            transform: translateY(-20px) rotate(180deg);
            opacity: 1;
        }
    }
    
    /* Premium Shadow Effects */
    .shadow-3xl {
        box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.4);
    }
    
    /* Glass Morphism Enhancement */
    .backdrop-blur-2xl {
        backdrop-filter: blur(40px);
    }
    
    /* Responsive Typography */
    @media (max-width: 640px) {
        .text-4xl { font-size: 2.5rem; }
        .text-5xl { font-size: 3rem; }
        .text-7xl { font-size: 3.5rem; }
    }
</style>

<script>
    function premiumHowItWorksComponent() {
        return {
            init() {
                console.log('Premium How It Works component initialized');
                
                // Enhanced scroll animations
                this.$nextTick(() => {
                    // Add dynamic particle movement based on scroll
                    window.addEventListener('scroll', () => {
                        const scrolled = window.pageYOffset;
                        const particles = document.querySelectorAll('.particle');
                        
                        particles.forEach((particle, index) => {
                            const speed = (index + 1) * 0.5;
                            particle.style.transform = `translateY(${scrolled * speed * 0.1}px) rotate(${scrolled * speed * 0.2}deg)`;
                        });
                    });
                    
                    // Enhanced hover effects for cards
                    const cards = document.querySelectorAll('.group');
                    cards.forEach(card => {
                        card.addEventListener('mouseenter', function() {
                            this.style.transform = 'translateY(-10px) scale(1.02)';
                        });
                        
                        card.addEventListener('mouseleave', function() {
                            this.style.transform = 'translateY(0) scale(1)';
                        });
                    });
                });
            }
        }
    }
</script>