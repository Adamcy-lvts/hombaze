<div class="min-h-screen bg-gray-50 font-sans text-gray-900">
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

        @if($stats['total'] > 0)
            <!-- Filters -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <div class="relative">
                            <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search properties..."
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl text-sm transition-all duration-200">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <x-forms.select
                            wire:model.live="propertyType"
                            :options="[
                                '' => 'All Types',
                                'rent' => 'For Rent',
                                'sale' => 'For Sale',
                                'lease' => 'For Lease',
                                'shortlet' => 'Shortlet'
                            ]"
                            class="w-full py-2.5 px-4 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl text-sm transition-all duration-200"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <x-forms.select
                            wire:model.live="sortBy"
                            :options="[
                                'saved_date' => 'Date Saved',
                                'price_low' => 'Price: Low to High',
                                'price_high' => 'Price: High to Low',
                                'title' => 'Title A-Z'
                            ]"
                            class="w-full py-2.5 px-4 bg-gray-50 border-transparent focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl text-sm transition-all duration-200"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-50">
                    <div class="text-sm text-gray-500">
                        <span class="font-bold text-gray-900">{{ $stats['total'] }}</span> properties saved
                        <span class="mx-2">&bull;</span>
                        {{ $stats['rent'] }} for rent, {{ $stats['sale'] }} for sale
                    </div>
                    <div class="flex gap-3">
                        <button class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Create Alert</button>
                        <button class="text-sm font-medium text-gray-500 hover:text-gray-700">Export List</button>
                    </div>
                </div>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" wire:loading.class="opacity-50">
                @foreach($savedProperties as $savedProperty)
                    @php $property = $savedProperty->property; @endphp
                    <div class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <!-- Image & Badges -->
                        <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
                            @if($property->getMedia('featured')->count() > 0)
                                <img src="{{ $property->getFirstMedia('featured')->getUrl() }}"
                                     alt="{{ $property->title }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @elseif($property->getMedia('gallery')->count() > 0)
                                <img src="{{ $property->getMedia('gallery')->first()->getUrl() }}"
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
                                <button wire:click.stop="removeSavedProperty({{ $savedProperty->id }})"
                                        wire:confirm="Are you sure you want to remove this property?"
                                        class="p-2 bg-white/90 backdrop-blur-sm rounded-full text-red-500 hover:bg-white hover:scale-110 transition-all duration-300 shadow-sm">
                                    <x-heroicon-s-heart class="w-4 h-4" />
                                </button>
                            </div>

                            @if($property->is_verified)
                                <div class="absolute bottom-3 left-3">
                                    <span class="px-2 py-1 bg-blue-500/90 backdrop-blur-sm text-white text-xs font-bold rounded-lg shadow-sm flex items-center gap-1">
                                        <x-heroicon-s-check-badge class="w-3 h-3" />
                                        Verified
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <div class="mb-4">
                                <h3 class="font-bold text-gray-900 text-lg line-clamp-1 group-hover:text-emerald-600 transition-colors">
                                    {{ $property->title }}
                                </h3>
                                <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                                    <x-heroicon-o-map-pin class="w-4 h-4" />
                                    {{ $property->city->name ?? 'Unknown' }}, {{ $property->state->name ?? 'Unknown' }}
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
                            
                            <a href="{{ route('property.show', $property->slug ?? $property->id) }}" class="mt-4 block w-full py-2.5 text-center text-sm font-semibold text-emerald-600 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $savedProperties->links() }}
            </div>

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

    <!-- Loading Overlay -->
    <div wire:loading.flex class="fixed inset-0 bg-black/20 backdrop-blur-sm z-50 items-center justify-center">
        <div class="bg-white rounded-xl p-4 shadow-lg flex items-center gap-3">
            <div class="animate-spin h-5 w-5 border-2 border-emerald-500 border-t-transparent rounded-full"></div>
            <span class="text-sm font-medium text-gray-700">Loading...</span>
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