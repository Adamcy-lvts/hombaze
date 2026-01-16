<x-filament-panels::page>
    <div class="space-y-6 pb-20"> <!-- Padding for bottom nav -->
        
        <div class="flex items-center justify-between">
            {{-- Optional: Top Add Button --}}
        </div>

        @if($properties->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center space-y-4">
                <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-full text-gray-400">
                    <x-heroicon-o-building-office-2 class="w-12 h-12" />
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">No Properties Yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm max-w-xs mx-auto">Start building your portfolio by adding your first property.</p>
                </div>
                <!-- Updated Route for Agent -->
                <a href="{{ route('filament.agent.pages.create-property') }}" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    Add Property
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4">
                @foreach($properties as $property)
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden flex flex-col">
                        {{-- Image --}}
                        <div class="relative h-48 w-full bg-gray-200 dark:bg-gray-800">
                            @if($property->getFirstMediaUrl('featured'))
                                <img src="{{ $property->getFirstMediaUrl('featured') }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full text-gray-400">
                                    <x-heroicon-o-camera class="w-12 h-12" />
                                </div>
                            @endif
                            
                            {{-- Badge --}}
                            <button wire:click="mountAction('changeStatus', { record: {{ $property->id }} })" 
                                    class="absolute top-3 right-3 shadow-sm hover:scale-105 transition-transform">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium 
                                    {{ $property->status === 'available' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                    {{ $property->status === 'rented' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                    {{ $property->status === 'sold' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                ">
                                    {{ ucfirst($property->status) }}
                                </span>
                            </button>
                        </div>

                        {{-- Content --}}
                        <div class="p-4 flex-1 flex flex-col">
                            <div class="flex-1 space-y-2">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white line-clamp-1">{{ $property->title }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $property->address }}</p>
                                    </div>
                                    <p class="text-primary-600 dark:text-primary-400 font-bold whitespace-nowrap">
                                        ₦{{ number_format($property->price) }}
                                    </p>
                                </div>
                                
                                <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <x-heroicon-m-home class="w-3.5 h-3.5 mr-1" />
                                        {{ $property->propertyType->name ?? 'Property' }}
                                    </div>
                                    @if($property->bedrooms)
                                    <div class="flex items-center">
                                        <span class="mr-1">&bull;</span> {{ $property->bedrooms }} Beds
                                    </div>
                                    @endif
                                    <div class="flex items-center">
                                        <span class="mr-1">&bull;</span> {{ ucfirst($property->listing_type) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                                <!-- Updated Route for Agent -->
                                <a href="{{ route('filament.agent.pages.edit-property', ['record' => $property->id]) }}" 
                                   class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                    <x-heroicon-m-pencil-square class="w-4 h-4 mr-1.5" />
                                    Edit
                                </a>

                                <button wire:click="mountAction('changeStatus', { record: {{ $property->id }} })"
                                        class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <x-heroicon-m-arrow-path class="w-4 h-4 mr-1.5" />
                                    Status
                                </button>
                                
                                <button wire:click="mountAction('delete', { record: {{ $property->id }} })" 
                                        class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-danger-600 dark:hover:text-danger-400 transition-colors">
                                    <x-heroicon-m-trash class="w-4 h-4 mr-1.5" />
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
        <x-filament-actions::modals />
    </div>
</x-filament-panels::page>
