@extends('layouts.property')

@section('title', 'My Saved Properties | HomeBaze')

@section('content')
<!-- Enhanced Saved Properties Page with Property Page Aesthetic -->
<div class="min-h-screen bg-linear-to-br from-gray-50 via-slate-50 to-gray-100 relative overflow-hidden">
    <!-- Subtle Background Elements -->
    <div class="absolute inset-0 opacity-30">
        <div class="floating-element absolute top-1/4 right-1/4 w-32 h-32 bg-linear-to-br from-purple-400/8 to-pink-500/6 rounded-full blur-3xl"></div>
        <div class="floating-element absolute bottom-1/3 left-1/4 w-40 h-40 bg-linear-to-br from-blue-400/6 to-indigo-500/4 rounded-full blur-3xl"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-30 pt-20 lg:pt-24">
        <!-- Premium Breadcrumb Navigation -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 lg:mb-8">
            <nav class="flex items-center justify-between" aria-label="Breadcrumb">
                <div class="flex items-center space-x-3 bg-white/70 backdrop-blur-xl rounded-2xl px-5 py-4 shadow-xl border border-white/40">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="group flex items-center text-gray-600 hover:text-purple-600 transition-all duration-300">
                        <div class="p-2 rounded-xl bg-purple-50 group-hover:bg-purple-100 group-hover:scale-105 transition-all duration-300">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm lg:text-base font-medium">Dashboard</span>
                    </a>

                    <!-- Separator -->
                    <div class="flex items-center">
                        <div class="w-8 h-0.5 bg-linear-to-r from-gray-300 to-gray-400 rounded-full"></div>
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-gray-400 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>

                    <!-- Current Page -->
                    <div class="flex items-center">
                        <div class="p-2 rounded-xl bg-linear-to-br from-purple-500 to-pink-600 shadow-lg">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-2">
                            <span class="text-sm lg:text-base font-bold text-gray-900">Saved Properties</span>
                            <div class="text-xs text-gray-500">{{ $stats['total'] ?? 0 }} Properties</div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <!-- Content Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(($stats['total'] ?? 0) > 0)
                <!-- Premium Filter and Search Bar -->
                <div class="bg-white/70 backdrop-blur-xl rounded-2xl shadow-xl border border-white/40 p-6 lg:p-8 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Properties</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" id="search" placeholder="Search by title, location, or type..."
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>

                    <!-- Property Type Filter -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                        <select id="type" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All Types</option>
                            <option value="apartment">Apartment</option>
                            <option value="house">House</option>
                            <option value="land">Land</option>
                            <option value="commercial">Commercial</option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select id="sort" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="saved_date">Date Saved</option>
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                            <option value="title">Title A-Z</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">
                            <span class="font-medium">{{ $stats['total'] ?? 0 }}</span> properties saved
                        </span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Create Alert
                        </button>
                        <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                            Export List
                        </button>
                    </div>
                </div>
            </div>

                <!-- Premium Properties Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    @foreach($savedProperties as $savedProperty)
                        @php $property = $savedProperty->property; @endphp
                        <div class="group bg-white/70 backdrop-blur-xl rounded-2xl shadow-xl border border-white/40 overflow-hidden hover:shadow-2xl hover:scale-105 transition-all duration-500">
                            <div class="relative">
                                @if($property->getMedia('featured')->first() || $property->getMedia('gallery')->first())
                                    <img src="{{ ($property->getMedia('featured')->first() ?? $property->getMedia('gallery')->first())?->getUrl() ?? 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=400&h=300&fit=crop' }}"
                                         alt="{{ $property->title }}"
                                         class="w-full h-48 lg:h-56 object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-48 lg:h-56 bg-linear-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Status Badge -->
                                <div class="absolute top-3 left-3">
                                    <span class="px-3 py-1 bg-linear-to-r {{ $property->listing_type === 'rent' ? 'from-emerald-500 to-teal-500' : 'from-blue-500 to-indigo-500' }} text-white text-xs font-bold rounded-full shadow-lg">
                                        {{ ucfirst($property->listing_type) }}
                                    </span>
                                </div>

                                <!-- Save/Unsave Button -->
                                <div class="absolute top-3 right-3">
                                    <button class="bg-white/90 backdrop-blur-xs hover:bg-white p-2 rounded-full transition-all duration-300 group">
                                        <svg class="w-4 h-4 text-red-500 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Date Saved -->
                                <div class="absolute bottom-3 left-3">
                                    <span class="bg-white/90 backdrop-blur-xs px-2 py-1 rounded-lg text-xs font-medium text-gray-700">
                                        Saved {{ $savedProperty->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-6">
                                <h3 class="font-bold text-gray-900 mb-2 text-lg group-hover:text-purple-600 transition-colors">
                                    {{ $property->title }}
                                </h3>

                                <div class="flex items-center space-x-2 text-sm text-gray-600 mb-3">
                                    <div class="p-1 rounded-md bg-gray-100">
                                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <span>{{ $property->area->name ?? $property->city->name }}, {{ $property->city->name }}</span>
                                </div>

                                <p class="text-2xl font-bold bg-linear-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-4">
                                    ₦{{ number_format($property->price) }}{{ $property->listing_type === 'rent' ? '/year' : '' }}
                                </p>

                                @if($property->bedrooms || $property->bathrooms || $property->size)
                                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                                        <span>
                                            @if($property->bedrooms) {{ $property->bedrooms }} Beds @endif
                                            @if($property->bathrooms) • {{ $property->bathrooms }} Baths @endif
                                            @if($property->size) • {{ $property->size }} sqm @endif
                                        </span>
                                    </div>
                                @endif

                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('property.show', $property->slug) }}"
                                       class="flex-1 bg-linear-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white py-2 px-3 rounded-xl font-medium transition-all duration-300 text-center shadow-lg hover:shadow-xl">
                                        View Details
                                    </a>
                                    <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 p-2 rounded-xl transition-all duration-300 hover:scale-110">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </button>
                                    <button class="bg-red-50 hover:bg-red-100 text-red-600 p-2 rounded-xl transition-all duration-300 hover:scale-110">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Premium Pagination -->
                @if(method_exists($savedProperties, 'hasPages') && $savedProperties->hasPages())
                    <div class="flex items-center justify-between mt-12">
                        <div class="text-sm text-gray-600">
                            Showing <span class="font-medium">{{ $savedProperties->firstItem() }}</span> to <span class="font-medium">{{ $savedProperties->lastItem() }}</span> of <span class="font-medium">{{ $savedProperties->total() }}</span> results
                        </div>
                        <div class="bg-white/70 backdrop-blur-xl rounded-2xl shadow-xl border border-white/40 p-2">
                            {{ $savedProperties->links() }}
                        </div>
                    </div>
                @endif

            @else
                <!-- Premium Empty State -->
                <div class="text-center py-16 lg:py-24">
                    <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/40 p-12 lg:p-16 max-w-2xl mx-auto">
                        <div class="w-24 h-24 mx-auto mb-8 bg-linear-to-br from-purple-100 to-pink-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">No Saved Properties Yet</h3>
                        <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                            Start building your dream property collection! Browse our listings and save the properties that catch your eye.
                        </p>
                        <a href="{{ route('properties.search') }}"
                           class="inline-flex items-center space-x-2 bg-linear-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-8 py-4 rounded-2xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>Explore Properties</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection