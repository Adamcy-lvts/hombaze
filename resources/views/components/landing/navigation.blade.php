<!-- Premium Navigation Component -->
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 ease-out bg-gray-900" x-data="navigationComponent()">
    <!-- Enhanced Glass Morphism Background -->
    <div class="absolute inset-0 bg-white/5 backdrop-blur-xl border-b border-white/10 shadow-lg transition-all duration-300"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Mobile-Optimized Premium Logo -->
            <div class="flex items-center space-x-2 md:space-x-3 group">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-400 to-blue-500 rounded-xl md:rounded-2xl blur-sm opacity-75 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative w-8 h-8 md:w-12 md:h-12 bg-gradient-to-br from-emerald-500 to-blue-600 rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-500">
                        <svg class="w-5 h-5 md:w-7 md:h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg md:text-2xl font-black text-white tracking-tight">HomeBaze</span>
                    <span class="text-xs md:text-xs text-white/60 font-medium tracking-widest hidden md:block">PREMIUM</span>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-1">
                <a href="{{ route('properties.search') }}" wire:navigate class="nav-link group relative px-4 py-2 text-white/90 hover:text-white font-semibold transition-all duration-300">
                    <span class="relative z-10">Properties</span>
                    <div class="absolute inset-0 bg-white/10 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300 origin-center"></div>
                </a>
                <a href="{{ route('agents') }}" wire:navigate class="nav-link group relative px-4 py-2 text-white/90 hover:text-white font-semibold transition-all duration-300">
                    <span class="relative z-10">Agents</span>
                    <div class="absolute inset-0 bg-white/10 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300 origin-center"></div>
                </a>
                <a href="{{ route('agencies') }}" wire:navigate class="nav-link group relative px-4 py-2 text-white/90 hover:text-white font-semibold transition-all duration-300">
                    <span class="relative z-10">Agencies</span>
                    <div class="absolute inset-0 bg-white/10 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300 origin-center"></div>
                </a>
                <a href="{{ route('about') }}" wire:navigate class="nav-link group relative px-4 py-2 text-white/90 hover:text-white font-semibold transition-all duration-300">
                    <span class="relative z-10">About</span>
                    <div class="absolute inset-0 bg-white/10 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300 origin-center"></div>
                </a>
                <a href="{{ route('contact') }}" wire:navigate class="nav-link group relative px-4 py-2 text-white/90 hover:text-white font-semibold transition-all duration-300">
                    <span class="relative z-10">Contact</span>
                    <div class="absolute inset-0 bg-white/10 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-300 origin-center"></div>
                </a>
            </div>

            <!-- Mobile-Optimized Premium CTA Buttons -->
            <div class="hidden lg:flex items-center space-x-3">
                @auth
                    <!-- Notification Bell -->
                    <div class="mr-2">
                        <livewire:notification-bell />
                    </div>
                    <!-- Authenticated User Menu -->
                    <div class="relative" x-data="{ userMenuOpen: false }">
                        <button @click="userMenuOpen = !userMenuOpen" class="group relative flex items-center space-x-2 px-4 py-2 md:px-6 md:py-2.5 text-white/90 hover:text-white font-semibold transition-all duration-300 overflow-hidden text-sm md:text-base">
                            <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <span class="relative z-10">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': userMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                            <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-white/20 rounded-lg scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                        </button>

                        <!-- User Dropdown Menu -->
                        <div x-show="userMenuOpen"
                             @click.away="userMenuOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-1 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-1 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute right-0 mt-2 w-64 bg-white/95 backdrop-blur-xl rounded-xl shadow-xl border border-white/20 py-2 z-50">

                            <!-- User Info -->
                            <div class="px-4 py-3 border-b border-gray-200/20">
                                <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-600">{{ auth()->user()->email }}</p>
                                <p class="text-xs text-emerald-600 font-medium mt-1">{{ ucfirst(str_replace('_', ' ', auth()->user()->user_type)) }}</p>
                            </div>

                            <!-- Menu Items -->
                            <div class="py-2">
                                @if(auth()->user()->isCustomer())
                                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        </svg>
                                        My Dashboard
                                    </a>
                                    <a href="{{ route('customer.saved-properties') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        Saved Properties
                                    </a>
                                @elseif(auth()->user()->isAgent())
                                    <a href="{{ route('filament.agent.pages.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Agent Dashboard
                                    </a>
                                @elseif(auth()->user()->isPropertyOwner())
                                    <a href="{{ route('filament.landlord.pages.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                        Landlord Dashboard
                                    </a>
                                @endif

                                <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile
                                </a>
                            </div>

                            <!-- Logout -->
                            <div class="border-t border-gray-200/20 pt-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Guest User Buttons -->
                    <a href="{{ route('login') }}" class="group relative px-4 py-2 md:px-6 md:py-2.5 text-white/90 hover:text-white font-semibold transition-all duration-300 overflow-hidden text-sm md:text-base">
                        <span class="relative z-10">Sign In</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-white/20 rounded-lg scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                    </a>
                    <a href="{{ route('register') }}" class="nav-cta group relative px-4 py-2 md:px-8 md:py-3 bg-gradient-to-r from-emerald-500 to-blue-500 text-white font-bold rounded-lg md:rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 text-sm md:text-base">
                        <span class="relative z-10 flex items-center">
                            Get Started
                            <svg class="w-3 h-3 md:w-4 md:h-4 ml-1 md:ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-blue-600 scale-0 group-hover:scale-100 transition-transform duration-500 origin-center"></div>
                        <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="lg:hidden">
                <button @click="toggleMobileMenu" class="group relative p-2 text-white hover:bg-white/10 rounded-lg transition-colors duration-300">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Premium Mobile Menu -->
    <div x-show="mobileMenuOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 transform -translate-y-full scale-95"
         x-transition:enter-end="opacity-1 transform translate-y-0 scale-100" 
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-1 transform translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 transform -translate-y-full scale-95"
         class="lg:hidden fixed top-20 left-0 right-0 z-[999] bg-slate-900/90 backdrop-blur-xl border-t border-white/10 shadow-2xl">
        <div class="px-6 py-8 space-y-6">
            <!-- Mobile Navigation Links -->
            <div class="space-y-2">
                <a href="{{ route('properties.search') }}" wire:navigate class="mobile-nav-link group flex items-center py-3 px-4 text-white/90 hover:text-white font-semibold rounded-xl hover:bg-white/10 transition-all duration-300">
                    <span class="flex-1">Properties</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <a href="{{ route('agents') }}" wire:navigate class="mobile-nav-link group flex items-center py-3 px-4 text-white/90 hover:text-white font-semibold rounded-xl hover:bg-white/10 transition-all duration-300">
                    <span class="flex-1">Agents</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <a href="{{ route('agencies') }}" wire:navigate class="mobile-nav-link group flex items-center py-3 px-4 text-white/90 hover:text-white font-semibold rounded-xl hover:bg-white/10 transition-all duration-300">
                    <span class="flex-1">Agencies</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <a href="{{ route('about') }}" wire:navigate class="mobile-nav-link group flex items-center py-3 px-4 text-white/90 hover:text-white font-semibold rounded-xl hover:bg-white/10 transition-all duration-300">
                    <span class="flex-1">About</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <a href="{{ route('contact') }}" wire:navigate class="mobile-nav-link group flex items-center py-3 px-4 text-white/90 hover:text-white font-semibold rounded-xl hover:bg-white/10 transition-all duration-300">
                    <span class="flex-1">Contact</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            <!-- Mobile CTA Buttons -->
            <div class="pt-4 border-t border-white/10 space-y-3">
                @auth
                    <!-- Authenticated User Mobile Menu -->
                    <div class="text-center pb-3">
                        <div class="flex items-center justify-center space-x-3 mb-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <div class="text-left">
                                <p class="text-white font-semibold text-sm">{{ auth()->user()->name }}</p>
                                <p class="text-white/60 text-xs">{{ ucfirst(str_replace('_', ' ', auth()->user()->user_type)) }}</p>
                            </div>
                            <!-- Mobile Notification Bell -->
                            <div class="ml-auto">
                                <livewire:notification-bell />
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->isCustomer())
                        <a href="{{ route('dashboard') }}" class="block w-full py-3 text-center text-white font-semibold border-2 border-white/20 rounded-lg hover:bg-white/10 hover:border-white/30 transition-all duration-300 text-sm">
                            My Dashboard
                        </a>
                        <a href="{{ route('properties.search') }}" class="block w-full py-3 text-center bg-gradient-to-r from-emerald-500 to-blue-500 text-white font-bold rounded-lg hover:from-emerald-600 hover:to-blue-600 transition-all duration-300 shadow-lg hover:shadow-xl text-sm">
                            Saved Properties
                        </a>
                    @elseif(auth()->user()->isAgent())
                        <a href="{{ route('filament.agent.pages.dashboard') }}" class="block w-full py-3 text-center bg-gradient-to-r from-emerald-500 to-blue-500 text-white font-bold rounded-lg hover:from-emerald-600 hover:to-blue-600 transition-all duration-300 shadow-lg hover:shadow-xl text-sm">
                            Agent Dashboard
                        </a>
                    @elseif(auth()->user()->isPropertyOwner())
                        <a href="{{ route('filament.landlord.pages.dashboard') }}" class="block w-full py-3 text-center bg-gradient-to-r from-emerald-500 to-blue-500 text-white font-bold rounded-lg hover:from-emerald-600 hover:to-blue-600 transition-all duration-300 shadow-lg hover:shadow-xl text-sm">
                            Landlord Dashboard
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full py-3 text-center text-red-400 font-semibold border-2 border-red-400/20 rounded-lg hover:bg-red-500/10 hover:border-red-400/30 transition-all duration-300 text-sm">
                            Sign Out
                        </button>
                    </form>
                @else
                    <!-- Guest User Mobile Buttons -->
                    <a href="{{ route('login') }}" class="block w-full py-3 text-center text-white font-semibold border-2 border-white/20 rounded-lg hover:bg-white/10 hover:border-white/30 transition-all duration-300 text-sm">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="block w-full py-3 text-center bg-gradient-to-r from-emerald-500 to-blue-500 text-white font-bold rounded-lg hover:from-emerald-600 hover:to-blue-600 transition-all duration-300 shadow-lg hover:shadow-xl text-sm">
                        Get Started
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Navigation Spacer to prevent content overlap -->
<div class="h-20"></div>

