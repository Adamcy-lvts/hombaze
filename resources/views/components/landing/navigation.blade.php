<!-- Premium Navigation with Glass Morphism -->
<nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" id="main-navigation">
    <div class="bg-black/20 backdrop-blur-2xl border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Premium Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="group flex items-center space-x-3">
                        <!-- Logo Icon -->
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <!-- Brand Text -->
                        <div class="text-2xl font-bold">
                            <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">Home</span><span class="text-white">Baze</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation with Premium Styling -->
                <div class="hidden md:block">
                    <div class="flex items-center space-x-2">
                        <a href="#" class="nav-link group px-6 py-3 rounded-2xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium backdrop-blur-sm border border-transparent hover:border-white/20">
                            <span class="relative">
                                Browse
                                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-400 to-purple-400 group-hover:w-full transition-all duration-300"></span>
                            </span>
                        </a>
                        <a href="#" class="nav-link group px-6 py-3 rounded-2xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium backdrop-blur-sm border border-transparent hover:border-white/20">
                            <span class="relative">
                                About
                                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-400 to-purple-400 group-hover:w-full transition-all duration-300"></span>
                            </span>
                        </a>
                        <a href="#" class="nav-link group px-6 py-3 rounded-2xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium backdrop-blur-sm border border-transparent hover:border-white/20">
                            <span class="relative">
                                Contact
                                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-400 to-purple-400 group-hover:w-full transition-all duration-300"></span>
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Premium Auth Links -->
                <div class="hidden md:flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="px-6 py-3 rounded-2xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium backdrop-blur-sm border border-white/20 hover:border-white/40">
                            Login
                        </a>
                        <div class="relative" id="register-dropdown">
                            <button class="group relative overflow-hidden bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-2xl font-semibold transition-all duration-300 transform hover:scale-105 flex items-center shadow-lg hover:shadow-xl" onclick="toggleRegisterDropdown()">
                                <!-- Animated Background -->
                                <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                
                                <!-- Button Content -->
                                <span class="relative mr-2">Register</span>
                                <svg class="relative w-4 h-4 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                
                                <!-- Shine Effect -->
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                            </button>
                            <div class="absolute right-0 mt-3 w-64 bg-black/80 backdrop-blur-2xl rounded-2xl shadow-2xl border border-white/20 hidden transform opacity-0 scale-95 transition-all duration-300" id="register-menu">
                                <div class="p-2">
                                    <a href="/tenant/register" class="group flex items-center px-4 py-3 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-300">
                                        <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium">Find Home</div>
                                            <div class="text-xs text-white/60">Join as Tenant</div>
                                        </div>
                                    </a>
                                    <a href="/landlord/register" class="group flex items-center px-4 py-3 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-300">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium">List Property</div>
                                            <div class="text-xs text-white/60">Join as Landlord</div>
                                        </div>
                                    </a>
                                    <a href="/agent/register" class="group flex items-center px-4 py-3 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-300">
                                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 6V8a2 2 0 00-2-2H10a2 2 0 00-2 2v8a2 2 0 002 2h4a2 2 0 002-2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium">Join as Agent</div>
                                            <div class="text-xs text-white/60">Professional Agent</div>
                                        </div>
                                    </a>
                                    <a href="/agency/register" class="group flex items-center px-4 py-3 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-300">
                                        <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-300">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium">Register Agency</div>
                                            <div class="text-xs text-white/60">Business Account</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="/dashboard" class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-2xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            Dashboard
                        </a>
                    @endguest
                </div>

                <!-- Premium Mobile menu button -->
                <div class="md:hidden">
                    <button class="p-3 rounded-2xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-300 backdrop-blur-sm border border-white/20" onclick="toggleMobileMenu()">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
            </div>
        </div>

        <!-- Premium Mobile menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="bg-black/90 backdrop-blur-2xl border-t border-white/10">
                <div class="px-4 py-6 space-y-3">
                    <a href="#" class="block px-4 py-3 rounded-2xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium">Browse</a>
                    <a href="#" class="block px-4 py-3 rounded-2xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium">About</a>
                    <a href="#" class="block px-4 py-3 rounded-2xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium">Contact</a>
                    
                    @guest
                        <div class="pt-4 border-t border-white/10">
                            <a href="{{ route('login') }}" class="block px-4 py-3 rounded-2xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium mb-3">Login</a>
                            
                            <div class="space-y-2">
                                <p class="px-4 py-2 text-sm font-semibold text-white/60">Join as:</p>
                                <a href="/tenant/register" class="flex items-center px-4 py-3 rounded-2xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-300">
                                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium">Tenant</div>
                                        <div class="text-xs text-white/60">Find your home</div>
                                    </div>
                                </a>
                                <a href="/landlord/register" class="flex items-center px-4 py-3 rounded-2xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-300">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium">Landlord</div>
                                        <div class="text-xs text-white/60">List properties</div>
                                    </div>
                                </a>
                                <a href="/agent/register" class="flex items-center px-4 py-3 rounded-2xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-300">
                                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 6V8a2 2 0 00-2-2H10a2 2 0 00-2 2v8a2 2 0 002 2h4a2 2 0 002-2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium">Agent</div>
                                        <div class="text-xs text-white/60">Join our team</div>
                                    </div>
                                </a>
                                <a href="/agency/register" class="flex items-center px-4 py-3 rounded-2xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-300">
                                    <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium">Agency</div>
                                        <div class="text-xs text-white/60">Business account</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="pt-4 border-t border-white/10">
                            <a href="/dashboard" class="block px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-2xl font-semibold transition-all duration-300">Dashboard</a>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</nav>