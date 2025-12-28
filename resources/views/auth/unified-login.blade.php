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
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Left Side - Branding & Visuals (Premium Dark Theme) -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-slate-900">
            <!-- Background Image -->
            <div class="absolute inset-0">
                <img 
                    src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                    alt="Luxury Home" 
                    class="w-full h-full object-cover opacity-60"
                >
                <div class="absolute inset-0 bg-gradient-to-b from-slate-900/80 via-slate-900/60 to-slate-900/90"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 w-full flex flex-col justify-between p-12 text-white">
                <!-- Logo -->
                <div>
                    <a href="{{ route('landing') }}" class="flex items-center space-x-3 group">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-300">
                            <x-application-logo class="w-7 h-7 text-white" />
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-bold tracking-tight">HomeBaze</span>
                            <span class="text-xs text-emerald-400 font-medium tracking-widest uppercase">Premium Real Estate</span>
                        </div>
                    </a>
                </div>

                <!-- Feature Highlights -->
                <div class="space-y-8">
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Verified Listings</h3>
                            <p class="text-slate-300 text-sm leading-relaxed">Every property is vetted for authenticity and quality assurance.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Secure Transactions</h3>
                            <p class="text-slate-300 text-sm leading-relaxed">Bank-grade security for all your property transactions and data.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Fast & Efficient</h3>
                            <p class="text-slate-300 text-sm leading-relaxed">Connect with agents and owners instantly through our platform.</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Stats -->
                <div class="flex items-center justify-between border-t border-white/10 pt-8">
                    <div>
                        <div class="text-2xl font-bold">10k+</div>
                        <div class="text-xs text-slate-400 uppercase tracking-wider">Properties</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">500+</div>
                        <div class="text-xs text-slate-400 uppercase tracking-wider">Agents</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">50k+</div>
                        <div class="text-xs text-slate-400 uppercase tracking-wider">Users</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form (Light Theme) -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-4 sm:p-12 bg-gray-50 min-h-screen overflow-y-auto">
            <div class="w-full max-w-md space-y-8">
                <!-- Universal Logo -->
                <div class="text-center mb-10">
                    <a href="{{ route('landing') }}" class="inline-flex flex-col items-center group">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-105 transition-all duration-500 mb-4">
                            <x-application-logo class="w-9 h-9 text-white" />
                        </div>
                        <div class="flex flex-col items-center">
                            <span class="text-3xl font-black text-gray-900 tracking-tight leading-none">HomeBaze</span>
                            <span class="text-[10px] text-emerald-600 font-black uppercase tracking-[0.3em] mt-2">Premium Real Estate</span>
                        </div>
                    </a>
                </div>

                <!-- Header -->
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Welcome back</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Please enter your details to sign in
                    </p>
                </div>

                <!-- Status & Errors -->
                @if (session('status'))
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center text-emerald-700 text-sm">
                        <svg class="w-5 h-5 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center text-red-600 text-sm">
                                <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Form -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sm:p-8">
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email/Phone -->
                        <div>
                            <label for="credential" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email or Phone Number
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <input 
                                    id="credential" 
                                    name="credential" 
                                    type="text" 
                                    required 
                                    autofocus
                                    value="{{ old('credential') }}"
                                    class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-gray-900 placeholder-gray-400"
                                    placeholder="Enter your email or phone"
                                >
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input 
                                    id="password" 
                                    name="password" 
                                    type="password" 
                                    required 
                                    class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-gray-900 placeholder-gray-400"
                                    placeholder="Enter your password"
                                >
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg id="eyeOpen" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg id="eyeClosed" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Remember & Forgot -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                <span class="ml-2 text-sm text-gray-600">Remember me</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 transition-colors">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-emerald-500/30 transition-all duration-200 transform hover:-translate-y-0.5">
                            Sign In
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="mt-8 relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Don't have an account?</span>
                        </div>
                    </div>

                    <!-- Register Link -->
                    <div class="mt-6 text-center">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                            Create an account
                        </a>
                    </div>
                </div>

                <p class="text-center text-xs text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
