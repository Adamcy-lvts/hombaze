<!-- How It Works Section -->
<section class="relative py-16 lg:py-24 bg-linear-to-br from-gray-50 via-white to-gray-100 overflow-hidden" x-data="howItWorksComponent()">
    <!-- Background Pattern -->
    <!-- <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"><g fill=\"none\" fill-rule=\"evenodd\"><g fill=\"%23000000\" fill-opacity=\"0.05\"><path d=\"M20 20c0-5.5-4.5-10-10-10s-10 4.5-10 10 4.5 10 10 10 10-4.5 10-10zm10 0c0-5.5-4.5-10-10-10s-10 4.5-10 10 4.5 10 10 10 10-4.5 10-10z\"/></g></svg>'); background-size: 40px 40px;"></div>
    </div> -->

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center mb-12 lg:mb-16">
            <div class="inline-flex items-center space-x-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span>Simple & Secure</span>
            </div>
            <h2 class="text-3xl lg:text-5xl font-black text-gray-900 mb-4 lg:mb-6">
                How HomeBaze
                <span class="bg-linear-to-r from-blue-600 to-emerald-600 bg-clip-text text-transparent">Works</span>
            </h2>
            <p class="text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Your journey to finding the perfect home in Nigeria starts here. Simple steps, trusted process, guaranteed results.
            </p>
        </div>

        <!-- Trust Badges -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6 mb-16 lg:mb-20">
            <a href="#property-verification" class="group text-center p-4 lg:p-6 bg-white rounded-xl lg:rounded-2xl shadow-md border border-gray-200/60 hover:shadow-lg hover:border-emerald-200 transition-all duration-500"
                 x-intersect.once="$el.classList.add('animate-fade-in-up')">
                <div class="w-12 h-12 lg:w-16 lg:h-16 bg-linear-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center mx-auto mb-3 lg:mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 lg:w-8 lg:h-8 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4 8-8z"/>
                    </svg>
                </div>
                <h3 class="text-sm lg:text-base font-bold text-gray-900 mb-1 lg:mb-2">Verified Properties</h3>
                <p class="text-xs lg:text-sm text-gray-600">Every listing is verified and authenticated</p>
            </a>

            <a href="#agent-verification" class="group text-center p-4 lg:p-6 bg-white rounded-xl lg:rounded-2xl shadow-md border border-gray-200/60 hover:shadow-lg hover:border-blue-200 transition-all duration-500"
                 x-intersect.once="$el.classList.add('animate-fade-in-up')">
                <div class="w-12 h-12 lg:w-16 lg:h-16 bg-linear-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center mx-auto mb-3 lg:mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 lg:w-8 lg:h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 6L13.5 7.5C13.1 7.9 12.6 8 12 8S10.9 7.9 10.5 7.5L9 6L3 7V9H5V20H7V13H9V20H11V9H13V20H15V13H17V20H19V9H21ZM12 10.5C12.8 10.5 13.5 11.2 13.5 12S12.8 13.5 12 13.5 10.5 12.8 10.5 12 11.2 10.5 12 10.5ZM7.5 12C7.5 11.2 8.2 10.5 9 10.5S10.5 11.2 10.5 12 9.8 13.5 9 13.5 7.5 12.8 7.5 12ZM15 10.5C15.8 10.5 16.5 11.2 16.5 12S15.8 13.5 15 13.5 13.5 12.8 13.5 12 14.2 10.5 15 10.5Z"/>
                    </svg>
                </div>
                <h3 class="text-sm lg:text-base font-bold text-gray-900 mb-1 lg:mb-2">Trusted Agents</h3>
                <p class="text-xs lg:text-sm text-gray-600">Professional agents with proven track records</p>
            </a>

            <div class="group text-center p-4 lg:p-6 bg-white rounded-xl lg:rounded-2xl shadow-md border border-gray-200/60 hover:shadow-lg hover:border-amber-200 transition-all duration-500"
                 x-intersect.once="$el.classList.add('animate-fade-in-up')">
                <div class="w-12 h-12 lg:w-16 lg:h-16 bg-linear-to-br from-amber-100 to-amber-200 rounded-xl flex items-center justify-center mx-auto mb-3 lg:mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 lg:w-8 lg:h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="text-sm lg:text-base font-bold text-gray-900 mb-1 lg:mb-2">Secure Transactions</h3>
                <p class="text-xs lg:text-sm text-gray-600">Safe and secure payment processing</p>
            </div>

            <div class="group text-center p-4 lg:p-6 bg-white rounded-xl lg:rounded-2xl shadow-md border border-gray-200/60 hover:shadow-lg hover:border-purple-200 transition-all duration-500"
                 x-intersect.once="$el.classList.add('animate-fade-in-up')">
                <div class="w-12 h-12 lg:w-16 lg:h-16 bg-linear-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center mx-auto mb-3 lg:mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 lg:w-8 lg:h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-sm lg:text-base font-bold text-gray-900 mb-1 lg:mb-2">24/7 Support</h3>
                <p class="text-xs lg:text-sm text-gray-600">Round-the-clock customer assistance</p>
            </div>
        </div>

        <!-- How It Works Steps -->
        <div class="relative mb-16 lg:mb-20">
            <!-- Connecting Line Background -->
            <div class="hidden md:block absolute top-12 left-1/2 transform -translate-x-1/2 w-2/3 h-0.5 bg-linear-to-r from-emerald-300 via-blue-300 to-purple-300 z-0"></div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 relative z-10">
                <!-- Step 1: Search -->
                <div class="group text-center"
                     x-intersect.once="$el.classList.add('animate-fade-in-up')">
                    <div class="relative mb-8">
                        <!-- Step Number -->
                        <div class="absolute -top-4 -left-4 w-8 h-8 bg-linear-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center shadow-lg z-20">
                            <span class="text-white text-sm font-bold">1</span>
                        </div>
                        <!-- Icon Container -->
                        <div class="w-20 h-20 lg:w-24 lg:h-24 bg-linear-to-br from-emerald-100 to-emerald-200 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-all duration-500 shadow-lg border-4 border-white relative z-20">
                            <svg class="w-10 h-10 lg:w-12 lg:h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-4">Search & Discover</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Browse through thousands of verified properties across Nigeria. Use our smart filters to find exactly what you're looking for.
                    </p>
                </div>

                <!-- Step 2: Connect -->
                <div class="group text-center"
                     x-intersect.once="$el.classList.add('animate-fade-in-up')">
                    <div class="relative mb-8">
                        <!-- Step Number -->
                        <div class="absolute -top-4 -left-4 w-8 h-8 bg-linear-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg z-20">
                            <span class="text-white text-sm font-bold">2</span>
                        </div>
                        <!-- Icon Container -->
                        <div class="w-20 h-20 lg:w-24 lg:h-24 bg-linear-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-all duration-500 shadow-lg border-4 border-white relative z-20">
                            <svg class="w-10 h-10 lg:w-12 lg:h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-4">Connect & Chat</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Connect directly with verified agents and property owners. Schedule viewings and get instant responses to your questions.
                    </p>
                </div>

                <!-- Step 3: Move In -->
                <div class="group text-center"
                     x-intersect.once="$el.classList.add('animate-fade-in-up')">
                    <div class="relative mb-8">
                        <!-- Step Number -->
                        <div class="absolute -top-4 -left-4 w-8 h-8 bg-linear-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg z-20">
                            <span class="text-white text-sm font-bold">3</span>
                        </div>
                        <!-- Icon Container -->
                        <div class="w-20 h-20 lg:w-24 lg:h-24 bg-linear-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-all duration-500 shadow-lg border-4 border-white relative z-20">
                            <svg class="w-10 h-10 lg:w-12 lg:h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl lg:text-2xl font-bold text-gray-900 mb-4">Secure & Move In</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Complete your transaction securely through our platform. Get your keys and move into your new home with confidence.
                    </p>
                </div>
            </div>
        </div>

        <!-- Key Benefits -->
        <div class="bg-linear-to-br from-slate-50 to-white rounded-3xl p-8 lg:p-12 shadow-xl border border-gray-200/60">
            <div class="text-center mb-8 lg:mb-12">
                <h3 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">Why Choose HomeBaze?</h3>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Join thousands of satisfied clients who found their dream homes through our platform
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <div class="group flex items-start space-x-4 p-4 lg:p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300"
                     x-intersect.once="$el.classList.add('animate-fade-in-up')">
                    <div class="shrink-0 w-12 h-12 bg-linear-to-br from-green-100 to-green-200 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">No Hidden Fees</h4>
                        <p class="text-gray-600 text-sm">Transparent pricing with no surprise charges. What you see is what you pay.</p>
                    </div>
                </div>

                <div class="group flex items-start space-x-4 p-4 lg:p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300"
                     x-intersect.once="$el.classList.add('animate-fade-in-up')">
                    <div class="shrink-0 w-12 h-12 bg-linear-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Quick Approval</h4>
                        <p class="text-gray-600 text-sm">Fast-track your applications with our streamlined approval process.</p>
                    </div>
                </div>

                <div class="group flex items-start space-x-4 p-4 lg:p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300"
                     x-intersect.once="$el.classList.add('animate-fade-in-up')">
                    <div class="shrink-0 w-12 h-12 bg-linear-to-br from-purple-100 to-purple-200 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Legal Protection</h4>
                        <p class="text-gray-600 text-sm">All transactions are legally protected with proper documentation.</p>
                    </div>
                </div>
            </div>
        </div>


        <!-- CTA Section -->
        <div class="text-center">
            <div class="max-w-2xl mx-auto">
                <h3 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">Ready to Find Your Dream Home?</h3>
                <p class="text-lg text-gray-600 mb-8">Join thousands of satisfied clients who found their perfect home through HomeBaze</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                    <a href="{{ route('properties.search') }}" 
                       class="group inline-flex items-center justify-center space-x-2 bg-linear-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold py-4 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl">
                        <span>Browse Properties</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                    <a href="/register" 
                       class="group inline-flex items-center justify-center space-x-2 bg-white hover:bg-gray-50 text-gray-900 font-semibold py-4 px-8 rounded-xl border-2 border-gray-200 hover:border-emerald-300 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <span>Get Started Free</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
                
                <!-- Verification Process Links -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center text-sm text-gray-600">
                    <span class="font-medium">Learn about our verification process:</span>
                    <div class="flex flex-wrap gap-3 justify-center">
                        <a href="#property-verification" class="inline-flex items-center space-x-1 text-emerald-600 hover:text-emerald-700 font-semibold transition-colors duration-300">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm4.59-12.42L10 14.17l-2.59-2.58L6 13l4 4 8-8z"/>
                            </svg>
                            <span>Property Verification</span>
                        </a>
                        <a href="#agent-verification" class="inline-flex items-center space-x-1 text-blue-600 hover:text-blue-700 font-semibold transition-colors duration-300">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 6L13.5 7.5C13.1 7.9 12.6 8 12 8S10.9 7.9 10.5 7.5L9 6L3 7V9H5V20H7V13H9V20H11V9H13V20H15V13H17V20H19V9H21Z"/>
                            </svg>
                            <span>Agent Verification</span>
                        </a>
                        <a href="{{ route('properties.search', ['type' => 'sale']) }}" class="inline-flex items-center space-x-1 text-purple-600 hover:text-purple-700 font-semibold transition-colors duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m0 0V11a1 1 0 011-1h2a1 1 0 011 1v10m0 0h3a1 1 0 001-1V10M9 21h6"></path>
                            </svg>
                            <span>Homes for Sale</span>
                        </a>
                        <a href="{{ route('properties.search', ['type' => 'rent']) }}" class="inline-flex items-center space-x-1 text-amber-600 hover:text-amber-700 font-semibold transition-colors duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            <span>Homes for Rent</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Stagger animation for trust badges */
    .group:nth-child(1) { animation-delay: 0.1s; }
    .group:nth-child(2) { animation-delay: 0.2s; }
    .group:nth-child(3) { animation-delay: 0.3s; }
    .group:nth-child(4) { animation-delay: 0.4s; }
    
    /* Stagger animation for steps */
    .group:nth-child(1) { animation-delay: 0.2s; }
    .group:nth-child(2) { animation-delay: 0.4s; }
    .group:nth-child(3) { animation-delay: 0.6s; }
    
    /* Stagger animation for benefits */
    .group:nth-child(1) { animation-delay: 0.1s; }
    .group:nth-child(2) { animation-delay: 0.2s; }
    .group:nth-child(3) { animation-delay: 0.3s; }
</style>

<script>
    function howItWorksComponent() {
        return {
            init() {
                console.log('How It Works component initialized');
            }
        }
    }
    
    // Counter animation function
    function countUp(element, target) {
        const duration = 2000; // 2 seconds
        const step = target / (duration / 16); // 60fps
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            
            // Format number with commas for large numbers
            const formatted = Math.floor(current).toLocaleString();
            element.textContent = formatted + (target >= 1000 ? '+' : '');
        }, 16);
    }
</script>