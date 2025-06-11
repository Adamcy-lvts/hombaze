import './bootstrap';

// GSAP Landing Page Animations and Interactions
document.addEventListener('DOMContentLoaded', function() {
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
                        onUpdate: function() {
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
                    {opacity: 0, y: -10}, 
                    {duration: 0.3, opacity: 1, y: 0, ease: 'power2.out'}
                );
            }
        }
    }
    
    // Property Search Form Handling
    const searchForm = document.getElementById('property-search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
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
            window.location.href = `/search?${params.toString()}`;
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
    
    // Property card hover effects (for featured properties)
    gsap.utils.toArray('.property-card').forEach(card => {
        const image = card.querySelector('.property-image');
        const overlay = card.querySelector('.property-overlay');
        
        if (image && overlay) {
            card.addEventListener('mouseenter', () => {
                gsap.to(image, {duration: 0.3, scale: 1.05, ease: 'power2.out'});
                gsap.to(overlay, {duration: 0.3, opacity: 0.8, ease: 'power2.out'});
            });
            
            card.addEventListener('mouseleave', () => {
                gsap.to(image, {duration: 0.3, scale: 1, ease: 'power2.out'});
                gsap.to(overlay, {duration: 0.3, opacity: 0.5, ease: 'power2.out'});
            });
        }
    });
    
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
    
    // Navbar scroll effect
    let lastScrollTop = 0;
    const navbar = document.querySelector('.navbar');
    
    if (navbar) {
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > lastScrollTop) {
                // Scrolling down
                gsap.to(navbar, {duration: 0.3, y: -navbar.offsetHeight, ease: 'power2.out'});
            } else {
                // Scrolling up
                gsap.to(navbar, {duration: 0.3, y: 0, ease: 'power2.out'});
            }
            
            lastScrollTop = scrollTop;
            
            // Add shadow on scroll
            if (scrollTop > 50) {
                navbar.classList.add('shadow-lg');
            } else {
                navbar.classList.remove('shadow-lg');
            }
        });
    }
});
