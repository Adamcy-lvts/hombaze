<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HomeBaze') }} - Tenant Invitation</title>

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
                src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                alt="Modern Apartment Interior" 
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
                        <div>
                            <h1 class="text-3xl font-black text-white">HomeBaze</h1>
                            <p class="text-white/70 text-sm font-medium">Premium Real Estate Platform</p>
                        </div>
                    </a>

                    <!-- Invitation Welcome Message -->
                    <div class="mb-8">
                        <h2 class="text-4xl lg:text-5xl font-black text-white mb-4 leading-tight">
                            Welcome to Your<br>
                            <span class="bg-gradient-to-r from-emerald-400 to-blue-400 bg-clip-text text-transparent">
                                New Home Journey
                            </span>
                        </h2>
                        <p class="text-white/80 text-lg leading-relaxed">
                            You've been personally invited by {{ $invitation->landlord->name }} to join HomeBaze's exclusive tenant platform. 
                            Experience premium property management at its finest.
                        </p>
                    </div>

                    <!-- Feature Cards -->
                    <div class="space-y-4">
                        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-white font-bold text-lg">Secure Access</h3>
                                    <p class="text-white/70 text-sm">Invitation-only tenant portal</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-white font-bold text-lg">Direct Communication</h3>
                                    <p class="text-white/70 text-sm">Chat with your landlord instantly</p>
                                </div>
                            </div>
                        </div>

                        @if($invitation->property)
                        <div class="bg-gradient-to-r from-emerald-500/20 to-blue-500/20 backdrop-blur-xl border border-emerald-400/30 rounded-2xl p-6 shadow-2xl">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-emerald-500/30 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-white font-bold text-lg">Your Property</h3>
                                    <p class="text-emerald-200 text-sm font-medium">{{ $invitation->property->title }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-1">100%</div>
                        <div class="text-white/60 text-sm font-medium">Verified</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-1">24/7</div>
                        <div class="text-white/60 text-sm font-medium">Support</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-1">5★</div>
                        <div class="text-white/60 text-sm font-medium">Experience</div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Registration Form -->
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

                <!-- Registration Card -->
                <div class="bg-white/10 backdrop-blur-2xl border border-white/20 rounded-3xl shadow-2xl p-8 sm:p-10">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-emerald-500/20 to-blue-500/20 rounded-2xl mb-4">
                            <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                        </div>
                        <h1 class="text-3xl lg:text-4xl font-black text-white mb-3">
                            You're Invited!
                        </h1>
                        <p class="text-white/70 text-lg">Join HomeBaze as {{ $invitation->landlord->name }}'s tenant</p>
                    </div>

                    @if($invitation->message)
                        <div class="mb-6 bg-emerald-500/20 backdrop-blur-xl border border-emerald-400/30 rounded-2xl p-4">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                <p class="text-emerald-200 text-sm font-medium italic">"{{ $invitation->message }}"</p>
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

                    <!-- Registration Form -->
                    <div id="registrationForm">
                        <form method="POST" action="{{ route('tenant.invitation.register', $invitation->token) }}" class="space-y-6">
                            @csrf

                            <!-- Full Name -->
                            <div>
                                <label for="name" class="block text-sm font-bold text-white mb-3">
                                    Full Name
                                </label>
                                <div class="relative group">
                                    <input 
                                        id="name" 
                                        type="text" 
                                        name="name" 
                                        value="{{ old('name') }}"
                                        required 
                                        autofocus
                                        autocomplete="name"
                                        class="w-full px-4 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-white/50 focus:border-emerald-400/50 focus:bg-white/20 focus:outline-none focus:ring-2 focus:ring-emerald-400/20 transition-all duration-300 backdrop-blur-xl"
                                        placeholder="Enter your full name"
                                    >
                                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-emerald-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                                </div>
                            </div>

                            <!-- Email (read-only) -->
                            <div>
                                <label for="email" class="block text-sm font-bold text-white mb-3">
                                    Email Address
                                </label>
                                <div class="relative">
                                    <input 
                                        id="email" 
                                        type="email" 
                                        name="email" 
                                        value="{{ $invitation->email }}"
                                        readonly
                                        class="w-full px-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white/70 placeholder-white/30 cursor-not-allowed backdrop-blur-xl"
                                    >
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                        <svg class="w-5 h-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-white/50 text-xs mt-2">This email was provided by your landlord</p>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-bold text-white mb-3">
                                    Phone Number
                                </label>
                                <div class="relative group">
                                    <input 
                                        id="phone" 
                                        type="text" 
                                        name="phone" 
                                        value="{{ old('phone') }}"
                                        required 
                                        autocomplete="tel"
                                        class="w-full px-4 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-white/50 focus:border-emerald-400/50 focus:bg-white/20 focus:outline-none focus:ring-2 focus:ring-emerald-400/20 transition-all duration-300 backdrop-blur-xl"
                                        placeholder="Enter your phone number"
                                    >
                                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-emerald-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
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
                                        type="password" 
                                        name="password" 
                                        required 
                                        autocomplete="new-password"
                                        class="w-full px-4 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-white/50 focus:border-emerald-400/50 focus:bg-white/20 focus:outline-none focus:ring-2 focus:ring-emerald-400/20 transition-all duration-300 backdrop-blur-xl"
                                        placeholder="Create a secure password"
                                    >
                                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-emerald-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-bold text-white mb-3">
                                    Confirm Password
                                </label>
                                <div class="relative group">
                                    <input 
                                        id="password_confirmation" 
                                        type="password" 
                                        name="password_confirmation" 
                                        required 
                                        autocomplete="new-password"
                                        class="w-full px-4 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-white/50 focus:border-emerald-400/50 focus:bg-white/20 focus:outline-none focus:ring-2 focus:ring-emerald-400/20 transition-all duration-300 backdrop-blur-xl"
                                        placeholder="Confirm your password"
                                    >
                                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-emerald-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button 
                                type="submit"
                                class="w-full relative group overflow-hidden bg-gradient-to-r from-emerald-500 to-blue-600 hover:from-emerald-600 hover:to-blue-700 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-emerald-400/50 shadow-xl hover:shadow-2xl"
                            >
                                <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <span class="relative flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                    <span>Create Tenant Account</span>
                                </span>
                            </button>
                        </form>

                        <!-- Already have account toggle -->
                        <div class="mt-8 text-center">
                            <p class="text-white/60 text-sm mb-3">Already have an account with this email?</p>
                            <button 
                                onclick="showLoginForm()" 
                                class="text-emerald-400 hover:text-emerald-300 font-semibold text-sm transition-colors duration-200 underline decoration-emerald-400/50 hover:decoration-emerald-300"
                            >
                                Login instead
                            </button>
                        </div>
                    </div>

                    <!-- Login Form (hidden by default) -->
                    <div id="loginForm" class="hidden">
                        <div class="text-center mb-6">
                            <h2 class="text-2xl font-black text-white mb-2">Welcome Back</h2>
                            <p class="text-white/70">Login to accept your invitation</p>
                        </div>

                        <form method="POST" action="{{ route('tenant.invitation.login', $invitation->token) }}" class="space-y-6">
                            @csrf
                            
                            <!-- Login Password -->
                            <div>
                                <label for="login_password" class="block text-sm font-bold text-white mb-3">
                                    Password
                                </label>
                                <div class="relative group">
                                    <input 
                                        id="login_password" 
                                        type="password" 
                                        name="password" 
                                        required
                                        class="w-full px-4 py-4 bg-white/10 border border-white/20 rounded-2xl text-white placeholder-white/50 focus:border-emerald-400/50 focus:bg-white/20 focus:outline-none focus:ring-2 focus:ring-emerald-400/20 transition-all duration-300 backdrop-blur-xl"
                                        placeholder="Enter your password"
                                    >
                                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-emerald-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                                </div>
                            </div>

                            <!-- Login Button -->
                            <button 
                                type="submit"
                                class="w-full relative group overflow-hidden bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold py-4 px-8 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-400/50 shadow-xl hover:shadow-2xl"
                            >
                                <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <span class="relative flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span>Login and Accept Invitation</span>
                                </span>
                            </button>
                        </form>
                        
                        <!-- Back to registration -->
                        <div class="mt-6 text-center">
                            <button 
                                onclick="hideLoginForm()" 
                                class="text-white/60 hover:text-white font-medium text-sm transition-colors duration-200"
                            >
                                ← Create new account instead
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showLoginForm() {
            document.getElementById('registrationForm').classList.add('hidden');
            document.getElementById('loginForm').classList.remove('hidden');
        }
        
        function hideLoginForm() {
            document.getElementById('loginForm').classList.add('hidden');
            document.getElementById('registrationForm').classList.remove('hidden');
        }
    </script>
</body>
</html>