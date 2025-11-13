<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Properties' }} - {{ config('app.name', 'HomeBaze') }}</title>

    <!-- SEO Meta Tags -->
    @if(isset($ogData))
    <meta name="description" content="{{ $ogData['description'] }}">
    <meta name="keywords" content="{{ $ogData['property_type'] }}, {{ $ogData['location'] }}, Nigeria real estate, HomeBaze, property for sale">
    @else
    <meta name="description" content="Find your perfect property from thousands of verified listings across Nigeria. Search by location, price, and amenities.">
    <meta name="keywords" content="Nigeria real estate search, property finder, houses for sale, apartments Lagos, Abuja properties">
    @endif
    <meta name="author" content="HomeBaze">

    <!-- Open Graph Meta Tags for WhatsApp Link Previews -->
    @if(isset($ogData))
    <meta property="og:title" content="{{ $ogData['title'] }} - HomeBaze">
    <meta property="og:description" content="{{ $ogData['description'] }}">
    <meta property="og:type" content="{{ $ogData['type'] }}">
    <meta property="og:url" content="{{ $ogData['url'] }}">
    <meta property="og:image" content="{{ $ogData['image'] }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="{{ $ogData['site_name'] }}">

    <!-- WhatsApp Specific Meta Tags -->
    <meta property="og:image:alt" content="{{ $ogData['title'] }} - Property in {{ $ogData['location'] }}">

    <!-- Property Specific Meta Tags -->
    <meta property="product:price:amount" content="{{ $ogData['price'] }}">
    <meta property="product:price:currency" content="{{ $ogData['currency'] }}">
    <meta property="og:locality" content="{{ $ogData['location'] }}">

    <!-- Twitter Card Tags for better social sharing -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogData['title'] }} - HomeBaze">
    <meta name="twitter:description" content="{{ $ogData['description'] }}">
    <meta name="twitter:image" content="{{ $ogData['image'] }}">
    @else
    <meta property="og:title" content="Properties - HomeBaze">
    <meta property="og:description" content="Discover verified properties across Nigeria's most desirable locations.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/homebaze-og-image.jpg') }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @if (app()->environment('production'))
    @include('components.analytics.google-tag')
    @endif

    <!-- GSAP CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>



    <!-- Additional Head Content -->
    @stack('head')
</head>

<body class="font-inter antialiased bg-gray-900 overflow-x-hidden">



    <!-- Main Content -->
    <main class="min-h-screen">
        {{ $slot }}
    </main>




  

    @livewireScripts



</body>

</html>