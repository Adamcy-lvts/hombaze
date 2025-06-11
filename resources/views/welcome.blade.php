<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HomeBaze') }} - Nigeria's Premier Real Estate Platform</title>
    <meta name="description"
        content="Find your perfect home in Nigeria. Browse verified properties, connect with certified agents, and discover your dream property in Abuja, Kano, Kaduna, and Maiduguri.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- GSAP for animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
</head>

<body class="font-sans antialiased">
    @php
        // Get statistics for trust signals
        $stats = [
            'properties' => \App\Models\Property::where('is_published', true)->count() ?: 1250,
            'users' => \App\Models\User::count() ?: 850,
            'agents' => \App\Models\Agent::count() ?: 120,
            'agencies' => \App\Models\Agency::where('is_active', true)->count() ?: 45,
        ];
    @endphp

    <!-- Navigation -->
    @include('components.landing.navigation')

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        @include('components.landing.hero', ['stats' => $stats])

        <!-- CTA Section -->
        @include('components.landing.cta-section')

        <!-- Featured Properties -->
        @livewire('landing.featured-properties')

        <!-- Features Section -->
        @include('components.landing.features')
    </main>

    <!-- Footer -->
    @include('components.landing.footer')
</body>

</html>
