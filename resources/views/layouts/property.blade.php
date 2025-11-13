<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Properties') - {{ config('app.name', 'HomeBaze') }}</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Find your perfect property from thousands of verified listings across Nigeria. Search by location, price, and amenities.">
    <meta name="keywords" content="Nigeria real estate search, property finder, houses for sale, apartments Lagos, Abuja properties">
    <meta name="author" content="HomeBaze">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Properties - HomeBaze">
    <meta property="og:description" content="Discover verified properties across Nigeria's most desirable locations.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/homebaze-og-image.jpg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- GSAP CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>


    <!-- Additional Head Content -->
    @stack('head')
</head>

<body class="font-inter antialiased bg-gray-900 overflow-x-hidden">
    <!-- Navigation Component (same as landing page) -->
    <!-- @include('components.landing.navigation') -->

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <!-- @include('components.landing.footer') -->

    <!-- Custom CSS -->
    <style>
        .font-inter {
            font-family: 'Inter', sans-serif;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1f2937;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #10b981, #3b82f6);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #059669, #2563eb);
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Page transition animations */
        .page-transition {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading states */
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Loading animation */
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
    </style>

    <!-- Custom JavaScript -->
    <script>
        // Register GSAP plugins
        gsap.registerPlugin(ScrollTrigger);

        document.addEventListener('DOMContentLoaded', function() {
            // Add page transition effect
            document.body.classList.add('page-transition');

            // Track property page views
            if (typeof gtag !== 'undefined') {
                gtag('event', 'page_view', {
                    page_title: 'Properties',
                    page_location: window.location.href
                });
            }

            // Auto-focus search input if present
            const searchInput = document.querySelector('input[name="q"]');
            if (searchInput && !searchInput.value) {
                setTimeout(() => searchInput.focus(), 100);
            }

            // Smooth scroll for pagination links
            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function() {
                    setTimeout(() => {
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }, 100);
                });
            });
        });

        // Search form enhancement
        function enhanceSearchForm() {
            const searchForm = document.querySelector('#search-form');
            if (searchForm) {
                searchForm.addEventListener('submit', function() {
                    // Add loading state to submit button
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Searching...';
                        submitBtn.disabled = true;
                    }
                });
            }
        }

        // Initialize search form enhancements
        document.addEventListener('DOMContentLoaded', enhanceSearchForm);

        // Global error handling
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
        });

        // Performance monitoring
        window.addEventListener('load', function() {
            if ('performance' in window) {
                const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
                console.log('Page load time:', loadTime + 'ms');
            }
        });

        // Accessibility enhancements
        document.addEventListener('keydown', function(e) {
            // Skip to main content with Alt+M
            if (e.altKey && e.key === 'm') {
                e.preventDefault();
                document.querySelector('main').focus();
            }
        });

        // Analytics tracking (replace with your analytics code)
        function trackEvent(category, action, label) {
            if (typeof gtag !== 'undefined') {
                gtag('event', action, {
                    event_category: category,
                    event_label: label
                });
            }
        }

        // Track property interactions
        document.addEventListener('DOMContentLoaded', function() {
            const propertyCards = document.querySelectorAll('[data-property-id]');
            propertyCards.forEach(card => {
                card.addEventListener('click', function() {
                    const propertyId = this.getAttribute('data-property-id');
                    trackEvent('Property', 'View', propertyId);
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
