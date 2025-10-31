<!-- Enhanced Saved Properties with Livewire -->
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100 relative overflow-hidden">
    <!-- Subtle Background Elements -->
    <div class="absolute inset-0 opacity-30">
        <div class="floating-element absolute top-1/4 right-1/4 w-32 h-32 bg-gradient-to-br from-purple-400/8 to-pink-500/6 rounded-full blur-3xl"></div>
        <div class="floating-element absolute bottom-1/3 left-1/4 w-40 h-40 bg-gradient-to-br from-blue-400/6 to-indigo-500/4 rounded-full blur-3xl"></div>
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
                        <div class="w-8 h-0.5 bg-gradient-to-r from-gray-300 to-gray-400 rounded-full"></div>
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-gray-400 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>

                    <!-- Current Page -->
                    <div class="flex items-center">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 shadow-lg">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-2">
                            <span class="text-sm lg:text-base font-bold text-gray-900">Saved Properties</span>
                            <div class="text-xs text-gray-500">{{ $stats['total'] }} Properties</div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Content Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($stats['total'] > 0)
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
                                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by title, location, or type..."
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 transition-all duration-300">
                            </div>
                        </div>

                        <!-- Property Type Filter -->
                        <div>
                            <x-forms.select
                                label="Property Type"
                                wire:model.live="propertyType"
                                :options="[
                                    '' => 'All Types',
                                    'rent' => 'For Rent',
                                    'sale' => 'For Sale',
                                    'lease' => 'For Lease',
                                    'shortlet' => 'Shortlet'
                                ]"
                            />
                        </div>

                        <!-- Sort By -->
                        <div>
                            <x-forms.select
                                label="Sort By"
                                wire:model.live="sortBy"
                                :options="[
                                    'saved_date' => 'Date Saved',
                                    'price_low' => 'Price: Low to High',
                                    'price_high' => 'Price: High to Low',
                                    'title' => 'Title A-Z'
                                ]"
                            />
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">
                                <span class="font-medium">{{ $stats['total'] }}</span> properties saved
                            </span>
                            <div class="text-xs text-gray-500">
                                {{ $stats['rent'] }} for rent • {{ $stats['sale'] }} for sale
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-lg hover:shadow-xl">
                                Create Alert
                            </button>
                            <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                                Export List
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Premium Properties Grid with Livewire - Matching Property Search Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8" wire:loading.class="opacity-50">
                    @foreach($savedProperties as $savedProperty)
                        @php $property = $savedProperty->property; @endphp
                        <a href="{{ route('property.show', $property->slug ?? $property->id) }}"
                           class="group block relative bg-white/80 backdrop-blur-2xl rounded-3xl border border-gray-200/40 overflow-hidden hover:bg-white/90 hover:border-gray-300/40 transition-all duration-700 transform hover:scale-[1.02] hover:-translate-y-2 shadow-2xl hover:shadow-3xl">

                            <!-- Premium Property Image -->
                            <div class="relative h-64 lg:h-72 overflow-hidden">
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
                                        <div class="w-20 h-20 bg-white/40 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                @endif

                                <!-- Premium Overlay Gradient -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-60 group-hover:opacity-80 transition-opacity duration-500"></div>

                                <!-- Premium Verified Badge -->
                                @if($property->is_verified)
                                    <div class="absolute top-4 left-4">
                                        <div class="inline-flex items-center space-x-2 bg-blue-100/60 backdrop-blur-xl border border-blue-300/40 text-blue-700 px-3 py-1.5 rounded-xl font-bold text-xs shadow-xl">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Verified</span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Premium Listing Type Badge -->
                                <div class="absolute top-4 right-4">
                                    @if($property->listing_type === 'rent')
                                        <div class="inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-emerald-100/60 to-green-100/60 backdrop-blur-xl border border-emerald-300/40 text-emerald-700 text-xs font-bold rounded-xl shadow-2xl">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                            </svg>
                                            <span>For Rent</span>
                                        </div>
                                    @elseif($property->listing_type === 'sale')
                                        <div class="inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-blue-100/60 to-indigo-100/60 backdrop-blur-xl border border-blue-300/40 text-blue-700 text-xs font-bold rounded-xl shadow-2xl">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                            <span>For Sale</span>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-purple-100/60 to-pink-100/60 backdrop-blur-xl border border-purple-300/40 text-purple-700 text-xs font-bold rounded-xl shadow-2xl capitalize">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span>{{ $property->listing_type }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Date Saved Badge -->
                                <div class="absolute bottom-4 left-4">
                                    <div class="inline-flex items-center space-x-2 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-xl text-xs font-medium text-gray-700 shadow-lg">
                                        <svg class="w-3 h-3 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                        <span>Saved {{ $savedProperty->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Compact Property Details -->
                            <div class="p-4 bg-gradient-to-br from-white/60 via-white/80 to-white/60 backdrop-blur-sm">

                                <!-- Compact Header: Title + Location -->
                                <div class="mb-3">
                                    <h4 class="font-bold text-gray-900 text-base mb-1.5 line-clamp-1 group-hover:text-emerald-600 transition-colors duration-300">
                                        {{ $property->title }}
                                    </h4>
                                    <div class="flex items-center text-xs font-medium text-gray-600">
                                        <svg class="w-3 h-3 mr-1.5 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="truncate">{{ $property->city->name ?? 'Unknown' }}, {{ $property->state->name ?? 'Unknown' }}</span>
                                    </div>
                                </div>

                                <!-- Compact Features Row -->
                                @if($property->bedrooms || $property->bathrooms || $property->floor_area)
                                <div class="flex items-center space-x-3 mb-3">
                                    @if($property->bedrooms)
                                    <div class="flex items-center space-x-1.5 text-xs font-semibold text-gray-900">
                                        <div class="w-5 h-5 bg-emerald-100/60 rounded-md flex items-center justify-center">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                        </div>
                                        <span>{{ $property->bedrooms }} bed</span>
                                    </div>
                                    @endif
                                    @if($property->bedrooms && $property->bathrooms)
                                    <div class="w-px h-4 bg-gray-400"></div>
                                    @endif
                                    @if($property->bathrooms)
                                    <div class="flex items-center space-x-1.5 text-xs font-semibold text-gray-900">
                                        <div class="w-5 h-5 bg-blue-100/60 rounded-md flex items-center justify-center">
                                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                            </svg>
                                        </div>
                                        <span>{{ $property->bathrooms }} bath</span>
                                    </div>
                                    @endif
                                    @if($property->floor_area)
                                    <div class="w-px h-4 bg-gray-400"></div>
                                    <div class="flex items-center space-x-1.5 text-xs font-semibold text-gray-600">
                                        <span>{{ number_format($property->floor_area) }}m²</span>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                <!-- Compact Price + Action Row -->
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="text-lg font-black bg-gradient-to-r from-emerald-600 via-teal-500 to-blue-600 bg-clip-text text-transparent">
                                            ₦{{ number_format($property->price) }}
                                        </div>
                                        @if($property->listing_type === 'rent')
                                            <div class="text-xs font-medium text-gray-500">/year</div>
                                        @endif
                                    </div>

                                    <!-- Compact Action Buttons -->
                                    <div class="flex items-center space-x-2">
                                        <button
                                            wire:click.stop="removeSavedProperty({{ $savedProperty->id }})"
                                            wire:confirm="Are you sure you want to remove this property from your saved list?"
                                            onclick="event.stopPropagation(); event.preventDefault();"
                                            class="group/heart p-2.5 bg-red-50 border-red-300 hover:bg-red-100 backdrop-blur-sm rounded-lg border transition-all duration-300 hover:scale-110"
                                            title="Remove from saved"
                                        >
                                            <!-- Filled heart for saved properties -->
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                        </button>
                                        <button class="group/btn bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-xs font-bold py-2.5 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                            <span class="flex items-center space-x-1.5">
                                                <span>View</span>
                                                <svg class="w-3 h-3 group-hover/btn:translate-x-0.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Livewire Pagination -->
                <div class="mt-12">
                    {{ $savedProperties->links() }}
                </div>

            @else
                <!-- Premium Empty State -->
                <div class="text-center py-16 lg:py-24">
                    <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/40 p-12 lg:p-16 max-w-2xl mx-auto">
                        <div class="w-24 h-24 mx-auto mb-8 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">No Saved Properties Yet</h3>
                        <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                            Start building your dream property collection! Browse our listings and save the properties that catch your eye.
                        </p>
                        <a href="{{ route('properties.search') }}"
                           class="inline-flex items-center space-x-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-8 py-4 rounded-2xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
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

    <!-- Loading Overlay -->
    <div wire:loading.flex class="fixed inset-0 bg-black/20 backdrop-blur-sm z-50 items-center justify-center">
        <div class="bg-white/90 backdrop-blur-xl rounded-2xl p-6 shadow-2xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-600"></div>
                <span class="text-gray-700 font-medium">Loading...</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('property-removed', (message) => {
            // Show a nice notification
            if (typeof window.showNotification === 'function') {
                window.showNotification(message, 'success');
            }
        });
    });
</script>
@endpush