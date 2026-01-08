<x-filament-panels::page>
    <div x-data="{ 
            step: @entangle('step'), 
            totalSteps: 4,
            next() { $wire.nextStep() },
            prev() { $wire.previousStep() },
            goTo(s) { $wire.set('step', s) }
         }" 
         class="relative min-h-screen flex flex-col pb-20"
         x-on:keydown.window.prevent.enter="$event.preventDefault();" {{-- Prevent enter key submission --}}
    >
        
        {{-- Selection Screen --}}
        @if(!$this->selectedCategory)
            <div class="flex flex-col items-center justify-center space-y-6 pt-10 animate-fade-in">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">What define's this property?</h2>
                
                <button wire:click="selectCategory('residential')" class="w-full max-w-sm p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-all group">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-xl text-primary-600 dark:text-primary-400 group-hover:scale-110 transition-transform">
                            <x-heroicon-o-home class="w-8 h-8" />
                        </div>
                        <div class="text-left">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Residential</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Apartments, Houses, Condos</p>
                        </div>
                    </div>
                </button>

                <button wire:click="selectCategory('commercial')" class="w-full max-w-sm p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-all group">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                            <x-heroicon-o-building-office-2 class="w-8 h-8" />
                        </div>
                        <div class="text-left">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Commercial</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Offices, Shops, Warehouses</p>
                        </div>
                    </div>
                </button>
            </div>
        @else
            {{-- Wizard Interface --}}
            <div class="flex-1 flex flex-col">
                {{-- Top Navigation --}}
                <div class="flex items-center justify-between mb-6">
                    <button wire:click="$set('selectedCategory', null)" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 flex items-center">
                        <x-heroicon-s-arrow-left class="w-4 h-4 mr-1"/> Back
                    </button>
                    <div class="flex space-x-1">
                        <template x-for="i in totalSteps">
                            <div class="h-1.5 w-6 rounded-full transition-colors duration-300"
                                 :class="i <= step ? 'bg-primary-600 dark:bg-primary-500' : 'bg-gray-200 dark:bg-gray-700'"></div>
                        </template>
                    </div>
                </div>

                {{-- Steps Container --}}
                <div class="flex-1 relative overflow-hidden px-6"> {{-- Added px-6 for page padding --}}
                    
                    {{-- Step 1: Cover Image --}}
                    <div x-show="step === 1" 
                         {{-- ... existing transitions ... --}}
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 translate-x-full"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-300 transform"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-full"
                         class="absolute inset-0 flex flex-col space-y-6"
                    >
                        <div class="text-center space-y-2">
                            <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-indigo-600 dark:from-primary-400 dark:to-indigo-400">
                                Captivate Buyers
                            </h2>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">Upload a stunning cover photo to grab attention immediately.</p>
                        </div>

                        <div class="relative group cursor-pointer flex-1 min-h-[300px] border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-3xl flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors overflow-hidden">
                            
                            <input type="file" wire:model="featured_image" accept="image/*" capture="environment" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                            @if($featured_image)
                                <img src="{{ $featured_image->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-cover">
                                <div class="absolute bottom-4 right-4 bg-black/50 text-white px-3 py-1 rounded-full text-xs backdrop-blur-sm pointer-events-none">
                                    Tap to change
                                </div>
                            @else
                                <div class="flex flex-col items-center text-center p-6 space-y-3 pointer-events-none">
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded-full shadow-lg text-primary-600 dark:text-primary-400">
                                        <x-heroicon-o-camera class="w-8 h-8" />
                                    </div>
                                    <p class="font-medium text-gray-900 dark:text-white">Take a Photo</p>
                                    <p class="text-xs text-gray-500">or upload from gallery</p>
                                </div>
                            @endif
                        </div>
                        @error('featured_image') <span class="text-danger-600 dark:text-danger-400 text-sm block text-center">{{ $message }}</span> @enderror
                        
                        <div class="pt-4">
                            <button @click="next()" type="button" class="w-full py-3.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-medium shadow-lg shadow-primary-500/30 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                                Continue
                            </button>
                        </div>
                    </div>

                    {{-- Step 2: Essentials --}}
                    <div x-show="step === 2" style="display: none;"
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 translate-x-full"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-300 transform"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-full"
                         class="absolute inset-0 flex flex-col space-y-6"
                    >
                         <h2 class="text-xl font-bold text-gray-900 dark:text-white">The Essentials</h2>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Property Title</label>
                                <input type="text" wire:model.live="propertyTitle" placeholder="e.g. Modern 3-Bed Apartment" 
                                       class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                                @error('propertyTitle') <span class="text-danger-600 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price (₦)</label>
                                <input type="number" wire:model="price" placeholder="0.00" 
                                       class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                                @error('price') <span class="text-danger-600 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Listing Type</label>
                                    <select wire:model="listing_type" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                                        <option value="rent">Rent</option>
                                        <option value="sale">Sale</option>
                                        <option value="lease">Lease</option>
                                        <option value="shortlet">Shortlet</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                    <select wire:model="status" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                                        <option value="available">Available</option>
                                        <option value="rented">Rented</option>
                                        <option value="sold">Sold</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <button @click="prev()" type="button" class="px-6 py-3.5 bg-white border border-gray-200 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"> Back </button>
                            <button @click="next()" type="button" class="flex-1 py-3.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-medium shadow-lg shadow-primary-500/30 transition-all active:scale-95"> Continue </button>
                        </div>
                    </div>


                     {{-- Step 3: Details --}}
                     <div x-show="step === 3" style="display: none;"
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 translate-x-full"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-300 transform"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-full"
                         class="absolute inset-0 flex flex-col space-y-6"
                    >
                         <h2 class="text-xl font-bold text-gray-900 dark:text-white">Key Features</h2>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bedrooms</label>
                                <div class="flex items-center space-x-4">
                                     <button type="button" @click="$wire.set('bedrooms', Math.max(0, $wire.bedrooms - 1))" class="w-12 h-12 rounded-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white flex items-center justify-center text-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">-</button>
                                     <span class="text-3xl font-bold w-12 text-center text-gray-900 dark:text-white" x-text="$wire.bedrooms || 0"></span>
                                     <button type="button" @click="$wire.set('bedrooms', ($wire.bedrooms || 0) + 1)" class="w-12 h-12 rounded-full bg-primary-600 text-white border border-primary-600 flex items-center justify-center text-xl font-bold hover:bg-primary-700 transition-colors">+</button>
                                </div>
                                <input type="hidden" wire:model="bedrooms"> {{-- Hidden bound input --}}
                                @error('bedrooms') <span class="text-danger-600 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description (Optional)</label>
                                <textarea wire:model="description" rows="4" placeholder="Highlight unique features..." 
                                       class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none"></textarea>
                            </div>
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <button @click="prev()" type="button" class="px-6 py-3.5 bg-white border border-gray-200 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"> Back </button>
                            <button @click="next()" type="button" class="px-6 py-3.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-medium shadow-lg shadow-primary-500/30 transition-all active:scale-95"> Continue </button>
                        </div>
                    </div>

                    {{-- Step 4: Location --}}
                     <div x-show="step === 4" style="display: none;"
                         x-transition:enter="transition ease-out duration-300 transform"
                         x-transition:enter-start="opacity-0 translate-x-full"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-300 transform"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-full"
                         class="absolute inset-0 flex flex-col space-y-6"
                    >
                         <h2 class="text-xl font-bold text-gray-900 dark:text-white">Location</h2>

                        <div class="space-y-5">
                            
                            {{-- State & City Auto-filled visual --}}
                            <div class="flex space-x-3 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <x-heroicon-o-map-pin class="w-5 h-5 text-primary-500" />
                                <div class="text-sm">
                                    <p class="text-gray-900 dark:text-white font-medium">Auto-located in:</p>
                                    <p class="text-gray-500 dark:text-gray-400">
                                        {{ \App\Models\State::find($state_id)?->name ?? 'Unknown State' }}, 
                                        {{ \App\Models\City::find($city_id)?->name ?? 'Unknown City' }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Area</label>
                                
                                {{-- Searchable Area Select --}}
                                <div class="relative" x-data="{ open: false }">
                                    <input type="text" 
                                           wire:model.live="areaSearch" 
                                           placeholder="Search Area..." 
                                           @focus="open = true" 
                                           @click.outside="open = false"
                                           class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 pr-10 outline-none"
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400"/>
                                    </div>
                                    
                                    <div x-show="open" 
                                         class="absolute z-20 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg max-h-60 overflow-y-auto"
                                         style="display: none;">
                                        @if($this->getFilteredAreasProperty()->count() > 0)
                                            <ul class="py-1">
                                                @foreach($this->getFilteredAreasProperty() as $area)
                                                    <li @click="$wire.selectArea({{ $area->id }}); open = false" 
                                                        class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer flex items-center justify-between group">
                                                        <span class="text-gray-900 dark:text-gray-200 group-hover:text-primary-600 dark:group-hover:text-primary-400">{{ $area->name }}</span>
                                                        @if($area_id === $area->id)
                                                            <x-heroicon-s-check class="w-5 h-5 text-primary-600"/>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">No areas found.</div>
                                        @endif
                                    </div>
                                </div>
                                @error('area_id') <span class="text-danger-600 text-xs">{{ $message }}</span> @enderror
                            </div>

                             <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                                <textarea wire:model="address" rows="2" placeholder="Street number, building name..." 
                                       class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none"></textarea>
                                @error('address') <span class="text-danger-600 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex space-x-3 pt-4 pb-10">
                            <button @click="prev()" type="button" class="px-6 py-3.5 bg-white border border-gray-200 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"> Back </button>
                            <button wire:click="create" wire:loading.attr="disabled" type="button" class="flex-1 py-3.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium shadow-lg shadow-green-500/30 transition-all active:scale-95 flex items-center justify-center space-x-2"> 
                                <span wire:loading.remove>Publish Property</span>
                                <span wire:loading>Publishing...</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