<style>
    [x-cloak] {
        display: none !important;
    }
    
    .glass-nav {
        backdrop-filter: blur(16px);
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    /* Enhanced navigation animations */
    .nav-link::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 50%;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, #10b981, #3b82f6);
        transition: all 0.3s ease;
        transform: translateX(-50%);
    }
    
    .nav-link:hover::after {
        width: 100%;
    }
    
    /* Premium glow effects */
    @keyframes premium-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); }
        50% { box-shadow: 0 0 30px rgba(16, 185, 129, 0.5), 0 0 40px rgba(59, 130, 246, 0.3); }
    }
    
    .nav-cta:hover {
        animation: premium-glow 2s ease-in-out infinite;
    }
    
    /* Mobile nav stagger animation */
    .mobile-nav-link {
        opacity: 0;
        transform: translateX(-20px);
        animation: slideInLeft 0.3s ease-out forwards;
    }
    
    .mobile-nav-link:nth-child(1) { animation-delay: 0.1s; }
    .mobile-nav-link:nth-child(2) { animation-delay: 0.15s; }
    .mobile-nav-link:nth-child(3) { animation-delay: 0.2s; }
    .mobile-nav-link:nth-child(4) { animation-delay: 0.25s; }
    .mobile-nav-link:nth-child(5) { animation-delay: 0.3s; }
    
    @keyframes slideInLeft {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Enhanced glass morphism */
    nav::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
        backdrop-filter: blur(16px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        z-index: -1;
    }
</style>

<script>
    function navigationComponent() {
        return {
            mobileMenuOpen: false,

            toggleMobileMenu() {
                this.mobileMenuOpen = !this.mobileMenuOpen;
            },

            init() {
                // Enhanced navbar scroll effect
                let lastScrollY = window.scrollY;
                const navbar = document.getElementById('navbar');
                const navBackground = navbar.querySelector('.absolute.inset-0');
                
                window.addEventListener('scroll', () => {
                    const currentScrollY = window.scrollY;

                    // Enhance glass effect on scroll
                    if (currentScrollY > 50) {
                        navBackground.style.background = 'rgba(255, 255, 255, 0.08)';
                        navBackground.style.backdropFilter = 'blur(20px)';
                        navBackground.style.borderColor = 'rgba(255, 255, 255, 0.15)';
                    } else {
                        navBackground.style.background = 'rgba(255, 255, 255, 0.05)';
                        navBackground.style.backdropFilter = 'blur(16px)';
                        navBackground.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                    }

                    // Hide/show navbar on scroll direction
                    if (currentScrollY > lastScrollY && currentScrollY > 200) {
                        navbar.style.transform = 'translateY(-100%)';
                    } else {
                        navbar.style.transform = 'translateY(0)';
                    }

                    lastScrollY = currentScrollY;
                });

                // Enhanced navigation animations (fallback for GSAP)
                this.$nextTick(() => {
                    // Smooth reveal animation on load
                    if (typeof gsap !== 'undefined') {
                        gsap.fromTo(navbar,
                            { y: -100, opacity: 0 },
                            { y: 0, opacity: 1, duration: 1, ease: "power3.out", delay: 0.2 }
                        );
                    } else {
                        // Fallback CSS animation
                        navbar.style.animation = 'slideDown 0.8s ease-out';
                    }

                    // Logo hover animation with CSS fallback
                    const logo = document.querySelector('.group');
                    if (logo) {
                        const logoIcon = logo.querySelector('.relative > .relative');
                        if (logoIcon) {
                            logo.addEventListener('mouseenter', function() {
                                if (typeof gsap !== 'undefined') {
                                    gsap.to(logoIcon, {
                                        rotation: 5,
                                        scale: 1.05,
                                        duration: 0.3,
                                        ease: "back.out(1.7)"
                                    });
                                } else {
                                    logoIcon.style.transform = 'rotate(5deg) scale(1.05)';
                                    logoIcon.style.transition = 'transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                                }
                            });
                            logo.addEventListener('mouseleave', function() {
                                if (typeof gsap !== 'undefined') {
                                    gsap.to(logoIcon, {
                                        rotation: 0,
                                        scale: 1,
                                        duration: 0.3,
                                        ease: "power2.out"
                                    });
                                } else {
                                    logoIcon.style.transform = 'rotate(0deg) scale(1)';
                                }
                            });
                        }
                    }

                    // Enhanced CTA button animations with CSS fallback
                    document.querySelectorAll('.nav-cta').forEach(button => {
                        button.addEventListener('mouseenter', function() {
                            if (typeof gsap !== 'undefined') {
                                gsap.to(this, {
                                    scale: 1.05,
                                    duration: 0.3,
                                    ease: "back.out(1.7)"
                                });
                            } else {
                                this.style.transform = 'scale(1.05)';
                                this.style.transition = 'transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                            }
                        });
                        button.addEventListener('mouseleave', function() {
                            if (typeof gsap !== 'undefined') {
                                gsap.to(this, {
                                    scale: 1,
                                    duration: 0.3,
                                    ease: "power2.out"
                                });
                            } else {
                                this.style.transform = 'scale(1)';
                            }
                        });
                    });
                    
                    // Mobile menu animation
                    const mobileLinks = document.querySelectorAll('.mobile-nav-link');
                    if (mobileLinks.length > 0) {
                        mobileLinks.forEach((link, index) => {
                            link.addEventListener('click', () => {
                                this.mobileMenuOpen = false;
                            });
                        });
                    }
                });
                
                // Close mobile menu when clicking outside
                document.addEventListener('click', (e) => {
                    if (!navbar.contains(e.target) && this.mobileMenuOpen) {
                        this.mobileMenuOpen = false;
                    }
                });
                
                // Close mobile menu on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.mobileMenuOpen) {
                        this.mobileMenuOpen = false;
                    }
                });
            }
        }
    }
</script>
