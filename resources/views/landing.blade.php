<x-layouts.landing>
    <!-- Hero Section -->
    @include('components.landing.hero-premium')
    
    <!-- CTA Section -->
    @include('components.landing.cta-section')
    
    <!-- Featured Properties Section -->
    @include('components.landing.featured-properties-section')
    
    @push('scripts')
    <script>
        // Navigation functionality
        function toggleRegisterDropdown() {
            const menu = document.getElementById('register-menu');
            menu.classList.toggle('hidden');
        }

        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('register-dropdown');
            const menu = document.getElementById('register-menu');
            
            if (!dropdown.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // Counter animation
        function animateCounters() {
            const counters = document.querySelectorAll('[data-counter]');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-counter'));
                const duration = 2000; // 2 seconds
                const step = target / (duration / 16); // 60fps
                let current = 0;
                
                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    
                    // Format number with commas
                    const formatted = Math.floor(current).toLocaleString();
                    counter.textContent = formatted + (target >= 1000 ? '+' : '');
                }, 16);
            });
        }

        // GSAP Animations
        document.addEventListener('DOMContentLoaded', function() {
            // Register ScrollTrigger plugin
            gsap.registerPlugin(ScrollTrigger);

            // Hero section animation
            gsap.timeline()
                .from('#hero-section h1', {
                    duration: 1,
                    y: 50,
                    opacity: 0,
                    ease: 'power3.out'
                })
                .from('#hero-section p', {
                    duration: 0.8,
                    y: 30,
                    opacity: 0,
                    ease: 'power3.out'
                }, '-=0.5')
                .from('[data-counter]', {
                    duration: 0.6,
                    y: 20,
                    opacity: 0,
                    stagger: 0.1,
                    ease: 'power3.out',
                    onComplete: animateCounters
                }, '-=0.3')
                .from('.property-search-component', {
                    duration: 0.8,
                    y: 30,
                    opacity: 0,
                    ease: 'power3.out'
                }, '-=0.2');

            // CTA cards animation
            gsap.from('.cta-card', {
                scrollTrigger: {
                    trigger: '#cta-section',
                    start: 'top 80%'
                },
                duration: 0.8,
                y: 50,
                opacity: 0,
                stagger: 0.2,
                ease: 'power3.out'
            });

            // Property cards animation
            gsap.from('.property-card', {
                scrollTrigger: {
                    trigger: '#featured-properties',
                    start: 'top 80%'
                },
                duration: 0.6,
                y: 30,
                opacity: 0,
                stagger: 0.1,
                ease: 'power3.out'
            });

            // Navbar scroll effect
            const navbar = document.getElementById('main-navigation');
            let lastScrollY = window.scrollY;

            window.addEventListener('scroll', () => {
                if (window.scrollY > 100) {
                    navbar.classList.add('bg-white/95', 'backdrop-blur-sm');
                } else {
                    navbar.classList.remove('bg-white/95', 'backdrop-blur-sm');
                }
            });

            // Hover effects for CTA cards
            document.querySelectorAll('.cta-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    gsap.to(this, {
                        duration: 0.3,
                        y: -10,
                        scale: 1.02,
                        ease: 'power2.out'
                    });
                });

                card.addEventListener('mouseleave', function() {
                    gsap.to(this, {
                        duration: 0.3,
                        y: 0,
                        scale: 1,
                        ease: 'power2.out'
                    });
                });
            });

            // Property card hover effects
            document.querySelectorAll('.property-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    gsap.to(this, {
                        duration: 0.3,
                        y: -5,
                        scale: 1.01,
                        ease: 'power2.out'
                    });
                });

                card.addEventListener('mouseleave', function() {
                    gsap.to(this, {
                        duration: 0.3,
                        y: 0,
                        scale: 1,
                        ease: 'power2.out'
                    });
                });
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
    @endpush
</x-layouts.landing>
