@extends('layouts.property')

@section('title', 'Properties - HomeBaze')

@section('content')
<!-- Enhanced Property Search Results with Premium Design -->
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100 relative overflow-hidden">
    <!-- Subtle Background Elements -->
    <div class="absolute inset-0 opacity-20">
        <div class="floating-element absolute top-1/4 right-1/4 w-32 h-32 bg-gradient-to-br from-emerald-400/8 to-teal-500/6 rounded-full blur-3xl"></div>
        <div class="floating-element absolute bottom-1/3 left-1/4 w-40 h-40 bg-gradient-to-br from-blue-400/6 to-indigo-500/4 rounded-full blur-3xl"></div>
        <div class="floating-element absolute top-1/2 right-1/3 w-24 h-24 bg-gradient-to-br from-amber-400/8 to-orange-500/6 rounded-full blur-2xl"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-30 pt-20 lg:pt-24">
        <!-- Premium Breadcrumb Navigation -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 lg:mb-8">
            <nav class="flex items-center" aria-label="Breadcrumb">
                <div class="flex items-center space-x-3 bg-white/70 backdrop-blur-xl rounded-2xl px-5 py-4 shadow-xl border border-white/40">
                    <!-- Home -->
                    <a href="{{ route('landing') }}" class="group flex items-center text-gray-600 hover:text-emerald-600 transition-all duration-300">
                        <div class="p-2 rounded-xl bg-emerald-50 group-hover:bg-emerald-100 group-hover:scale-105 transition-all duration-300">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm lg:text-base font-medium">Home</span>
                    </a>
                    
                    <!-- Separator -->
                    <div class="flex items-center">
                        <div class="w-8 h-0.5 bg-gradient-to-r from-gray-300 to-gray-400 rounded-full"></div>
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-gray-400 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    
                    <!-- Current Page -->
                    <div class="flex items-center">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-2">
                            <span class="text-sm lg:text-base font-bold text-gray-900">Property Search</span>
                            @if(request('q'))
                                <div class="text-xs text-emerald-600">{{ Str::limit(request('q'), 20) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Layout with Sidebar -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 lg:mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:gap-8">
                <!-- Search Filters Sidebar -->
                <div class="lg:col-span-1 space-y-4 lg:space-y-6">
                    <!-- Main Search Bar -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 p-4 lg:p-6 sticky top-20 lg:top-24">
                        <h3 class="text-base lg:text-lg font-semibold text-gray-900 mb-3 lg:mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Search Properties
                        </h3>
                        
                        <form method="GET" action="{{ route('properties.search') }}" class="space-y-4">
                            <!-- Search Input -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Search Keywords</label>
                                <input type="text" 
                                       name="q" 
                                       value="{{ request('q') }}"
                                       placeholder="Location, property type, features..."
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900 text-sm">
                            </div>

                            <!-- Listing Type -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Listing Type</label>
                                <select name="listingType" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900 text-sm">
                                    <option value="">Any Listing</option>
                                    <option value="rent" {{ request('listingType') == 'rent' ? 'selected' : '' }}>For Rent</option>
                                    <option value="sale" {{ request('listingType') == 'sale' ? 'selected' : '' }}>For Sale</option>
                                    <option value="lease" {{ request('listingType') == 'lease' ? 'selected' : '' }}>For Lease</option>
                                    <option value="shortlet" {{ request('listingType') == 'shortlet' ? 'selected' : '' }}>Shortlet</option>
                                </select>
                            </div>

                            <!-- Property Type -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Property Type</label>
                                <select name="propertyType" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900 text-sm">
                                    <option value="">Any Type</option>
                                    <option value="apartment" {{ request('propertyType') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                                    <option value="house" {{ request('propertyType') == 'house' ? 'selected' : '' }}>House</option>
                                    <option value="duplex" {{ request('propertyType') == 'duplex' ? 'selected' : '' }}>Duplex</option>
                                    <option value="villa" {{ request('propertyType') == 'villa' ? 'selected' : '' }}>Villa</option>
                                    <option value="penthouse" {{ request('propertyType') == 'penthouse' ? 'selected' : '' }}>Penthouse</option>
                                    <option value="commercial" {{ request('propertyType') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                    <option value="land" {{ request('propertyType') == 'land' ? 'selected' : '' }}>Land</option>
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Price Range (₦)</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                           placeholder="Min price" 
                                           class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900 text-sm">
                                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                           placeholder="Max price" 
                                           class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900 text-sm">
                                </div>
                            </div>

                            <!-- Bedrooms & Bathrooms -->
                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Bedrooms</label>
                                    <select name="bedrooms" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900 text-sm">
                                        <option value="">Any</option>
                                        <option value="0" {{ request('bedrooms') == '0' ? 'selected' : '' }}>Studio</option>
                                        <option value="1" {{ request('bedrooms') == '1' ? 'selected' : '' }}>1+</option>
                                        <option value="2" {{ request('bedrooms') == '2' ? 'selected' : '' }}>2+</option>
                                        <option value="3" {{ request('bedrooms') == '3' ? 'selected' : '' }}>3+</option>
                                        <option value="4" {{ request('bedrooms') == '4' ? 'selected' : '' }}>4+</option>
                                        <option value="5" {{ request('bedrooms') == '5' ? 'selected' : '' }}>5+</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Bathrooms</label>
                                    <select name="bathrooms" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900 text-sm">
                                        <option value="">Any</option>
                                        <option value="1" {{ request('bathrooms') == '1' ? 'selected' : '' }}>1+</option>
                                        <option value="2" {{ request('bathrooms') == '2' ? 'selected' : '' }}>2+</option>
                                        <option value="3" {{ request('bathrooms') == '3' ? 'selected' : '' }}>3+</option>
                                        <option value="4" {{ request('bathrooms') == '4' ? 'selected' : '' }}>4+</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Additional Filters -->
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">Additional Options</label>
                                
                                <!-- Furnishing Status -->
                                <select name="furnishingStatus" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900 text-sm mb-2">
                                    <option value="">Any Furnishing</option>
                                    <option value="furnished" {{ request('furnishingStatus') == 'furnished' ? 'selected' : '' }}>Fully Furnished</option>
                                    <option value="semi_furnished" {{ request('furnishingStatus') == 'semi_furnished' ? 'selected' : '' }}>Semi Furnished</option>
                                    <option value="unfurnished" {{ request('furnishingStatus') == 'unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                                </select>

                                <!-- Key Features -->
                                <select name="features" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900 text-sm mb-3">
                                    <option value="">Key Features</option>
                                    <option value="swimming_pool" {{ request('features') == 'swimming_pool' ? 'selected' : '' }}>Swimming Pool</option>
                                    <option value="gym" {{ request('features') == 'gym' ? 'selected' : '' }}>Gym/Fitness Center</option>
                                    <option value="security_guards" {{ request('features') == 'security_guards' ? 'selected' : '' }}>24/7 Security</option>
                                    <option value="air_conditioning" {{ request('features') == 'air_conditioning' ? 'selected' : '' }}>Air Conditioning</option>
                                    <option value="generator" {{ request('features') == 'generator' ? 'selected' : '' }}>Backup Generator</option>
                                    <option value="elevator" {{ request('features') == 'elevator' ? 'selected' : '' }}>Elevator</option>
                                    <option value="gated_community" {{ request('features') == 'gated_community' ? 'selected' : '' }}>Gated Community</option>
                                </select>

                                <!-- Property Status -->
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="isFeatured" value="1" 
                                               {{ request('isFeatured') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="text-sm text-gray-700">Featured Only</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="isVerified" value="1" 
                                               {{ request('isVerified') ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="text-sm text-gray-700">Verified Only</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Search Buttons -->
                            <div class="space-y-2 pt-4 border-t border-gray-200">
                                <button type="submit"
                                        class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold py-2.5 px-4 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-emerald-500/40 flex items-center justify-center text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Search Properties
                                </button>
                                <a href="{{ route('properties.search') }}"
                                   class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 px-4 rounded-xl transition-all duration-300 flex items-center justify-center text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Clear Filters
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Active Filters Summary -->
                    @if(request()->hasAny(['q', 'listingType', 'propertyType', 'bedrooms', 'bathrooms', 'min_price', 'max_price']))
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 p-4 lg:p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                            </svg>
                            Active Filters
                        </h3>
                        <div class="space-y-2">
                            @if(request('q'))
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    "{{ request('q') }}"
                                </span>
                            @endif
                            @if(request('listingType'))
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                    {{ ucfirst(request('listingType')) }}
                                </span>
                            @endif
                            @if(request('bedrooms'))
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                    {{ request('bedrooms') }}+ Bedrooms
                                </span>
                            @endif
                            @if(request('bathrooms'))
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                    {{ request('bathrooms') }}+ Bathrooms
                                </span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Properties Results -->
                <div class="lg:col-span-3 space-y-6 lg:space-y-8">
                    <!-- Results Header -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 p-4 lg:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">
                                    Property Search Results
                                </h1>
                                <p class="text-gray-600 text-sm lg:text-base">
                                    Found <span class="font-semibold text-emerald-600">{{ $properties->total() }}</span> 
                                    {{ Str::plural('property', $properties->total()) }}
                                    @if(request('q'))
                                        for "<span class="font-medium text-gray-900">{{ request('q') }}</span>"
                                    @endif
                                </p>
                            </div>
                            
                            <!-- Sort Options -->
                            <div class="mt-4 sm:mt-0">
                                <select class="px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900 text-sm">
                                    <option>Sort by Relevance</option>
                                    <option>Price: Low to High</option>
                                    <option>Price: High to Low</option>
                                    <option>Newest First</option>
                                    <option>Most Popular</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @if($properties->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                            @foreach($properties as $property)
                                <a href="{{ route('property.show', $property->slug ?? $property->id) }}" 
                                   class="group relative bg-white/95 backdrop-blur-sm rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 overflow-hidden hover:shadow-xl hover:shadow-emerald-500/20 transition-all duration-500 transform hover:scale-[1.01] hover:-translate-y-1">
                                    
                                    <!-- Property Image -->
                                    <div class="relative h-48 lg:h-56 overflow-hidden">
                                        @if($property->getMedia('featured')->count() > 0)
                                            <img src="{{ $property->getFirstMedia('featured')->getUrl() }}" 
                                                 alt="{{ $property->title }}"
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                        @elseif($property->getMedia('gallery')->count() > 0)
                                            <img src="{{ $property->getMedia('gallery')->first()->getUrl() }}" 
                                                 alt="{{ $property->title }}"
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        <!-- Overlay Gradient -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                        
                                        <!-- Status Badges -->
                                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                                            @if($property->is_featured)
                                                <span class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-yellow-500 to-amber-500 text-white text-xs font-bold rounded-xl shadow-lg backdrop-blur-sm">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                    Featured
                                                </span>
                                            @endif
                                            @if($property->is_verified)
                                                <span class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-bold rounded-xl shadow-lg backdrop-blur-sm">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Verified
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Listing Type Badge -->
                                        <div class="absolute top-4 right-4">
                                            <span class="inline-flex items-center px-3 py-1.5 bg-white/10 backdrop-blur-xl text-white text-xs font-semibold rounded-xl border border-white/20 shadow-lg capitalize">
                                                {{ $property->listing_type }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Property Details -->
                                    <div class="p-4 lg:p-6">
                                        <!-- Price -->
                                        <div class="flex items-center justify-between mb-3">
                                            <div>
                                                <h3 class="text-xl lg:text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                                                    ₦{{ number_format($property->price) }}
                                                </h3>
                                                @if($property->price_period && $property->price_period !== 'total')
                                                    <span class="text-xs font-medium text-gray-500 mt-1 block">
                                                        /{{ str_replace('per_', '', $property->price_period) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <button class="p-2 bg-gray-50 hover:bg-emerald-50 rounded-xl transition-colors duration-300 group/heart">
                                                <svg class="w-4 h-4 text-gray-400 group-hover/heart:text-emerald-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Title -->
                                        <h4 class="font-bold text-gray-900 text-base lg:text-lg mb-2 line-clamp-2 group-hover:text-emerald-600 transition-colors duration-300 leading-tight">
                                            {{ $property->title }}
                                        </h4>

                                        <!-- Location -->
                                        <div class="flex items-center text-gray-600 mb-4">
                                            <div class="p-1 bg-emerald-50 rounded-lg mr-2">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium line-clamp-1">{{ $property->city->name ?? 'Unknown' }}, {{ $property->state->name ?? 'Unknown' }}</span>
                                        </div>

                                        <!-- Property Features -->
                                        <div class="grid grid-cols-3 gap-3">
                                            <div class="text-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                                                <div class="w-6 h-6 bg-emerald-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                                    </svg>
                                                </div>
                                                <div class="text-sm font-bold text-gray-900">{{ $property->bedrooms }}</div>
                                                <div class="text-xs text-gray-500 font-medium">Beds</div>
                                            </div>
                                            <div class="text-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                                                <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                                    </svg>
                                                </div>
                                                <div class="text-sm font-bold text-gray-900">{{ $property->bathrooms }}</div>
                                                <div class="text-xs text-gray-500 font-medium">Baths</div>
                                            </div>
                                            @if($property->size_sqm)
                                                <div class="text-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                                                    <div class="w-6 h-6 bg-amber-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="text-sm font-bold text-gray-900">{{ number_format($property->size_sqm) }}</div>
                                                    <div class="text-xs text-gray-500 font-medium">Sqm</div>
                                                </div>
                                            @else
                                                <div class="text-center p-3 bg-gray-50 rounded-xl border border-gray-200">
                                                    <div class="w-6 h-6 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                                        <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="text-sm font-bold text-gray-900">{{ ucfirst($property->property_type) }}</div>
                                                    <div class="text-xs text-gray-500 font-medium">Type</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <!-- Enhanced Premium Pagination -->
                        <div class="mt-8 lg:mt-12">
                            <div class="bg-white/95 backdrop-blur-sm rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 p-6 lg:p-8">
                                {{ $properties->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @else
                        <!-- No Results -->
                        <div class="text-center py-12 lg:py-20">
                            <div class="relative max-w-lg mx-auto">
                                <div class="relative bg-white/95 backdrop-blur-sm rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 p-8 lg:p-12">
                                    <!-- Icon -->
                                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center shadow-lg">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    
                                    <h3 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">
                                        No Properties Found
                                    </h3>
                                    <p class="text-gray-600 text-base lg:text-lg mb-8 max-w-md mx-auto leading-relaxed">
                                        We couldn't find any properties matching your criteria. Try adjusting your search filters or explore different areas.
                                    </p>
                                    
                                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                        <a href="{{ route('properties.search') }}"
                                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-emerald-500/40">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Clear All Filters
                                        </a>
                                        <a href="{{ route('landing') }}"
                                           class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-300">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                            Back to Home
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
@endsection

@push('styles')
<style>
    /* Enhanced line clamp utilities */
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Floating elements animation */
    .floating-element {
        animation: float 6s ease-in-out infinite;
    }
    
    .floating-element:nth-child(2) {
        animation-delay: -2s;
    }
    
    .floating-element:nth-child(3) {
        animation-delay: -4s;
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0px) translateX(0px) rotate(0deg);
        }
        33% {
            transform: translateY(-20px) translateX(10px) rotate(2deg);
        }
        66% {
            transform: translateY(20px) translateX(-10px) rotate(-2deg);
        }
    }

    /* Premium card hover effects */
    .group:hover .floating-element {
        animation-play-state: paused;
    }

    /* Smooth scroll behavior */
    html {
        scroll-behavior: smooth;
    }

    /* Enhanced backdrop blur support */
    @supports (backdrop-filter: blur(20px)) {
        .backdrop-blur-xl {
            backdrop-filter: blur(20px);
        }
    }
</style>
@endpush