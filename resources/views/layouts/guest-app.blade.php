<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#059669">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="HomeBaze">
        <meta name="application-name" content="HomeBaze">
        <meta name="msapplication-TileColor" content="#059669">
        <meta name="msapplication-TileImage" content="/icons/icon-144x144.png">

        <!-- PWA Manifest -->
        <link rel="manifest" href="/manifest.json">

        <!-- PWA Icons -->
        <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-96x96.png">
        <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if (app()->environment('production'))
            @include('components.analytics.google-tag')
        @endif

        @stack('head')

        @stack('styles')

        <style>
            /* Critical Anti-FOUC & Smooth Page Load */
            .page-cloak { opacity: 0; }
            html { transition: opacity 0.3s ease-out; }
        </style>
        <script>
            // Add cloak class immediately to prevent splash
            document.documentElement.classList.add('page-cloak');

            let revealed = false;
            function reveal() { 
                if (revealed) return;
                revealed = true;
                
                requestAnimationFrame(() => {
                    document.documentElement.classList.remove('page-cloak');
                    document.documentElement.style.opacity = '1';
                });
            }

            // Initial load
            if(document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', reveal);
            } else {
                reveal();
            }

            // Safety timeout (500ms)
            setTimeout(reveal, 500);

            // Livewire navigation support
            document.addEventListener('livewire:navigated', reveal);
            document.addEventListener('livewire:load', reveal);
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Guest Navigation -->
            @if (!($hideNav ?? false))
            <nav x-data="{ open: false }" class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-gray-100 transition-all duration-300">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center relative z-50">
                                <style>
                                    @keyframes building-rise {
                                        0% { transform: translateY(110%); opacity: 0; }
                                        100% { transform: translateY(0); opacity: 1; }
                                    }
                                    .animate-building-rise {
                                        opacity: 0; /* Star Hidden to prevent flash */
                                        animation: building-rise 1.2s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; /* Elastic fade-in */
                                    }
                                </style>
                                <a href="{{ route('landing') }}" wire:navigate class="group relative overflow-hidden p-1">
                                    <!-- Glow Effect -->
                                    <div class="absolute -inset-2 bg-emerald-500/20 rounded-full blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                    
                                    <!-- Animated Container -->
                                    <div class="animate-building-rise">
                                        <x-application-logo style="height: 2.5rem;" class="relative block h-10 w-auto fill-current text-emerald-600 transition-all duration-500 transform group-hover:scale-110 group-hover:text-emerald-500" />
                                    </div>
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <x-nav-link :href="route('properties.search')" :active="request()->routeIs('properties.search')" wire:navigate>
                                    {{ __('Properties') }}
                                </x-nav-link>
                                <x-nav-link :href="route('agents')" :active="request()->routeIs('agents')" wire:navigate>
                                    {{ __('Agents') }}
                                </x-nav-link>
                                <x-nav-link :href="route('agencies')" :active="request()->routeIs('agencies')" wire:navigate>
                                    {{ __('Agencies') }}
                                </x-nav-link>
                                <x-nav-link :href="route('pricing')" :active="request()->routeIs('pricing')" wire:navigate>
                                    {{ __('Pricing') }}
                                </x-nav-link>
                                {{-- <x-nav-link :href="route('about')" :active="request()->routeIs('about')" wire:navigate>
                                    {{ __('About') }}
                                </x-nav-link>
                                <x-nav-link :href="route('contact')" :active="request()->routeIs('contact')" wire:navigate>
                                    {{ __('Contact') }}
                                </x-nav-link> --}}
                            </div>
                        </div>

                        <!-- Authentication Links -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            @auth
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-hidden transition ease-in-out duration-150">
                                            <div>{{ auth()->user()->name }}</div>

                                            <div class="ms-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <x-dropdown-link :href="auth()->user()->getPanelDashboardUrl()" wire:navigate>
                                            {{ __('Dashboard') }}
                                        </x-dropdown-link>

                                        <x-dropdown-link :href="auth()->user()->getPanelProfileUrl()" wire:navigate>
                                            {{ __('Profile') }}
                                        </x-dropdown-link>

                                        <!-- Authentication -->
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-start">
                                                <x-dropdown-link>
                                                    {{ __('Log Out') }}
                                                </x-dropdown-link>
                                            </button>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            @else
                                <!-- Guest Links -->
                                <div class="flex items-center space-x-1">
                                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-emerald-600 px-4 py-2.5 text-sm font-semibold transition-colors duration-200">
                                        Sign In
                                    </a>
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 rounded-full text-sm font-bold shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 transform hover:-translate-y-0.5 transition-all duration-200">
                                        Get Started
                                    </a>
                                </div>
                            @endauth
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <x-responsive-nav-link :href="route('properties.search')" :active="request()->routeIs('properties.search')" wire:navigate>
                            {{ __('Properties') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('agents')" :active="request()->routeIs('agents')" wire:navigate>
                            {{ __('Agents') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('agencies')" :active="request()->routeIs('agencies')" wire:navigate>
                            {{ __('Agencies') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('pricing')" :active="request()->routeIs('pricing')" wire:navigate>
                            {{ __('Pricing') }}
                        </x-responsive-nav-link>
                        {{-- <x-responsive-nav-link :href="route('about')" :active="request()->routeIs('about')" wire:navigate>
                            {{ __('About') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('contact')" :active="request()->routeIs('contact')" wire:navigate>
                            {{ __('Contact') }}
                        </x-responsive-nav-link> --}}
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        @auth
                            <div class="px-4">
                                <div class="font-medium text-base text-gray-800">{{ auth()->user()->name }}</div>
                                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <x-responsive-nav-link :href="auth()->user()->getPanelDashboardUrl()" wire:navigate>
                                    {{ __('Dashboard') }}
                                </x-responsive-nav-link>

                                <x-responsive-nav-link :href="auth()->user()->getPanelProfileUrl()" wire:navigate>
                                    {{ __('Profile') }}
                                </x-responsive-nav-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-start">
                                        <x-responsive-nav-link>
                                            {{ __('Log Out') }}
                                        </x-responsive-nav-link>
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="space-y-1 px-4">
                                <a href="{{ route('login') }}" class="block w-full text-start px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Sign In
                                </a>
                                <a href="{{ route('register') }}" class="block w-full text-start px-4 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                                    Get Started
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </nav>
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow-sm">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>

        <!-- Toast Container -->
        <x-toast-container />

        <!-- PWA Install Prompt -->
        <x-pwa-install-prompt />

        @stack('scripts')
    </body>
</html>
