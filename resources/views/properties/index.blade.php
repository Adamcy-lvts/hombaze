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

    <div class="relative z-30 py-24">
        <!-- Premium Breadcrumb Navigation -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-2">
            <nav class="flex items-center space-x-2" aria-label="Breadcrumb">
                <div class="flex items-center space-x-2 bg-white/60 backdrop-blur-xl rounded-2xl px-4 py-3 shadow-lg border border-white/30">
                    <!-- Home -->
                    <a href="{{ route('landing') }}" class="group flex items-center text-gray-600 hover:text-emerald-600 transition-all duration-300">
                        <div class="p-1.5 rounded-lg bg-emerald-50 group-hover:bg-emerald-100 transition-colors duration-300">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm font-medium">Home</span>
                    </a>
                    
                    <!-- Separator -->
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    
                    <!-- Current Page -->
                    <div class="flex items-center">
                        <div class="p-1.5 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm font-semibold text-gray-900">
                            Search Results
                            @if(request('q'))
                                <span class="text-emerald-600">for "{{ Str::limit(request('q'), 15) }}"</span>
                            @endif
                        </span>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Premium Search Results Header -->
        <div class="bg-white/80 backdrop-blur-xl shadow-lg border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-4xl lg:text-5xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent mb-4">
                            Property Search Results
                        </h1>
                        <p class="text-gray-600 text-lg lg:text-xl">
                            Found <span class="font-semibold text-emerald-600">{{ $properties->total() }}</span> 
                            {{ Str::plural('property', $properties->total()) }}
                            @if(request('q'))
                                for "<span class="font-medium text-gray-900">{{ request('q') }}</span>"
                            @endif
                        </p>
                    </div>
                    {{-- <div class="mt-6 md:mt-0">
                        <a href="{{ route('landing') }}" 
                           class="group inline-flex items-center px-6 py-3 lg:px-8 lg:py-4 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 text-white font-semibold text-lg rounded-2xl shadow-xl hover:shadow-emerald-500/40 transition-all duration-500 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-3 group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            New Search
                        </a>
                    </div> --}}
                </div>
            </div>
        </div>

        <!-- Advanced Search and Filter Section -->
        <div class="bg-white/90 backdrop-blur-xl shadow-lg border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
                <!-- Search Bar -->
                <div class="mb-6">
                    <form method="GET" action="{{ route('properties.search') }}" class="w-full">
                        <div class="flex items-center bg-gray-50 rounded-2xl border border-gray-200 focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-500/20 transition-all duration-300">
                            <div class="flex-shrink-0 pl-4 pr-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                   name="q" 
                                   value="{{ request('q') }}"
                                   placeholder="Search by location, property type, price, features... (e.g., '3 bedroom Lagos', 'under 5M', 'swimming pool')"
                                   class="flex-1 py-3 lg:py-4 bg-transparent text-gray-900 placeholder-gray-500 focus:outline-none border-0 ring-0 text-sm lg:text-base">
                            <button type="submit"
                                    class="flex-shrink-0 m-1 lg:m-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold py-2 lg:py-3 px-4 lg:px-6 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-emerald-500/40 flex items-center">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span class="hidden sm:inline ml-2">Search</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Advanced Filters -->
                <div x-data="{ showFilters: false }" class="space-y-4">
                    <!-- Filter Toggle Button -->
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Refine Your Search</h3>
                        <button @click="showFilters = !showFilters" type="button"
                                class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-300">
                            <svg class="w-5 h-5 mr-2 transition-transform duration-300" :class="showFilters ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                            </svg>
                            <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
                        </button>
                    </div>

                    <!-- Filters Panel -->
                    <div x-show="showFilters" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-4"
                         class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
                        
                        <form method="GET" action="{{ route('properties.search') }}" class="space-y-6">
                            <!-- Preserve existing search query -->
                            @if(request('q'))
                                <input type="hidden" name="q" value="{{ request('q') }}">
                            @endif
                            
                            <!-- Main Filters Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Listing Type -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Listing Type</label>
                                    <select name="listingType" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900">
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
                                    <select name="propertyType" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900">
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

                                <!-- Bedrooms -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Bedrooms</label>
                                    <select name="bedrooms" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900">
                                        <option value="">Any Bedrooms</option>
                                        <option value="0" {{ request('bedrooms') == '0' ? 'selected' : '' }}>Studio</option>
                                        <option value="1" {{ request('bedrooms') == '1' ? 'selected' : '' }}>1 Bedroom</option>
                                        <option value="2" {{ request('bedrooms') == '2' ? 'selected' : '' }}>2 Bedrooms</option>
                                        <option value="3" {{ request('bedrooms') == '3' ? 'selected' : '' }}>3 Bedrooms</option>
                                        <option value="4" {{ request('bedrooms') == '4' ? 'selected' : '' }}>4 Bedrooms</option>
                                        <option value="5" {{ request('bedrooms') == '5' ? 'selected' : '' }}>5+ Bedrooms</option>
                                    </select>
                                </div>

                                <!-- Bathrooms -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Bathrooms</label>
                                    <select name="bathrooms" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900">
                                        <option value="">Any Bathrooms</option>
                                        <option value="1" {{ request('bathrooms') == '1' ? 'selected' : '' }}>1 Bathroom</option>
                                        <option value="2" {{ request('bathrooms') == '2' ? 'selected' : '' }}>2 Bathrooms</option>
                                        <option value="3" {{ request('bathrooms') == '3' ? 'selected' : '' }}>3 Bathrooms</option>
                                        <option value="4" {{ request('bathrooms') == '4' ? 'selected' : '' }}>4+ Bathrooms</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Min Price (₦)</label>
                                    <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                           placeholder="Minimum price" 
                                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Max Price (₦)</label>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                           placeholder="Maximum price" 
                                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900">
                                </div>
                            </div>

                            <!-- Additional Filters -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Furnishing Status -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Furnishing</label>
                                    <select name="furnishingStatus" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900">
                                        <option value="">Any Furnishing</option>
                                        <option value="furnished" {{ request('furnishingStatus') == 'furnished' ? 'selected' : '' }}>Fully Furnished</option>
                                        <option value="semi_furnished" {{ request('furnishingStatus') == 'semi_furnished' ? 'selected' : '' }}>Semi Furnished</option>
                                        <option value="unfurnished" {{ request('furnishingStatus') == 'unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                                    </select>
                                </div>

                                <!-- Parking Spaces -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Parking Spaces</label>
                                    <select name="parkingSpaces" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900">
                                        <option value="">Any Parking</option>
                                        <option value="0" {{ request('parkingSpaces') == '0' ? 'selected' : '' }}>No Parking</option>
                                        <option value="1" {{ request('parkingSpaces') == '1' ? 'selected' : '' }}>1 Space</option>
                                        <option value="2" {{ request('parkingSpaces') == '2' ? 'selected' : '' }}>2 Spaces</option>
                                        <option value="3" {{ request('parkingSpaces') == '3' ? 'selected' : '' }}>3+ Spaces</option>
                                    </select>
                                </div>

                                <!-- Key Features -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Key Features</label>
                                    <select name="features" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white text-gray-900">
                                        <option value="">Any Features</option>
                                        <option value="swimming_pool" {{ request('features') == 'swimming_pool' ? 'selected' : '' }}>Swimming Pool</option>
                                        <option value="gym" {{ request('features') == 'gym' ? 'selected' : '' }}>Gym/Fitness Center</option>
                                        <option value="security_guards" {{ request('features') == 'security_guards' ? 'selected' : '' }}>24/7 Security</option>
                                        <option value="air_conditioning" {{ request('features') == 'air_conditioning' ? 'selected' : '' }}>Air Conditioning</option>
                                        <option value="generator" {{ request('features') == 'generator' ? 'selected' : '' }}>Backup Generator</option>
                                        <option value="elevator" {{ request('features') == 'elevator' ? 'selected' : '' }}>Elevator</option>
                                        <option value="gated_community" {{ request('features') == 'gated_community' ? 'selected' : '' }}>Gated Community</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Property Status Checkboxes -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Property Status</label>
                                <div class="flex flex-wrap gap-4">
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

                            <!-- Filter Actions -->
                            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                                <button type="submit"
                                        class="flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-emerald-500/40 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Apply Filters
                                </button>
                                <a href="{{ route('properties.search') }}"
                                   class="sm:flex-none bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-all duration-300 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Clear All
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Search Filters Summary -->
        @if(request()->hasAny(['q', 'listingType', 'propertyType', 'bedrooms', 'bathrooms', 'min_price', 'max_price']))
        <div class="bg-gradient-to-r from-emerald-50/80 via-teal-50/60 to-emerald-50/80 backdrop-blur-sm border-b border-emerald-200/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h3 class="text-lg font-semibold text-emerald-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                    </svg>
                    Active Filters
                </h3>
                <div class="flex flex-wrap gap-3">
                    @if(request('q'))
                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium bg-gradient-to-r from-emerald-100 to-emerald-50 text-emerald-800 border border-emerald-200/50 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            "{{ request('q') }}"
                        </span>
                    @endif
                    @if(request('listingType'))
                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium bg-gradient-to-r from-blue-100 to-blue-50 text-blue-800 border border-blue-200/50 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ ucfirst(request('listingType')) }}
                        </span>
                    @endif
                    @if(request('bedrooms'))
                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium bg-gradient-to-r from-purple-100 to-purple-50 text-purple-800 border border-purple-200/50 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            {{ request('bedrooms') }}+ Bedrooms
                        </span>
                    @endif
                    @if(request('bathrooms'))
                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium bg-gradient-to-r from-amber-100 to-amber-50 text-amber-800 border border-amber-200/50 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                            </svg>
                            {{ request('bathrooms') }}+ Bathrooms
                        </span>
                    @endif
                    @if(request('min_price') || request('max_price'))
                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium bg-gradient-to-r from-green-100 to-green-50 text-green-800 border border-green-200/50 shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @if(request('min_price'))₦{{ number_format(request('min_price')) }}@endif
                            @if(request('min_price') && request('max_price')) - @endif
                            @if(request('max_price'))₦{{ number_format(request('max_price')) }}@endif
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Premium Properties Grid -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            @if($properties->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6">
                    @foreach($properties as $property)
                        <div class="group relative bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg border border-white/20 overflow-hidden hover:shadow-xl hover:shadow-emerald-500/20 transition-all duration-500 transform hover:scale-[1.01] hover:-translate-y-1">
                            <!-- Premium Property Image with Enhanced Overlay -->
                            <div class="relative h-48 sm:h-52 lg:h-56 overflow-hidden">
                                <img src="{{ $property->getFeaturedImageUrl('preview') }}" 
                                     alt="{{ $property->title }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                
                                <!-- Sophisticated Gradient Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                
                                <!-- Premium Status Badges -->
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
                                        <span class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-bold rounded-xl shadow-lg backdrop-blur-sm">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Verified
                                        </span>
                                    @endif
                                </div>

                                <!-- Enhanced Listing Type Badge -->
                                <div class="absolute top-4 right-4">
                                    <span class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-xl text-white text-sm font-semibold rounded-xl border border-white/20 shadow-lg capitalize">
                                        {{ $property->listing_type }}
                                    </span>
                                </div>

                                <!-- Hover Action Overlay -->
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500">
                                    <a href="{{ route('property.show', $property->slug) }}" 
                                       class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-xl text-white font-semibold rounded-2xl border border-white/30 shadow-xl hover:bg-white/30 transition-all duration-300 transform scale-90 group-hover:scale-100">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Quick View
                                    </a>
                                </div>
                            </div>

                            <!-- Enhanced Property Details -->
                            <div class="p-4 lg:p-5">
                                <!-- Premium Price Display -->
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h3 class="text-xl lg:text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                                            {{ $property->formatted_price }}
                                        </h3>
                                        @if($property->price_period && $property->price_period !== 'total')
                                            <span class="text-xs font-medium text-gray-500 mt-1 block">
                                                /{{ str_replace('per_', '', $property->price_period) }}
                                            </span>
                                        @endif
                                    </div>
                                    <button class="p-1.5 bg-gray-50 hover:bg-emerald-50 rounded-lg transition-colors duration-300 group">
                                        <svg class="w-4 h-4 text-gray-400 group-hover:text-emerald-600 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Property Title with Premium Typography -->
                                <h4 class="font-bold text-gray-900 text-base lg:text-lg mb-2 line-clamp-2 group-hover:text-emerald-600 transition-colors duration-300 leading-tight">
                                    {{ $property->title }}
                                </h4>

                                <!-- Enhanced Location -->
                                <div class="flex items-center text-gray-600 mb-4">
                                    <div class="p-1 bg-emerald-50 rounded-md mr-2">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-medium line-clamp-1">{{ $property->city->name ?? 'Unknown' }}, {{ $property->state->name ?? 'Unknown' }}</span>
                                </div>

                                <!-- Premium Property Features -->
                                <div class="grid grid-cols-3 gap-3 mb-4">
                                    <div class="text-center p-2 bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-lg">
                                        <div class="w-6 h-6 bg-emerald-100 rounded-md flex items-center justify-center mx-auto mb-1">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                        </div>
                                        <div class="text-sm font-bold text-gray-900">{{ $property->bedrooms }}</div>
                                        <div class="text-xs text-gray-500 font-medium">Beds</div>
                                    </div>
                                    <div class="text-center p-2 bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-lg">
                                        <div class="w-6 h-6 bg-blue-100 rounded-md flex items-center justify-center mx-auto mb-1">
                                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                            </svg>
                                        </div>
                                        <div class="text-sm font-bold text-gray-900">{{ $property->bathrooms }}</div>
                                        <div class="text-xs text-gray-500 font-medium">Baths</div>
                                    </div>
                                    @if($property->size_sqm)
                                        <div class="text-center p-2 bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-lg">
                                            <div class="w-6 h-6 bg-amber-100 rounded-md flex items-center justify-center mx-auto mb-1">
                                                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                                </svg>
                                            </div>
                                            <div class="text-sm font-bold text-gray-900">{{ number_format($property->size_sqm) }}</div>
                                            <div class="text-xs text-gray-500 font-medium">Sqm</div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Premium CTA Button -->
                                <a href="{{ route('property.show', $property->slug) }}" 
                                   class="w-full group relative inline-flex items-center justify-center px-4 py-2.5 lg:py-3 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 text-white font-semibold text-sm lg:text-base rounded-xl overflow-hidden shadow-lg hover:shadow-emerald-500/40 transition-all duration-500 transform hover:scale-[1.01]">
                                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-700 via-emerald-600 to-teal-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                    <span class="relative z-10 flex items-center">
                                        View Details
                                        <svg class="w-4 h-4 ml-1.5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                    </span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Enhanced Premium Pagination -->
                <div class="mt-16 lg:mt-20">
                    <div class="bg-white/60 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
                        {{ $properties->links('components.premium-pagination') }}
                    </div>
                </div>
            @else
                <!-- Enhanced No Results Section -->
                <div class="text-center py-20 lg:py-32">
                    <div class="relative max-w-2xl mx-auto">
                        <!-- Floating Elements for No Results -->
                        <div class="absolute -top-10 -right-10 w-20 h-20 bg-gradient-to-br from-emerald-400/20 to-teal-500/10 rounded-full blur-2xl"></div>
                        <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-gradient-to-br from-blue-400/20 to-indigo-500/10 rounded-full blur-2xl"></div>
                        
                        <div class="relative bg-white/60 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-12 lg:p-16">
                            <!-- Enhanced Icon -->
                            <div class="w-32 h-32 mx-auto mb-8 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            
                            <h3 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-gray-900 via-gray-700 to-gray-900 bg-clip-text text-transparent mb-4">
                                No Properties Found
                            </h3>
                            <p class="text-gray-600 text-lg lg:text-xl mb-10 max-w-lg mx-auto leading-relaxed">
                                We couldn't find any properties matching your search criteria. Try adjusting your filters or search terms to discover more options.
                            </p>
                            
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="{{ route('landing') }}" 
                                   class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 text-white font-semibold text-lg rounded-2xl shadow-xl hover:shadow-emerald-500/40 transition-all duration-500 transform hover:scale-105">
                                    <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Start New Search
                                </a>
                                
                                <a href="{{ route('properties.search') }}" 
                                   class="group inline-flex items-center px-8 py-4 bg-white/20 backdrop-blur-xl text-gray-900 font-semibold text-lg rounded-2xl border-2 border-white/30 hover:bg-white/30 transition-all duration-500 transform hover:scale-105">
                                    <svg class="w-6 h-6 mr-3 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    Browse All Properties
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