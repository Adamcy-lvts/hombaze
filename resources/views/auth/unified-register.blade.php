<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HomeBaze') }} - Register</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Premium Animations -->
    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        
        /* Glass morphism enhancements */
        .backdrop-blur-xl {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .backdrop-blur-md {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        
        /* Premium focus states */
        input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        /* Full page background */
        .full-bg {
            background-image: url('https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2075&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        /* Custom scrollbar for mobile */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        /* Radio button peer styles fix */
        input[type="radio"]:checked + label {
            background: rgba(255, 255, 255, 0.1) !important;
            border-color: rgb(96 165 250) !important;
            box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.5) !important;
        }
        
        input[type="radio"]:checked + label .w-10,
        input[type="radio"]:checked + label .w-8 {
            background: linear-gradient(135deg, rgb(59 130 246), rgb(147 51 234)) !important;
        }
        
        input[type="radio"]:checked + label svg {
            color: white !important;
        }
        
        input[type="radio"]:checked + label .w-4.h-4.border-2 {
            border-color: rgb(96 165 250) !important;
            background: linear-gradient(135deg, rgb(59 130 246), rgb(147 51 234)) !important;
        }
        
        input[type="radio"]:checked + label .w-4.h-4.border-2 .absolute {
            background: white !important;
            opacity: 1 !important;
        }
    </style>
</head>
<body class="font-sans antialiased full-bg">
    <div class="min-h-screen flex">
        <!-- Left Section - Branding -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <!-- Darker overlay for better text readability -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/85 via-blue-900/75 to-indigo-900/85"></div>
            
            <!-- Premium Glass Border -->
            <div class="absolute inset-0 border-r border-white/20"></div>
            
            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-center px-8 lg:px-12 py-12 text-white w-full">
                <!-- Premium Logo -->
                <div class="mb-12">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-400 to-blue-500 rounded-3xl blur-md opacity-75"></div>
                            <div class="relative w-16 h-16 bg-gradient-to-br from-emerald-500 to-blue-600 rounded-3xl flex items-center justify-center shadow-2xl">
                                <svg class="w-9 h-9 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-5xl font-black text-white tracking-tight mb-1">HomeBaze</h1>
                            <p class="text-white/70 text-lg font-medium tracking-widest">PREMIUM REAL ESTATE</p>
                        </div>
                    </div>
                    <p class="text-white/80 text-xl leading-relaxed">Your Gateway to Nigeria's Premier Real Estate Platform</p>
                </div>

                <!-- Premium Features List -->
                <div class="space-y-8 mb-12">
                    <div class="group flex items-start space-x-5 p-4 rounded-2xl backdrop-blur-sm bg-white/10 border border-white/10 hover:bg-white/15 transition-all duration-300">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500/20 to-blue-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2 text-white">Verified Properties</h3>
                            <p class="text-white/70 leading-relaxed">All properties undergo rigorous verification and authentication for your safety and peace of mind</p>
                        </div>
                    </div>

                    <div class="group flex items-start space-x-5 p-4 rounded-2xl backdrop-blur-sm bg-white/10 border border-white/10 hover:bg-white/15 transition-all duration-300">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2 text-white">Verified Agents</h3>
                            <p class="text-white/70 leading-relaxed">Work with licensed and verified real estate professionals who meet our strict quality standards</p>
                        </div>
                    </div>

                    <div class="group flex items-start space-x-5 p-4 rounded-2xl backdrop-blur-sm bg-white/10 border border-white/10 hover:bg-white/15 transition-all duration-300">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2 text-white">Prime Locations</h3>
                            <p class="text-white/70 leading-relaxed">Access exclusive properties in Nigeria's most prestigious and desirable neighborhoods</p>
                        </div>
                    </div>

                    <div class="group flex items-start space-x-5 p-4 rounded-2xl backdrop-blur-sm bg-white/10 border border-white/10 hover:bg-white/15 transition-all duration-300">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-500/20 to-red-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2 text-white">Document Verification</h3>
                            <p class="text-white/70 leading-relaxed">Advanced document verification and legal compliance ensuring secure transactions</p>
                        </div>
                    </div>
                </div>

                <!-- Premium Bottom Text -->
                <div class="backdrop-blur-sm bg-white/10 rounded-2xl border border-white/10 p-6">
                    <p class="text-white/80 text-lg leading-relaxed text-center">
                        <span class="font-semibold text-white">Join thousands</span> of property professionals and owners on Nigeria's most trusted real estate platform.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Section - Registration Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24 relative overflow-hidden">
            <!-- Darker overlay for better text readability -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/90 via-slate-800/80 to-slate-900/90"></div>
            
            <!-- Premium Background Effects -->
            <div class="absolute inset-0">
                <div class="absolute top-0 -left-4 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-blob"></div>
                <div class="absolute top-0 -right-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-8 left-20 w-72 h-72 bg-emerald-500 rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-blob animation-delay-4000"></div>
            </div>

            <div class="relative z-10 mx-auto w-full max-w-sm lg:max-w-2xl xl:max-w-3xl">
                <!-- Mobile Logo -->
                <div class="text-center mb-8 lg:hidden">
                    <div class="flex items-center justify-center space-x-3 mb-4">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-400 to-blue-500 rounded-2xl blur-sm opacity-75"></div>
                            <div class="relative w-12 h-12 bg-gradient-to-br from-emerald-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-black text-white tracking-tight">HomeBaze</span>
                            <span class="text-xs text-white/60 font-medium tracking-widest">PREMIUM</span>
                        </div>
                    </div>
                </div>

                <!-- Glass Morphism Form Container -->
                <div class="backdrop-blur-xl bg-white/10 rounded-3xl border border-white/20 shadow-2xl p-8 lg:p-12 xl:p-16 relative overflow-hidden">
                    <!-- Subtle Inner Glow -->
                    <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent rounded-3xl"></div>
                    
                    <div class="relative z-10">
                        <!-- Form Header -->
                        <div class="text-center mb-8">
                            <h2 class="text-2xl font-bold text-white mb-2">Create Your Account</h2>
                            <p class="text-white/70">Choose your account type to get started</p>
                        </div>

                        <!-- Registration Form -->
                        <form method="POST" action="{{ route('unified.register') }}" class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                            @csrf

                            <!-- User Type Selection -->
                            <div class="col-span-full">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-white/90 mb-2">Account Type</label>
                                    <p class="text-xs text-white/60 mb-4">Please select the type of account you want to create</p>
                                </div>
                                
                                <!-- Desktop Layout (3 columns) -->
                                <div class="hidden lg:grid lg:grid-cols-3 gap-4">
                                    @foreach($userTypes as $type => $config)
                                    <div class="relative">
                                        <input type="radio" 
                                               name="user_type" 
                                               value="{{ $type }}" 
                                               id="user_type_{{ $type }}"
                                               class="peer sr-only" 
                                               {{ old('user_type') === $type ? 'checked' : '' }}>
                                        <label for="user_type_{{ $type }}" 
                                               class="flex flex-col items-center p-4 backdrop-blur-md bg-white/5 border border-white/10 rounded-xl cursor-pointer hover:bg-white/10 hover:border-white/20 peer-checked:bg-white/10 peer-checked:border-blue-400 peer-checked:ring-2 peer-checked:ring-blue-400/50 transition-all duration-300 group h-full min-h-[120px]">
                                            <div class="w-10 h-10 bg-gradient-to-br from-white/10 to-white/5 rounded-lg flex items-center justify-center peer-checked:from-blue-500 peer-checked:to-purple-600 transition-all duration-300 group-hover:scale-105 mb-3">
                                                <svg class="w-5 h-5 text-white/70 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($type === 'agent')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    @elseif($type === 'property_owner')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    @endif
                                                </svg>
                                            </div>
                                            <div class="text-center">
                                                <div class="font-semibold text-white text-sm mb-1">{{ $config['label'] }}</div>
                                                <div class="text-xs text-white/60 leading-tight">{{ $config['description'] }}</div>
                                            </div>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Mobile Layout (Compact horizontal) -->
                                <div class="lg:hidden space-y-3">
                                    @foreach($userTypes as $type => $config)
                                    <div class="relative">
                                        <input type="radio" 
                                               name="user_type" 
                                               value="{{ $type }}" 
                                               id="user_type_mobile_{{ $type }}"
                                               class="peer sr-only" 
                                               {{ old('user_type') === $type ? 'checked' : '' }}>
                                        <label for="user_type_mobile_{{ $type }}" 
                                               class="flex items-center p-3 backdrop-blur-md bg-white/5 border border-white/10 rounded-lg cursor-pointer hover:bg-white/10 hover:border-white/20 peer-checked:bg-white/10 peer-checked:border-blue-400 peer-checked:ring-2 peer-checked:ring-blue-400/50 transition-all duration-300 group">
                                            <div class="w-8 h-8 bg-gradient-to-br from-white/10 to-white/5 rounded-lg flex items-center justify-center peer-checked:from-blue-500 peer-checked:to-purple-600 transition-all duration-300 mr-3 flex-shrink-0">
                                                <svg class="w-4 h-4 text-white/70 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($type === 'agent')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    @elseif($type === 'property_owner')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    @endif
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-white text-sm">{{ $config['label'] }}</div>
                                                <div class="text-xs text-white/60 leading-tight mt-0.5">{{ $config['description'] }}</div>
                                            </div>
                                            <div class="flex-shrink-0 ml-3">
                                                <div class="w-4 h-4 border-2 border-white/30 rounded-full peer-checked:border-blue-400 peer-checked:bg-gradient-to-br peer-checked:from-blue-500 peer-checked:to-purple-600 transition-all duration-300 relative">
                                                    <div class="absolute inset-0.5 bg-white/10 rounded-full peer-checked:bg-white opacity-0 peer-checked:opacity-100 transition-opacity duration-300"></div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>

                                @error('user_type')
                                    <div class="mt-3 flex items-center space-x-2 text-red-300">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-sm">{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div class="lg:col-span-2">
                                <label for="name" class="block text-sm font-medium text-white/90 mb-2">Full Name</label>
                                <input id="name" name="name" type="text" required 
                                       value="{{ old('name') }}"
                                       class="w-full px-4 py-3 backdrop-blur-md bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all duration-300"
                                       placeholder="Enter your full name">
                                @error('name')
                                    <div class="mt-2 flex items-center space-x-2 text-red-300">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-sm">{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-white/90 mb-2">Email Address</label>
                                <input id="email" name="email" type="email" required 
                                       value="{{ old('email') }}"
                                       class="w-full px-4 py-3 backdrop-blur-md bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all duration-300"
                                       placeholder="Enter your email address">
                                @error('email')
                                    <div class="mt-2 flex items-center space-x-2 text-red-300">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-sm">{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-white/90 mb-2">Phone Number</label>
                                <input id="phone" name="phone" type="tel" required 
                                       value="{{ old('phone') }}"
                                       class="w-full px-4 py-3 backdrop-blur-md bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all duration-300"
                                       placeholder="Enter your phone number">
                                @error('phone')
                                    <div class="mt-2 flex items-center space-x-2 text-red-300">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-sm">{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-white/90 mb-2">Password</label>
                                <div class="relative">
                                    <input id="password" name="password" type="password" required 
                                           class="w-full px-4 py-3 pr-12 backdrop-blur-md bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all duration-300"
                                           placeholder="Create a secure password">
                                    <button type="button" id="togglePassword" 
                                            class="absolute inset-y-0 right-0 pr-4 flex items-center hover:scale-110 transition-transform duration-200">
                                        <svg id="eyeIcon" class="h-5 w-5 text-white/50 hover:text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <svg id="eyeOffIcon" class="h-5 w-5 text-white/50 hover:text-white/80 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12m-3.122-3.122L21 21"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="mt-2 flex items-center space-x-2 text-red-300">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-sm">{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-white/90 mb-2">Confirm Password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required 
                                       class="w-full px-4 py-3 backdrop-blur-md bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all duration-300"
                                       placeholder="Confirm your password">
                                @error('password_confirmation')
                                    <div class="mt-2 flex items-center space-x-2 text-red-300">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-sm">{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>

                            <!-- General Registration Error -->
                            @error('registration')
                                <div class="col-span-full backdrop-blur-md bg-red-500/20 border border-red-400/30 rounded-xl p-4">
                                    <div class="flex items-center space-x-2 text-red-200">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <p class="text-sm font-medium">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror

                            <!-- Submit Button -->
                            <div class="col-span-full">
                                <button type="submit" 
                                        class="w-full relative py-4 px-6 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-blue-500/50 transition-all duration-300 group overflow-hidden">
                                    <span class="relative z-10 flex items-center justify-center">
                                        Create Account
                                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                    </span>
                                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-700 scale-0 group-hover:scale-100 transition-transform duration-500 origin-center"></div>
                                </button>
                            </div>

                            <!-- Sign In Link -->
                            <div class="col-span-full text-center pt-6 border-t border-white/10">
                                <p class="text-sm text-white/70">
                                    Already have an account?
                                    <a href="{{ route('login') }}" class="font-semibold text-blue-300 hover:text-blue-200 transition-colors duration-300">
                                        Sign in
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');

            if (togglePassword && passwordInput && eyeIcon && eyeOffIcon) {
                togglePassword.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    if (type === 'password') {
                        eyeIcon.classList.remove('hidden');
                        eyeOffIcon.classList.add('hidden');
                    } else {
                        eyeIcon.classList.add('hidden');
                        eyeOffIcon.classList.remove('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>