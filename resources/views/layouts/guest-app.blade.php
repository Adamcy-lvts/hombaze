<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Guest Navigation -->
            <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('landing') }}" wire:navigate>
                                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
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
                                <x-nav-link :href="route('about')" :active="request()->routeIs('about')" wire:navigate>
                                    {{ __('About') }}
                                </x-nav-link>
                                <x-nav-link :href="route('contact')" :active="request()->routeIs('contact')" wire:navigate>
                                    {{ __('Contact') }}
                                </x-nav-link>
                            </div>
                        </div>

                        <!-- Authentication Links -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            @auth
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                            <div>{{ auth()->user()->name }}</div>

                                            <div class="ms-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('profile')" wire:navigate>
                                            {{ __('Profile') }}
                                        </x-dropdown-link>

                                        <!-- Authentication -->
                                        <button wire:click="logout" class="w-full text-start">
                                            <x-dropdown-link>
                                                {{ __('Log Out') }}
                                            </x-dropdown-link>
                                        </button>
                                    </x-slot>
                                </x-dropdown>
                            @else
                                <!-- Guest Links -->
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 text-sm font-medium">
                                        Sign In
                                    </a>
                                    <a href="{{ route('register') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Get Started
                                    </a>
                                </div>
                            @endauth
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
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
                        <x-responsive-nav-link :href="route('about')" :active="request()->routeIs('about')" wire:navigate>
                            {{ __('About') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('contact')" :active="request()->routeIs('contact')" wire:navigate>
                            {{ __('Contact') }}
                        </x-responsive-nav-link>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        @auth
                            <div class="px-4">
                                <div class="font-medium text-base text-gray-800">{{ auth()->user()->name }}</div>
                                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                                    {{ __('Profile') }}
                                </x-responsive-nav-link>

                                <!-- Authentication -->
                                <button wire:click="logout" class="w-full text-start">
                                    <x-responsive-nav-link>
                                        {{ __('Log Out') }}
                                    </x-responsive-nav-link>
                                </button>
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

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Toast Container -->
        <x-toast-container />

        @stack('scripts')
    </body>
</html>