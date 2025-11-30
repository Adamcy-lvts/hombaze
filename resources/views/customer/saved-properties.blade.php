@extends('layouts.property')

@section('title', 'My Saved Properties | HomeBaze')

@section('content')
<div class="min-h-screen bg-gray-50 font-sans text-gray-900 pt-20">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Saved Properties</h1>
                <p class="text-gray-500 mt-2">View and manage your favorite properties.</p>
            </div>
            <div class="flex items-center gap-3">
                 <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                    Dashboard
                </a>
                <span class="text-gray-300">/</span>
                <span class="text-sm font-medium text-emerald-600">Saved Properties</span>
            </div>
        </div>

        @if(($stats['total'] ?? 0) > 0)
            <!-- Filters -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-8">
                <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <div class="relative">
                            <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                            <input type="text" name="search" placeholder="Search properties..." value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl text-sm transition-all duration-200">
                        </div>
                    </div>
                    <div>
                        <select name="type" class="w-full py-2.5 px-4 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl text-sm transition-all duration-200">
                            <option value="">All Types</option>
                            <option value="apartment" {{ request('type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                            <option value="house" {{ request('type') == 'house' ? 'selected' : '' }}>House</option>
                            <option value="land" {{ request('type') == 'land' ? 'selected' : '' }}>Land</option>
                            <option value="commercial" {{ request('type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                        </select>
                    </div>
                    <div>
                        <select name="sort" class="w-full py-2.5 px-4 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl text-sm transition-all duration-200">
                            <option value="saved_date" {{ request('sort') == 'saved_date' ? 'selected' : '' }}>Date Saved</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($savedProperties as $savedProperty)
                    @php $property = $savedProperty->property; @endphp
                    <div class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <!-- Image & Badges -->
                        <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
                            @if($property->getMedia('featured')->first() || $property->getMedia('gallery')->first())
                                <img src="{{ ($property->getMedia('featured')->first() ?? $property->getMedia('gallery')->first())?->getUrl() }}"
                                     alt="{{ $property->title }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <x-heroicon-o-home class="w-12 h-12" />
                                </div>
                            @endif

                            <div class="absolute top-3 left-3">
                                <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-gray-900 text-xs font-bold rounded-full shadow-sm">
                                    {{ ucfirst($property->listing_type) }}
                                </span>
                            </div>

                            <div class="absolute top-3 right-3">
                                <form action="{{ route('properties.unsave', $property) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-white/90 backdrop-blur-sm rounded-full text-red-500 hover:bg-white hover:scale-110 transition-all duration-300 shadow-sm">
                                        <x-heroicon-s-heart class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <div class="mb-4">
                                <h3 class="font-bold text-gray-900 text-lg line-clamp-1 group-hover:text-emerald-600 transition-colors">
                                    {{ $property->title }}
                                </h3>
                                <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                                    <x-heroicon-o-map-pin class="w-4 h-4" />
                                    {{ $property->area->name ?? $property->city->name }}, {{ $property->city->name }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                                <p class="text-xl font-bold text-emerald-600">
                                    ₦{{ number_format($property->price) }}
                                    @if($property->listing_type === 'rent') <span class="text-sm font-normal text-gray-500">/year</span> @endif
                                </p>
                                <div class="flex items-center gap-3 text-xs text-gray-500 font-medium">
                                    @if($property->bedrooms) <span>{{ $property->bedrooms }} Beds</span> @endif
                                    @if($property->toilets) <span>{{ $property->toilets }} Baths</span> @endif
                                </div>
                            </div>
                            
                            <a href="{{ route('property.show', $property->slug) }}" class="mt-4 block w-full py-2.5 text-center text-sm font-semibold text-emerald-600 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if(method_exists($savedProperties, 'hasPages') && $savedProperties->hasPages())
                <div class="mt-8">
                    {{ $savedProperties->links() }}
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="text-center py-24 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-heroicon-o-heart class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">No saved properties</h3>
                <p class="text-gray-500 max-w-sm mx-auto mb-8">Start building your dream property collection! Browse our listings and save the properties that catch your eye.</p>
                <a href="{{ route('properties.search') }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 mr-2" />
                    Explore Properties
                </a>
            </div>
        @endif
    </div>
</div>
@endsection