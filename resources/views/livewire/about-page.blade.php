<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 relative overflow-hidden">
    <!-- Premium Background Elements -->
    <div class="absolute inset-0">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-500/10 to-indigo-500/5 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-br from-emerald-500/8 to-teal-500/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 right-1/4 w-64 h-64 bg-gradient-to-br from-purple-500/5 to-pink-500/3 rounded-full blur-2xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- Premium Header Section -->
    <div class="relative z-10 bg-white/80 backdrop-blur-2xl border-b border-gray-200/60 shadow-2xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <!-- Premium Breadcrumb -->
            <nav class="flex items-center space-x-3 text-sm text-gray-600 mb-8">
                <a href="{{ route('landing') }}" wire:navigate class="group flex items-center space-x-2 hover:text-emerald-600 transition-colors duration-300">
                    <div class="w-8 h-8 bg-gray-100/60 backdrop-blur-sm rounded-lg flex items-center justify-center group-hover:bg-emerald-100/60 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <span class="font-medium">Home</span>
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="font-bold text-gray-900">About Us</span>
            </nav>

            <!-- Hero Section -->
            <div class="text-center mb-16">
                <h1 class="text-4xl lg:text-6xl font-black text-gray-900 mb-6">
                    About
                    <span class="bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-600 bg-clip-text text-transparent">HomeBaze</span>
                </h1>
                <p class="text-xl text-gray-600 leading-relaxed max-w-4xl mx-auto">
                    Nigeria's premier real estate platform, connecting property seekers with trusted agents and agencies across all 36 states. 
                    We're revolutionizing how Nigerians discover, evaluate, and secure their dream properties.
                </p>
            </div>

            <!-- Stats Section -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <div class="bg-white/60 backdrop-blur-xl border border-gray-200/60 rounded-2xl p-6 text-center shadow-xl">
                    <div class="text-3xl font-black text-blue-600 mb-2">{{ number_format($stats['total_properties']) }}</div>
                    <div class="text-sm text-gray-600 font-medium">Active Properties</div>
                </div>
                <div class="bg-white/60 backdrop-blur-xl border border-gray-200/60 rounded-2xl p-6 text-center shadow-xl">
                    <div class="text-3xl font-black text-emerald-600 mb-2">{{ number_format($stats['total_agencies']) }}</div>
                    <div class="text-sm text-gray-600 font-medium">Partner Agencies</div>
                </div>
                <div class="bg-white/60 backdrop-blur-xl border border-gray-200/60 rounded-2xl p-6 text-center shadow-xl">
                    <div class="text-3xl font-black text-purple-600 mb-2">{{ number_format($stats['total_agents']) }}</div>
                    <div class="text-sm text-gray-600 font-medium">Verified Agents</div>
                </div>
                <div class="bg-white/60 backdrop-blur-xl border border-gray-200/60 rounded-2xl p-6 text-center shadow-xl">
                    <div class="text-3xl font-black text-orange-600 mb-2">{{ number_format($stats['total_locations']) }}</div>
                    <div class="text-sm text-gray-600 font-medium">Cities Covered</div>
                </div>
                <div class="bg-white/60 backdrop-blur-xl border border-gray-200/60 rounded-2xl p-6 text-center shadow-xl">
                    <div class="text-3xl font-black text-red-600 mb-2">{{ number_format($stats['verified_agencies']) }}</div>
                    <div class="text-sm text-gray-600 font-medium">Verified Agencies</div>
                </div>
                <div class="bg-white/60 backdrop-blur-xl border border-gray-200/60 rounded-2xl p-6 text-center shadow-xl">
                    <div class="text-3xl font-black text-indigo-600 mb-2">{{ number_format($stats['total_users']) }}</div>
                    <div class="text-sm text-gray-600 font-medium">Happy Users</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="relative z-[1] max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        
        <!-- Mission & Vision Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-20">
            <!-- Mission -->
            <div class="bg-white/80 backdrop-blur-2xl border border-gray-200/40 rounded-3xl p-8 shadow-2xl">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Our Mission</h3>
                </div>
                <p class="text-gray-600 leading-relaxed">
                    To democratize access to quality real estate across Nigeria by providing a transparent, efficient, and user-friendly platform that connects property seekers with verified professionals, making property transactions seamless and trustworthy.
                </p>
            </div>

            <!-- Vision -->
            <div class="bg-white/80 backdrop-blur-2xl border border-gray-200/40 rounded-3xl p-8 shadow-2xl">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Our Vision</h3>
                </div>
                <p class="text-gray-600 leading-relaxed">
                    To become Nigeria's most trusted and comprehensive real estate platform, where every Nigerian can easily find, evaluate, and secure their ideal property while building wealth through informed real estate decisions.
                </p>
            </div>
        </div>

        <!-- Core Values Section -->
        <div class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900 mb-4">Our Core Values</h2>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto">The principles that guide everything we do at HomeBaze</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Trust -->
                <div class="text-center group">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-2">Trust</h4>
                    <p class="text-gray-600 text-sm">Building lasting relationships through transparency and verified listings</p>
                </div>

                <!-- Innovation -->
                <div class="text-center group">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-2">Innovation</h4>
                    <p class="text-gray-600 text-sm">Leveraging technology to simplify and enhance property discovery</p>
                </div>

                <!-- Excellence -->
                <div class="text-center group">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-2">Excellence</h4>
                    <p class="text-gray-600 text-sm">Striving for the highest standards in every interaction and service</p>
                </div>

                <!-- Empowerment -->
                <div class="text-center group">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-2">Empowerment</h4>
                    <p class="text-gray-600 text-sm">Giving Nigerians the tools and knowledge to make informed property decisions</p>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="mb-20">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900 mb-4">Meet Our Team</h2>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto">The passionate professionals driving HomeBaze forward</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($teamMembers as $member)
                    <div class="bg-white/80 backdrop-blur-2xl border border-gray-200/40 rounded-3xl p-8 text-center shadow-2xl group hover:shadow-3xl transition-all duration-500 hover:scale-105">
                        <!-- Profile Image -->
                        <div class="relative mb-6">
                            @if($member['image'])
                                <img src="{{ $member['image'] }}" alt="{{ $member['name'] }}" class="w-24 h-24 rounded-full mx-auto border-4 border-white/20 shadow-xl object-cover">
                            @else
                                <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full mx-auto flex items-center justify-center border-4 border-white/20 shadow-xl">
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Member Info -->
                        <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $member['name'] }}</h4>
                        <p class="text-blue-600 font-semibold text-sm mb-4">{{ $member['position'] }}</p>
                        <p class="text-gray-600 text-sm leading-relaxed mb-6">{{ $member['bio'] }}</p>

                        <!-- Social Links -->
                        <div class="flex justify-center space-x-4">
                            <a href="{{ $member['linkedin'] }}" class="w-10 h-10 bg-blue-100/60 rounded-xl flex items-center justify-center hover:bg-blue-200/60 transition-colors duration-300">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="{{ $member['twitter'] }}" class="w-10 h-10 bg-sky-100/60 rounded-xl flex items-center justify-center hover:bg-sky-200/60 transition-colors duration-300">
                                <svg class="w-5 h-5 text-sky-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Why Choose Us Section -->
        <div class="bg-white/80 backdrop-blur-2xl border border-gray-200/40 rounded-3xl p-12 shadow-2xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-black text-gray-900 mb-4">Why Choose HomeBaze?</h2>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto">What sets us apart in Nigeria's real estate landscape</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Comprehensive Coverage -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3">Nationwide Coverage</h4>
                    <p class="text-gray-600">Access properties across all 36 Nigerian states with local market expertise</p>
                </div>

                <!-- Verified Professionals -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3">Verified Professionals</h4>
                    <p class="text-gray-600">Work only with thoroughly vetted and verified agents and agencies</p>
                </div>

                <!-- Smart Technology -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-3">Smart Technology</h4>
                    <p class="text-gray-600">Advanced search filters and AI-powered recommendations for better matches</p>
                </div>
            </div>
        </div>
    </div>
</div>