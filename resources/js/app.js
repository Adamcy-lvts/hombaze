import './bootstrap';
import './invitation-clipboard';

// Alpine.js Components
document.addEventListener('alpine:init', () => {

    Alpine.data('currencyInput', (config = {}) => ({
        rawValue: '',

        // Configuration
        thousands: config.thousands || ',',
        min: config.min || null,
        max: config.max || null,
        wireModel: config.wireModel || null,

        init() {
            // Initialize with existing value
            const initialValue = this.$el.value || '';
            this.rawValue = initialValue.replace(/[^\d]/g, '');

            // Format if there's a value
            if (this.rawValue) {
                this.formatDisplay();
            }
        },

        formatDisplay() {
            let formattedValue = '';
            if (this.rawValue) {
                formattedValue = this.rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, this.thousands);
            }

            if (this.$el.value !== formattedValue) {
                const cursorPos = this.$el.selectionStart || 0;
                const oldLength = this.$el.value.length;

                this.$el.value = formattedValue;

                // Restore cursor position if focused
                if (document.activeElement === this.$el && this.$el.setSelectionRange) {
                    const newLength = formattedValue.length;
                    const newCursorPos = Math.max(0, Math.min(cursorPos + (newLength - oldLength), formattedValue.length));
                    this.$el.setSelectionRange(newCursorPos, newCursorPos);
                }
            }
        },

        handleInput(event) {
            const inputValue = event.target.value;
            const newRawValue = inputValue.replace(/[^\d]/g, '');

            // Apply max constraint
            if (this.max && newRawValue && parseInt(newRawValue) > this.max) {
                return;
            }

            this.rawValue = newRawValue;

            // Update Livewire
            if (this.wireModel && this.$wire) {
                this.$wire[this.wireModel] = this.rawValue;
            }

            this.formatDisplay();
        },

        handleBlur() {
            // Apply min validation only if there's a value
            if (this.rawValue && this.min && parseInt(this.rawValue) < this.min) {
                this.rawValue = this.min.toString();

                if (this.wireModel && this.$wire) {
                    this.$wire[this.wireModel] = this.rawValue;
                }

                this.formatDisplay();
            }
        },

        handleKeydown(event) {
            // Allow navigation and control keys
            const allowedKeys = [8, 9, 27, 13, 46, 35, 36, 37, 38, 39, 40];
            const isControlKey = event.ctrlKey && [65, 67, 86, 88, 90].includes(event.keyCode);

            if (allowedKeys.includes(event.keyCode) || isControlKey) {
                return;
            }

            // Only allow numeric input
            const isNumeric = (event.keyCode >= 48 && event.keyCode <= 57) ||
                             (event.keyCode >= 96 && event.keyCode <= 105);

            if (!isNumeric) {
                event.preventDefault();
            }
        }
    }));
});

