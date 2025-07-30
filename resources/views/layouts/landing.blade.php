<!-- Landing Page Layout - resources/views/landing.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HomeBaze') }} - Where Homes Find You</title>

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Discover extraordinary properties across Nigeria's most desirable locations. Find homes for rent, sale, and lease with verified agents and agencies.">
    <meta name="keywords"
        content="Nigeria real estate, property rental, houses for sale, apartments Lagos, Abuja properties, real estate agents">
    <meta name="author" content="HomeBaze">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="HomeBaze - Where Homes Find You">
    <meta property="og:description" content="Nigeria's premier real estate platform. Find your perfect home today.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('images/homebaze-og-image.jpg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- GSAP CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Structured Data for SEO -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "RealEstateAgent",
        "name": "HomeBaze",
        "description": "Nigeria's premier real estate platform",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/homebaze-logo.png') }}",
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "Nigeria"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+234-XXX-XXX-XXXX",
            "contactType": "customer service"
        }
    }
    </script>
</head>

<body class="font-inter antialiased bg-gray-900 overflow-x-hidden">
    <!-- Page Loading Indicator -->
    <div id="page-loader" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900">
        <div class="flex flex-col items-center space-y-4">
            <div
                class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-blue-500 rounded-xl flex items-center justify-center animate-pulse">
                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
            </div>
            <div class="text-white font-medium">Loading HomeBaze...</div>
            <div class="w-32 h-1 bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-emerald-400 to-blue-500 rounded-full animate-pulse"
                    style="width: 100%;"></div>
            </div>
        </div>
    </div>

    <!-- Navigation Component -->
    @include('components.landing.navigation')

    <!-- Main Content -->
    @yield('content')
    
    <!-- Footer Section -->
    @include('components.landing.footer')

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

        /* Loading animation */
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
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

        // Page loader
        window.addEventListener('load', function() {
            gsap.to('#page-loader', {
                opacity: 0,
                duration: 0.5,
                onComplete: function() {
                    document.getElementById('page-loader').style.display = 'none';
                }
            });
        });

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
                document.getElementById('hero').focus();
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

        // Track property search
        document.addEventListener('DOMContentLoaded', function() {
            const searchForms = document.querySelectorAll('form[data-search="property"]');
            searchForms.forEach(form => {
                form.addEventListener('submit', function() {
                    trackEvent('Property', 'Search', 'Hero Form');
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
