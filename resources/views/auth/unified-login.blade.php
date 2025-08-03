<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HomeBaze') }} - Premium Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <!-- Premium Real Estate Background -->
    <div class="min-h-screen relative overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/95 via-slate-900/90 to-blue-900/95 z-10"></div>
            <img 
                src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                alt="Beautiful Luxury Mansion" 
                class="w-full h-full object-cover"
            >
        </div>

        <!-- Premium Background Elements -->
        <div class="absolute inset-0 z-20">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-emerald-500/20 to-blue-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-br from-blue-500/15 to-indigo-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 right-1/4 w-64 h-64 bg-gradient-to-br from-purple-500/10 to-pink-500/5 rounded-full blur-2xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-30 min-h-screen flex">
            <!-- Left Side - Branding & Info -->
            <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 flex-col justify-between p-12">
                <!-- Logo & Brand -->
                <div class="mb-12">
                    <a href="{{ route('landing') }}" class="group flex items-center space-x-4 mb-8">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-400 to-blue-500 rounded-2xl blur-sm opacity-75 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="relative w-14 h-14 bg-gradient-to-br from-emerald-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-xl group-hover:shadow-2xl transition-all duration-500">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-3xl font-black text-white tracking-tight">HomeBaze</span>
                            <span class="text-sm text-white/60 font-medium tracking-widest">PREMIUM REAL ESTATE</span>
                        </div>
                    </a>
                </div>

                <!-- Feature Cards -->
                <div class="space-y-6 mb-12">
                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-lg">Verified Properties</h3>
                                <p class="text-white/70 text-sm">All listings are verified for authenticity</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-lg">Verified Agents</h3>
                                <p class="text-white/70 text-sm">Licensed and certified professionals</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-lg">Prime Locations</h3>
                                <p class="text-white/70 text-sm">Premium areas across Nigeria</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-indigo-500/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-lg">Document Verification</h3>
                                <p class="text-white/70 text-sm">Secure property documentation</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-1">10K+</div>
                        <div class="text-white/60 text-sm font-medium">Properties</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-1">500+</div>
                        <div class="text-white/60 text-sm font-medium">Agents</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-1">50K+</div>
                        <div class="text-white/60 text-sm font-medium">Happy Clients</div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="w-full lg:w-1/2 xl:w-2/5 flex flex-col justify-center px-6 sm:px-12 lg:px-16">
                <!-- Mobile Logo -->
                <div class="lg:hidden mb-8 text-center">
                    <a href="{{ route('landing') }}" class="inline-flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                        </div>
                        <span class="text-2xl font-black text-white">HomeBaze</span>
                    </a>
                </div>

                <!-- Login Card -->
                <div class="bg-white/10 backdrop-blur-2xl border border-white/20 rounded-3xl shadow-2xl p-8 sm:p-10">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <h1 class="text-3xl lg:text-4xl font-black text-white mb-3">
                            Welcome Back
                        </h1>
                        <p class="text-white/70 text-lg">Sign in to access your premium dashboard</p>
                    </div>

                    <!-- Status Messages -->
                    @if (session('status'))
                        <div class="mb-6 bg-emerald-500/20 backdrop-blur-xl border border-emerald-400/30 rounded-2xl p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-emerald-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-emerald-200 text-sm font-medium">{{ session('status') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-500/20 backdrop-blur-xl border border-red-400/30 rounded-2xl p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    @foreach ($errors->all() as $error)
                                        <p class="text-red-200 text-sm font-medium">{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email or Phone -->
                        <div>
                            <label for="credential" class="block text-sm font-bold text-white mb-3">
                                Email or Phone Number
                            </label>
                            <div class="relative group">
                                <input 
                                    id="credential" 
                                    name="credential" 
                                    type="text" 
                                    required 
                                    autofocus
                                    value="{{ old('credential') }}"
                                    placeholder="Enter your email or phone number"
                                    class="w-full pl-14 pr-4 py-4 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 text-white placeholder-white/40 transition-all duration-300 group-hover:bg-white/15 focus:bg-white/15"
                                >
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <div class="w-6 h-6 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-bold text-white mb-3">
                                Password
                            </label>
                            <div class="relative group">
                                <input 
                                    id="password" 
                                    name="password" 
                                    type="password"
                                    required
                                    placeholder="Enter your password"
                                    class="w-full pl-14 pr-14 py-4 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 text-white placeholder-white/40 transition-all duration-300 group-hover:bg-white/15 focus:bg-white/15"
                                >
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <div class="w-6 h-6 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Reveal Password Button -->
                                <button 
                                    type="button"
                                    id="togglePassword"
                                    class="absolute inset-y-0 right-0 pr-5 flex items-center cursor-pointer group/eye"
                                >
                                    <div class="w-6 h-6 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover/eye:bg-purple-500/30 transition-colors duration-300">
                                        <!-- Eye Open Icon -->
                                        <svg id="eyeOpen" class="w-4 h-4 text-purple-400 transition-opacity duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <!-- Eye Closed Icon -->
                                        <svg id="eyeClosed" class="w-4 h-4 text-purple-400 transition-opacity duration-200 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                        </svg>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center group cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="remember" 
                                    class="w-5 h-5 text-emerald-500 border-white/20 bg-white/10 rounded focus:ring-emerald-500 focus:ring-2"
                                >
                                <span class="ml-3 text-sm text-white/80 font-medium group-hover:text-white transition-colors">Remember me</span>
                            </label>

                            <a href="{{ route('password.request') }}" class="text-sm text-emerald-400 hover:text-emerald-300 font-semibold transition-colors duration-300">
                                Forgot password?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            class="group w-full relative bg-gradient-to-r from-emerald-500 to-blue-500 hover:from-emerald-600 hover:to-blue-600 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-500 transform hover:scale-105 shadow-xl hover:shadow-2xl overflow-hidden"
                        >
                            <span class="relative z-10 flex items-center justify-center">
                                Sign In to Dashboard
                                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-blue-600 scale-0 group-hover:scale-100 transition-transform duration-500 origin-center"></div>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="my-8 flex items-center">
                        <div class="flex-1 border-t border-white/20"></div>
                        <div class="px-4 text-sm text-white/60 font-medium">or</div>
                        <div class="flex-1 border-t border-white/20"></div>
                    </div>

                    <!-- Register Link -->
                    <div class="text-center">
                        <p class="text-white/70 text-lg">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="font-bold text-emerald-400 hover:text-emerald-300 transition-colors duration-300">
                                Create one here
                            </a>
                        </p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-white/50 text-sm">&copy; {{ date('Y') }} HomeBaze. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Animation Styles -->
    <style>
        @keyframes premium-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); }
            50% { box-shadow: 0 0 30px rgba(16, 185, 129, 0.5), 0 0 40px rgba(59, 130, 246, 0.3); }
        }
        
        .group:hover .w-14 {
            animation: premium-glow 2s ease-in-out infinite;
        }
        
        /* Smooth transitions for all interactive elements */
        input:focus {
            transform: translateY(-1px);
        }
        
        button:active {
            transform: scale(0.98);
        }
    </style>

    <!-- Password Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');
            
            if (togglePassword && passwordInput && eyeOpen && eyeClosed) {
                togglePassword.addEventListener('click', function() {
                    // Toggle password visibility
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';
                    
                    // Toggle eye icons
                    if (isPassword) {
                        eyeOpen.classList.add('hidden');
                        eyeClosed.classList.remove('hidden');
                    } else {
                        eyeOpen.classList.remove('hidden');
                        eyeClosed.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>