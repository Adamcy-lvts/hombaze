<!-- Search Page Navigation - Cleaner without hero overlap -->
<nav class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('landing') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-bold text-gray-900">HomeBaze</span>
                        <span class="hidden md:block text-xs text-emerald-600 font-medium">PREMIUM</span>
                    </div>
                </a>
            </div>

            <!-- Quick Search Bar (Mobile Hidden) -->
            <div class="hidden lg:flex flex-1 max-w-lg mx-8">
                <form method="GET" action="{{ route('properties.search') }}" class="w-full">
                    <div class="relative">
                        <input type="text" 
                               name="q" 
                               value="{{ request('q') }}"
                               placeholder="Quick search..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('landing') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200">
                    Home
                </a>
                <a href="{{ route('properties.search') }}" class="text-emerald-600 font-semibold">
                    Properties
                </a>
                <a href="#" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200">
                    Agents
                </a>
                <a href="#" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200">
                    Contact
                </a>
            </div>

            <!-- Auth Links -->
            <div class="flex items-center space-x-4">
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-emerald-600 transition-colors duration-200">
                            <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold text-emerald-600">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <span class="hidden md:block font-medium">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            @if(auth()->user()->isCustomer())
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">My Dashboard</a>
                                <a href="{{ route('properties.search') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Saved Properties</a>
                            @elseif(auth()->user()->isAgent())
                                <a href="{{ route('filament.agent.pages.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Agent Dashboard</a>
                            @elseif(auth()->user()->isPropertyOwner())
                                <a href="{{ route('filament.landlord.pages.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Landlord Dashboard</a>
                            @endif
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        Get Started
                    </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button type="button" 
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden p-2 rounded-lg text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        x-data="{ mobileMenuOpen: false }">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu (Hidden by default) -->
    <div x-show="mobileMenuOpen" 
         x-data="{ mobileMenuOpen: false }"
         @click.away="mobileMenuOpen = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="md:hidden bg-white border-t border-gray-200">
        <div class="px-4 py-4 space-y-3">
            <!-- Mobile Quick Search -->
            <form method="GET" action="{{ route('properties.search') }}" class="pb-3 border-b border-gray-200">
                <div class="relative">
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}"
                           placeholder="Search properties..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </form>

            <!-- Mobile Navigation Links -->
            <a href="{{ route('landing') }}" class="block text-gray-700 hover:text-emerald-600 font-medium py-2">Home</a>
            <a href="{{ route('properties.search') }}" class="block text-emerald-600 font-semibold py-2">Properties</a>
            <a href="#" class="block text-gray-700 hover:text-emerald-600 font-medium py-2">Agents</a>
            <a href="#" class="block text-gray-700 hover:text-emerald-600 font-medium py-2">Contact</a>
            
            @guest
                <hr class="my-2">
                <a href="{{ route('login') }}" class="block text-gray-700 hover:text-emerald-600 font-medium py-2">Sign In</a>
                <a href="{{ route('register') }}" class="block bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium text-center">Get Started</a>
            @endguest
        </div>
    </div>
</nav>