// GSAP Landing Page Animations and Interactions
document.addEventListener('DOMContentLoaded', function () {
    // Check if GSAP is loaded
    if (typeof gsap !== 'undefined') {
        // Register GSAP plugins
        gsap.registerPlugin(ScrollTrigger);

        // Hero Section Animations
        const heroTimeline = gsap.timeline();

        // Only animate if elements exist
        if (document.querySelector('.hero-title')) {
            heroTimeline
                .from('.hero-title', {
                    duration: 1,
                    y: 50,
                    opacity: 0,
                    ease: 'power2.out'
                })
                .from('.hero-subtitle', {
                    duration: 0.8,
                    y: 30,
                    opacity: 0,
                    ease: 'power2.out'
                }, '-=0.5')
                .from('.hero-search', {
                    duration: 0.8,
                    y: 30,
                    opacity: 0,
                    ease: 'power2.out'
                }, '-=0.3')
                .from('.hero-stats', {
                    duration: 0.8,
                    y: 20,
                    opacity: 0,
                    ease: 'power2.out'
                }, '-=0.2');
        }

        // Scroll-triggered animations
        gsap.utils.toArray('.animate-on-scroll').forEach(element => {
            gsap.from(element, {
                y: 50,
                opacity: 0,
                duration: 0.8,
                ease: 'power2.out',
                scrollTrigger: {
                    trigger: element,
                    start: 'top 85%',
                    end: 'bottom 15%',
                    toggleActions: 'play none none reverse'
                }
            });
        });

        // CTA Cards hover animations
        gsap.utils.toArray('.cta-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                gsap.to(card, {
                    duration: 0.3,
                    y: -8,
                    scale: 1.02,
                    ease: 'power2.out'
                });
            });

            card.addEventListener('mouseleave', () => {
                gsap.to(card, {
                    duration: 0.3,
                    y: 0,
                    scale: 1,
                    ease: 'power2.out'
                });
            });
        });

        // Feature cards stagger animation
        if (document.querySelector('.feature-card')) {
            gsap.from('.feature-card', {
                duration: 0.6,
                y: 30,
                opacity: 0,
                stagger: 0.1,
                ease: 'power2.out',
                scrollTrigger: {
                    trigger: '.features-section',
                    start: 'top 80%'
                }
            });
        }

        // Statistics counter animation
        gsap.utils.toArray('.stat-number').forEach(stat => {
            ScrollTrigger.create({
                trigger: stat,
                start: 'top 80%',
                onEnter: () => {
                    const endValue = parseInt(stat.textContent.replace(/,/g, ''));
                    const duration = 2;

                    gsap.fromTo(stat, {
                        textContent: 0
                    }, {
                        textContent: endValue,
                        duration: duration,
                        ease: 'power2.out',
                        snap: { textContent: 1 },
                        stagger: 0.1,
                        onUpdate: function () {
                            stat.textContent = Math.ceil(this.targets()[0].textContent).toLocaleString();
                        }
                    });
                }
            });
        });
    }

    // Mobile Menu Toggle (vanilla JS)
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', toggleMobileMenu);
    }

    function toggleMobileMenu() {
        const isOpen = !mobileMenu.classList.contains('hidden');

        if (isOpen) {
            // Close menu
            if (typeof gsap !== 'undefined') {
                gsap.to(mobileMenu, {
                    duration: 0.3,
                    opacity: 0,
                    y: -10,
                    ease: 'power2.out',
                    onComplete: () => mobileMenu.classList.add('hidden')
                });
            } else {
                mobileMenu.classList.add('hidden');
            }
        } else {
            // Open menu
            mobileMenu.classList.remove('hidden');
            if (typeof gsap !== 'undefined') {
                gsap.fromTo(mobileMenu,
                    { opacity: 0, y: -10 },
                    { duration: 0.3, opacity: 1, y: 0, ease: 'power2.out' }
                );
            }
        }
    }

    // Property Search Form Handling
    const searchForm = document.getElementById('property-search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const params = new URLSearchParams();

            // Only add non-empty values to URL params
            for (let [key, value] of formData.entries()) {
                if (value && value.trim() !== '') {
                    params.append(key, value);
                }
            }

            // Redirect to search results
            window.location.href = `/properties?${params.toString()}`;
        });
    }

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

    // Featured Properties Section Animations
    if (document.querySelector('#featured-properties')) {
        // Immediately ensure all property cards are visible
        gsap.set('.property-card', { opacity: 1, clearProps: 'transform' });
        // Header animation
        if (document.querySelector('[data-animate="header"]')) {
            gsap.from('[data-animate="header"]', {
                duration: 1,
                y: 50,
                opacity: 0,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: '#featured-properties',
                    start: 'top 80%'
                }
            });
        }

        // Property cards grid animation
        if (document.querySelector('[data-animate="grid"]')) {
            gsap.from('.property-card', {
                duration: 0.8,
                y: 60,
                opacity: 0,
                stagger: 0.15,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: '[data-animate="grid"]',
                    start: 'top 85%'
                },
                onComplete: function () {
                    // Ensure all cards are fully visible after animation
                    gsap.set('.property-card', { opacity: 1 });
                }
            });
        } else {
            // Fallback: ensure cards are visible if no animation trigger
            gsap.set('.property-card', { opacity: 1 });
        }

        // CTA animation
        if (document.querySelector('[data-animate="cta"]')) {
            gsap.from('[data-animate="cta"]', {
                duration: 0.8,
                y: 30,
                opacity: 0,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: '[data-animate="cta"]',
                    start: 'top 90%'
                }
            });
        }

        // 3D Perspective Carousel with Direct Transforms
        const carousel = document.getElementById('properties-carousel');
        const prevBtn = document.getElementById('carousel-prev');
        const nextBtn = document.getElementById('carousel-next');
        const dotsContainer = document.getElementById('carousel-dots');

        if (carousel && prevBtn && nextBtn && dotsContainer) {
            const cards = carousel.querySelectorAll('.property-card');
            const totalCards = cards.length;
            let currentIndex = 0;

            // Track zoom state for center card
            let isZoomed = false;

            // 3D Transform positions - direct inline style approach (larger scale)
            const getTransform = (position, centerZoom = false) => {
                switch (position) {
                    case 0: // Center - main card
                        const scale = centerZoom ? '1.35' : '1.2';
                        return {
                            transform: `translate(-50%, -50%) translateZ(0px) scale(${scale})`,
                            opacity: '1',
                            zIndex: '20'
                        };
                    case 1: // Right side
                        return {
                            transform: 'translate(-50%, -50%) translateZ(-150px) translateX(250px) rotateY(-25deg) scale(0.9)',
                            opacity: '0.8',
                            zIndex: '15'
                        };
                    case -1: // Left side
                        return {
                            transform: 'translate(-50%, -50%) translateZ(-150px) translateX(-250px) rotateY(25deg) scale(0.9)',
                            opacity: '0.8',
                            zIndex: '15'
                        };
                    case 2: // Far right
                        return {
                            transform: 'translate(-50%, -50%) translateZ(-300px) translateX(400px) rotateY(-35deg) scale(0.75)',
                            opacity: '0.6',
                            zIndex: '10'
                        };
                    case -2: // Far left
                        return {
                            transform: 'translate(-50%, -50%) translateZ(-300px) translateX(-400px) rotateY(35deg) scale(0.75)',
                            opacity: '0.6',
                            zIndex: '10'
                        };
                    default: // Hidden
                        return {
                            transform: 'translate(-50%, -50%) translateZ(-500px) translateX(550px) rotateY(45deg) scale(0.5)',
                            opacity: '0.3',
                            zIndex: '5'
                        };
                }
            };

            // Create pagination dots
            const createDots = () => {
                dotsContainer.innerHTML = '';

                for (let i = 0; i < totalCards; i++) {
                    const dot = document.createElement('button');
                    dot.className = `w-3 h-3 rounded-full transition-all duration-300 ${i === 0 ? 'bg-blue-600' : 'bg-gray-300'}`;
                    dot.addEventListener('click', () => goToSlide(i));
                    dotsContainer.appendChild(dot);
                }
            };

            // Update 3D carousel positions with direct transforms
            const updateCarousel = () => {
                cards.forEach((card, index) => {
                    // Calculate relative position to current center
                    let position = index - currentIndex;

                    // Wrap positions for circular effect
                    if (position > 2) position -= totalCards;
                    if (position < -2) position += totalCards;

                    // Get transform styles for this position (with zoom state for center)
                    const styles = getTransform(position, position === 0 && isZoomed);

                    // Apply styles directly - CSS transition handles the animation
                    card.style.setProperty('transform', styles.transform, 'important');
                    card.style.setProperty('opacity', styles.opacity, 'important');
                    card.style.setProperty('z-index', styles.zIndex, 'important');

                    // Add pointer cursor for all cards
                    card.style.cursor = 'pointer';

                    // Toggle center-card class for enhanced glassmorphism
                    if (position === 0) {
                        card.classList.add('center-card');
                    } else {
                        card.classList.remove('center-card');
                    }
                });

                // Update dots
                const dots = dotsContainer.querySelectorAll('button');
                dots.forEach((dot, index) => {
                    dot.className = `w-3 h-3 rounded-full transition-all duration-300 ${index === currentIndex ? 'bg-blue-600' : 'bg-gray-300'}`;
                });
            };

            // Toggle zoom for center card
            const toggleZoom = () => {
                isZoomed = !isZoomed;
                updateCarousel();
            };

            // Reset zoom when navigating
            const resetZoom = () => {
                if (isZoomed) {
                    isZoomed = false;
                }
            };

            // Go to specific slide
            const goToSlide = (index) => {
                resetZoom();
                currentIndex = index;
                updateCarousel();
            };

            // Next slide
            const nextSlide = () => {
                resetZoom();
                currentIndex = (currentIndex + 1) % totalCards;
                updateCarousel();
            };

            // Previous slide
            const prevSlide = () => {
                resetZoom();
                currentIndex = (currentIndex - 1 + totalCards) % totalCards;
                updateCarousel();
            };

            // Event listeners
            nextBtn.addEventListener('click', nextSlide);
            prevBtn.addEventListener('click', prevSlide);

            // Touch support
            let startX = 0;
            let startY = 0;
            let isDragging = false;

            carousel.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
                isDragging = true;
            });

            carousel.addEventListener('touchmove', (e) => {
                if (!isDragging) return;

                const deltaX = Math.abs(e.touches[0].clientX - startX);
                const deltaY = Math.abs(e.touches[0].clientY - startY);

                if (deltaX > deltaY && deltaX > 10) {
                    e.preventDefault();
                }
            });

            carousel.addEventListener('touchend', (e) => {
                if (!isDragging) return;
                isDragging = false;

                const endX = e.changedTouches[0].clientX;
                const diffX = startX - endX;

                if (Math.abs(diffX) > 50) {
                    if (diffX > 0) {
                        nextSlide();
                    } else {
                        prevSlide();
                    }
                }
            });

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') prevSlide();
                if (e.key === 'ArrowRight') nextSlide();
            });

            // Click on cards - center card zooms, side cards navigate
            cards.forEach((card, index) => {
                card.addEventListener('click', (e) => {
                    e.preventDefault();

                    // Check if this card is currently in the center position
                    if (index === currentIndex) {
                        // Center card - toggle zoom
                        toggleZoom();
                    } else {
                        // Side card - navigate to center
                        goToSlide(index);
                    }
                });
            });

            // Simple initialization - CSS provides good default, JS enhances
            const initializeCarousel = () => {
                // Reset state
                currentIndex = 0;
                isZoomed = false;

                // Initialize UI
                createDots();

                // Immediate update to activate JavaScript control
                updateCarousel();
            };

            // Initialize when ready
            setTimeout(initializeCarousel, 50);

            // Auto-rotate every 5 seconds (optional)
            // setInterval(() => {
            //     nextSlide();
            // }, 5000);
        }

        // Property card hover effects - but NOT for carousel cards
        gsap.utils.toArray('.property-card').forEach(card => {
            // Skip hover effects for carousel cards to prevent transform conflicts
            if (card.closest('#properties-carousel')) {
                return; // Skip carousel cards
            }

            card.addEventListener('mouseenter', () => {
                gsap.to(card, {
                    duration: 0.3,
                    y: -5,
                    scale: 1.01,
                    ease: 'power2.out'
                });
            });

            card.addEventListener('mouseleave', () => {
                gsap.to(card, {
                    duration: 0.3,
                    y: 0,
                    scale: 1,
                    ease: 'power2.out'
                });
            });
        });
    }

    // Search input focus animations
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('focus', () => {
            if (typeof gsap !== 'undefined') {
                gsap.to(input.parentElement, {
                    duration: 0.2,
                    scale: 1.02,
                    ease: 'power2.out'
                });
            }
        });

        input.addEventListener('blur', () => {
            if (typeof gsap !== 'undefined') {
                gsap.to(input.parentElement, {
                    duration: 0.2,
                    scale: 1,
                    ease: 'power2.out'
                });
            }
        });
    });

    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Navbar scroll effect - query fresh each time
    let lastScrollTop = 0;

    window.addEventListener('scroll', () => {
        // Query navbar fresh on each scroll to avoid stale references
        const navbar = document.querySelector('#navbar') || document.querySelector('#main-navigation') || document.querySelector('nav');

        if (!navbar) {
            return; // Silently return if no navbar found
        }

        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        try {
            if (scrollTop > lastScrollTop) {
                // Scrolling down
                if (typeof gsap !== 'undefined' && navbar.offsetHeight) {
                    gsap.to(navbar, { duration: 0.3, y: -navbar.offsetHeight, ease: 'power2.out' });
                }
            } else {
                // Scrolling up
                if (typeof gsap !== 'undefined') {
                    gsap.to(navbar, { duration: 0.3, y: 0, ease: 'power2.out' });
                }
            }

            // Add shadow on scroll
            if (scrollTop > 50) {
                navbar.classList.add('shadow-lg');
            } else {
                navbar.classList.remove('shadow-lg');
            }
        } catch (error) {
            // Silently catch any errors to prevent console spam
        }

        lastScrollTop = scrollTop;
    });
});
