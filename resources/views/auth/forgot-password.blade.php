<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HomeBaze') }} - Reset Password</title>

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
                src="https://images.unsplash.com/photo-1582407947304-fd86f028f716?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                alt="Modern Real Estate" 
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

                <!-- Security Features -->
                <div class="space-y-6 mb-12">
                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-lg">Secure Reset</h3>
                                <p class="text-white/70 text-sm">Your password reset is encrypted and secure</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-lg">Email Verification</h3>
                                <p class="text-white/70 text-sm">We'll send a secure link to your email</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-lg">Quick Process</h3>
                                <p class="text-white/70 text-sm">Get back to your account in minutes</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help Text -->
                <div class="text-center">
                    <p class="text-white/60 text-sm mb-4">Need immediate help?</p>
                    <a href="{{ route('contact') }}" class="text-emerald-400 hover:text-emerald-300 font-semibold transition-colors duration-300">
                        Contact Support →
                    </a>
                </div>
            </div>

            <!-- Right Side - Reset Password Form -->
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

                <!-- Reset Password Card -->
                <div class="bg-white/10 backdrop-blur-2xl border border-white/20 rounded-3xl shadow-2xl p-8 sm:p-10">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                        <h1 class="text-3xl lg:text-4xl font-black text-white mb-3">
                            Reset Password
                        </h1>
                        <p class="text-white/70 text-lg">We'll send you a secure link to reset your password</p>
                    </div>

                    <!-- Info Message -->
                    <div class="mb-6 bg-blue-500/20 backdrop-blur-xl border border-blue-400/30 rounded-2xl p-4">
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-blue-500/20 rounded-lg flex items-center justify-center mr-3 mt-0.5">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-blue-200 text-sm font-medium leading-relaxed">
                                Forgot your password? No problem. Just enter your email address and we'll email you a password reset link that will allow you to choose a new one.
                            </p>
                        </div>
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

                    <!-- Reset Password Form -->
                    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-bold text-white mb-3">
                                Email Address
                            </label>
                            <div class="relative group">
                                <input 
                                    id="email" 
                                    name="email" 
                                    type="email" 
                                    required 
                                    autofocus
                                    value="{{ old('email') }}"
                                    placeholder="Enter your email address"
                                    class="w-full pl-14 pr-4 py-4 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 text-white placeholder-white/40 transition-all duration-300 group-hover:bg-white/15 focus:bg-white/15"
                                >
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <div class="w-6 h-6 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            class="group w-full relative bg-gradient-to-r from-emerald-500 to-blue-500 hover:from-emerald-600 hover:to-blue-600 text-white font-bold py-4 px-6 rounded-2xl transition-all duration-500 transform hover:scale-105 shadow-xl hover:shadow-2xl overflow-hidden"
                        >
                            <span class="relative z-10 flex items-center justify-center">
                                Send Password Reset Link
                                <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
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

                    <!-- Back to Login -->
                    <div class="text-center">
                        <p class="text-white/70 text-lg">
                            Remember your password? 
                            <a href="{{ route('login') }}" class="font-bold text-emerald-400 hover:text-emerald-300 transition-colors duration-300">
                                Sign in here
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
</body>
</html>