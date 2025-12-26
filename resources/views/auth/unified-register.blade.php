<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HomeBaze') }} - Register</title>

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
                    src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=2075&q=80" 
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Verified Properties</h3>
                            <p class="text-slate-300 text-sm leading-relaxed">All properties undergo rigorous verification for your safety.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Expert Agents</h3>
                            <p class="text-slate-300 text-sm leading-relaxed">Work with licensed professionals who know the market.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">Prime Locations</h3>
                            <p class="text-slate-300 text-sm leading-relaxed">Access exclusive properties in the most desirable neighborhoods.</p>
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

        <!-- Right Side - Register Form (Light Theme) -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-gray-50 overflow-y-auto h-screen">
            <div class="w-full max-w-xl space-y-6">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <a href="{{ route('landing') }}" class="inline-flex items-center space-x-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                            <x-application-logo class="w-6 h-6 text-white" />
                        </div>
                        <span class="text-xl font-bold text-gray-900">HomeBaze</span>
                    </a>
                </div>

                <!-- Header -->
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Create Account</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Join thousands of users finding their dream homes
                    </p>
                </div>

                <!-- Form -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <form method="POST" action="{{ route('unified.register') }}" class="space-y-5">
                        @csrf

                        <!-- User Type Selection -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">I am a...</label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($userTypes as $type => $config)
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="user_type" value="{{ $type }}" class="peer sr-only" {{ old('user_type') === $type || ($type === 'customer' && !old('user_type')) ? 'checked' : '' }}>
                                    <div class="p-3 rounded-xl border border-gray-200 bg-gray-50 hover:bg-white hover:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:ring-1 peer-checked:ring-emerald-500 transition-all duration-200 flex flex-col items-center text-center h-full">
                                        <div class="text-sm font-bold text-gray-900 peer-checked:text-emerald-700">{{ $config['label'] }}</div>
                                        <div class="text-xs text-gray-500 mt-1 peer-checked:text-emerald-600/80">{{ $config['description'] }}</div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('user_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name</label>
                            <input id="name" name="name" type="text" required value="{{ old('name') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-gray-900 placeholder-gray-400" placeholder="John Doe">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email & Phone Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                                <input id="email" name="email" type="email" required value="{{ old('email') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-gray-900 placeholder-gray-400" placeholder="john@example.com">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">Phone Number</label>
                                <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-gray-900 placeholder-gray-400" placeholder="+234...">
                                @error('phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-gray-900 placeholder-gray-400" placeholder="Create a password">
                                <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                            <div class="relative">
                                <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors text-gray-900 placeholder-gray-400" placeholder="Confirm password">
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-emerald-500/30 transition-all duration-200 transform hover:-translate-y-0.5">
                            Create Account
                        </button>
                    </form>

                    <!-- Login Link -->
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
                                Sign in here
                            </a>
                        </p>
                    </div>
                </div>
                
                <p class="text-center text-xs text-gray-400 pb-8">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }
    </script>
</body>
</html>
