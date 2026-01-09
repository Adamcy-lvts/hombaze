<x-filament-panels::page>
    <div x-data="{ 
            step: @entangle('step'), 
            totalSteps: 4,
            next() { $wire.nextStep() },
            prev() { $wire.previousStep() },
            goTo(s) { $wire.set('step', s) }
         }" 
         class="relative min-h-screen flex flex-col pb-20"
         x-on:keydown.window.prevent.enter="$event.preventDefault();"
    >
        {{-- Wizard Interface --}}
        <div class="flex-1 flex flex-col pt-4">
            {{-- Top Navigation --}}
            <div class="flex items-center justify-between mb-6">
                <button wire:click="cancel" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 flex items-center">
                    <x-heroicon-s-arrow-left class="w-4 h-4 mr-1"/> Cancel
                </button>
                <div class="flex space-x-1">
                    <template x-for="i in totalSteps">
                        <div class="h-1.5 w-6 rounded-full transition-colors duration-300"
                                :class="i <= step ? 'bg-primary-600 dark:bg-primary-500' : 'bg-gray-200 dark:bg-gray-700'"></div>
                    </template>
                </div>
            </div>

            {{-- Steps Container --}}
            <div class="flex-1 relative overflow-hidden px-6">
                
                {{-- Step 2: Cover Image --}}
                <div x-show="step === 2" style="display: none;"
                        x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0 translate-x-full"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-300 transform"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 -translate-x-full"
                        class="absolute inset-0 flex flex-col space-y-6 overflow-y-auto pb-32"
                >
                    <div class="text-center space-y-2">
                        <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-indigo-600 dark:from-primary-400 dark:to-indigo-400">
                            @if($listing_type === 'sale')
                                Captivate Buyers
                            @elseif($listing_type === 'shortlet')
                                Captivate Guests
                            @else
                                Captivate Tenants
                            @endif
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Upload a stunning cover photo to grab attention immediately.</p>
                    </div>

                {{-- Featured Image --}}
                <div x-data="{
                        isProcessing: false,
                        uploadError: null,
                        async handleImageUpload(e) {
                            this.uploadError = null;
                            const file = e.target.files[0];
                            if (!file) return;

                            this.isProcessing = true;

                            try {
                                if (typeof window.validateAndProcessImage !== 'function') {
                                    throw new Error('Image processor not loaded. Please refresh.');
                                }
                                
                                const processedFile = await window.validateAndProcessImage(file);
                                
                                $wire.upload('featured_image', processedFile, 
                                    () => { this.isProcessing = false; }, 
                                    (error) => { this.uploadError = 'Upload failed'; this.isProcessing = false; }
                                );
                            } catch (err) {
                                this.uploadError = err;
                                this.isProcessing = false;
                                e.target.value = ''; 
                            }
                        }
                     }"
                     class="space-y-2"
                >
                   <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Target Audience Cover</label>
                   
                   <div class="relative group cursor-pointer min-h-[250px] border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors overflow-hidden">
                        {{-- Loading Overlay --}}
                        <div x-show="isProcessing" class="absolute inset-0 z-20 bg-white/80 dark:bg-gray-900/80 flex flex-col items-center justify-center backdrop-blur-sm transition-opacity" style="display: none;">
                            <svg class="animate-spin h-8 w-8 text-primary-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-xs font-medium text-primary-600">Optimizing...</p>
                        </div>
                        
                        <div x-show="uploadError" class="absolute inset-0 z-20 bg-white/90 dark:bg-gray-900/90 flex flex-col items-center justify-center p-6 text-center" style="display: none;">
                            <x-heroicon-o-exclamation-circle class="w-12 h-12 text-danger-500 mb-2"/>
                            <p class="text-danger-600 font-medium mb-4" x-text="uploadError"></p>
                            <button @click="uploadError = null" type="button" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200">Try Again</button>
                        </div>

                         <input type="file" accept="image/*" @change="handleImageUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        @if($featured_image)
                            <img src="{{ $featured_image->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-cover">
                        @elseif($existing_featured_image)
                            <img src="{{ $existing_featured_image }}" class="absolute inset-0 w-full h-full object-cover">
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
                    
                    {{-- Property Gallery Section --}}
                    <div class="space-y-4 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Property Gallery
                            </label>
                            <span class="text-xs text-gray-500">Max 10MB per image</span>
                        </div>

                        {{-- Existing Gallery --}}
                        @if($existingGallery && $existingGallery->count() > 0)
                            <div class="grid grid-cols-2 gap-4">
                                @foreach($existingGallery as $media)
                                    <div class="relative group bg-white dark:bg-gray-800 p-2 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                        <div class="relative aspect-video rounded-md overflow-hidden bg-gray-100 mb-2">
                                            <img src="{{ $media->getUrl() }}" class="object-cover w-full h-full">
                                            <button type="button" wire:click="deleteGalleryImage({{ $media->id }})" 
                                                    class="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full shadow-lg hover:bg-red-600 transition-colors"
                                                    onclick="confirm('Delete this image?') || event.stopImmediatePropagation()">
                                                <x-heroicon-m-trash class="w-3 h-3" />
                                            </button>
                                        </div>
                                        <div>
                                            <input type="text" 
                                                   wire:model="existing_gallery_captions.{{ $media->id }}" 
                                                   placeholder="Caption"
                                                   class="w-full text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-md focus:border-primary-500 focus:ring-primary-500 outline-none p-1.5"
                                            >
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- New Uploads --}}
                        <div class="flex items-center justify-center w-full mt-4">
                            <label for="new-gallery-upload" class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <x-heroicon-o-plus class="w-6 h-6 mb-1 text-gray-500 dark:text-gray-400" />
                                    <p class="text-xs text-gray-500 dark:text-gray-400"><span class="font-semibold">Add New Images</span></p>
                                </div>
                                <input id="new-gallery-upload" type="file" multiple wire:model="new_gallery_images" accept="image/*" class="hidden" />
                            </label>
                        </div>
                        @error('new_gallery_images.*') <span class="text-xs text-red-600 dark:text-red-400 block text-center">{{ $message }}</span> @enderror

                        {{-- New Uploads Preview --}}
                        @if($new_gallery_images)
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                @foreach($new_gallery_images as $index => $image)
                                    <div class="relative group bg-white dark:bg-gray-800 p-2 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                                        <div class="relative aspect-video rounded-md overflow-hidden bg-gray-100 mb-2">
                                            <img src="{{ $image->temporaryUrl() }}" class="object-cover w-full h-full">
                                            <button type="button" wire:click="removeNewGalleryImage({{ $index }})" 
                                                    class="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full shadow-lg hover:bg-red-600 transition-colors">
                                                <x-heroicon-m-x-mark class="w-3 h-3" />
                                            </button>
                                        </div>
                                        <div>
                                            <input type="text" 
                                                   wire:model="new_gallery_captions.{{ $index }}" 
                                                   placeholder="Caption"
                                                   class="w-full text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-md focus:border-primary-500 focus:ring-primary-500 outline-none p-1.5"
                                            >
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex space-x-3 pt-4">
                        <button @click="prev()" type="button" class="px-6 py-3.5 bg-white border border-gray-200 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"> Back </button>
                        <button @click="next()" type="button" class="flex-1 py-3.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-medium shadow-lg shadow-primary-500/30 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                            Continue
                        </button>
                    </div>
                </div>
                </div>

                {{-- Step 1: Essentials --}}
                <div x-show="step === 1"
                        x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0 translate-x-full"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-300 transform"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 -translate-x-full"
                        class="absolute inset-0 flex flex-col space-y-6 overflow-y-auto pb-32"
                >
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit Essentials</h2>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Property Type</label>
                                <select wire:model.live="property_type_id" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 pr-10 outline-none">
                                    <option value="">Select Type</option>
                                    @foreach($this->propertyTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('property_type_id') <span class="text-danger-600 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>

                            @if($this->propertySubtypes->isNotEmpty())
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Property Subtype</label>
                                <select wire:model="property_subtype_id" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 pr-10 outline-none">
                                    <option value="">Select Subtype (Optional)</option>
                                    @foreach($this->propertySubtypes as $subtype)
                                        <option value="{{ $subtype->id }}">{{ $subtype->name }}</option>
                                    @endforeach
                                </select>
                                @error('property_subtype_id') <span class="text-danger-600 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                            @endif
                        </div>

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

                    <div class="pt-4">
                        <button @click="next()" type="button" class="w-full py-3.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-medium shadow-lg shadow-primary-500/30 transition-all active:scale-95 text-center"> Continue </button>
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
                        class="absolute inset-0 flex flex-col space-y-6 overflow-y-auto pb-32"
                >
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit Details</h2>

                    <div class="space-y-5">
                        @if(!in_array($selectedCategory, ['land', 'commercial']))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bedrooms</label>
                            <div class="flex items-center space-x-4">
                                    <button type="button" @click="$wire.set('bedrooms', Math.max(0, $wire.bedrooms - 1))" class="w-12 h-12 rounded-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white flex items-center justify-center text-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">-</button>
                                    <span class="text-3xl font-bold w-12 text-center text-gray-900 dark:text-white" x-text="$wire.bedrooms || 0"></span>
                                    <button type="button" @click="$wire.set('bedrooms', ($wire.bedrooms || 0) + 1)" class="w-12 h-12 rounded-full bg-primary-600 text-white border border-primary-600 flex items-center justify-center text-xl font-bold hover:bg-primary-700 transition-colors">+</button>
                            </div>
                            <input type="hidden" wire:model="bedrooms">
                            @error('bedrooms') <span class="text-danger-600 text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        @if($selectedCategory === 'land')
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plot Size</label>
                                <button type="button" @click="$wire.set('useCustomPlotSize', ! $wire.useCustomPlotSize)" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
                                    {{ $useCustomPlotSize ? 'Select Standard Size' : 'Enter Custom Size' }}
                                </button>
                            </div>
                            
                            <div x-show="!$wire.useCustomPlotSize">
                                <select wire:model="plot_size_id" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 pr-10 outline-none">
                                    <option value="">Select Plot Size</option>
                                    @foreach($this->plotSizes as $size)
                                        <option value="{{ $size->id }}">{{ $size->display_text }}</option>
                                    @endforeach
                                </select>
                                @error('plot_size_id') <span class="text-danger-600 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div x-show="$wire.useCustomPlotSize" style="display: none;">
                                <div class="flex space-x-3">
                                    <div class="flex-1">
                                        <input type="number" wire:model="custom_plot_size" placeholder="Size" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 outline-none">
                                        @error('custom_plot_size') <span class="text-danger-600 text-xs block mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="w-1/3">
                                        <select wire:model="custom_plot_unit" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 pr-10 outline-none">
                                            <option value="sqm">sqm</option>
                                            <option value="sqft">sqft</option>
                                            <option value="acres">acres</option>
                                            <option value="hectares">hectares</option>
                                            <option value="plots">plots</option>
                                        </select>
                                        @error('custom_plot_unit') <span class="text-danger-600 text-xs block mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

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
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit Location</h2>

                    <div class="space-y-5">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">State</label>
                                <select wire:model.live="state_id" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 pr-10 outline-none">
                                    <option value="">Select State</option>
                                    @foreach($this->states as $state)
                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                @error('state_id') <span class="text-danger-600 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City</label>
                                <select wire:model.live="city_id" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500/50 transition-colors py-3.5 px-4 pr-10 outline-none" @if(!$state_id) disabled @endif>
                                    <option value="">Select City</option>
                                    @foreach($this->cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                @error('city_id') <span class="text-danger-600 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Area</label>
                            
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
                        <button wire:click="update" wire:loading.attr="disabled" type="button" class="flex-1 py-3.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium shadow-lg shadow-green-500/30 transition-all active:scale-95 flex items-center justify-center space-x-2"> 
                            <span wire:loading.remove>Update Property</span>
                            <span wire:loading>Updating...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Inline Image Processor (Copied from create-property) to ensure consistency
        window.validateAndProcessImage = async function(file) {
            const MIN_WIDTH = 1024;
            const MIN_HEIGHT = 768;
            const MAX_WIDTH = 2560;
            const MAX_HEIGHT = 1920;
            const QUALITY = 0.8;

            return new Promise((resolve, reject) => {
                const reader = new FileReader();

                reader.onload = (e) => {
                    const img = new Image();

                    img.onload = () => {
                        const width = img.naturalWidth;
                        const height = img.naturalHeight;

                        if (width < MIN_WIDTH || height < MIN_HEIGHT) {
                            reject(`This photo is too small (low quality). Please select a clearer photo at least ${MIN_WIDTH}x${MIN_HEIGHT}px.`);
                            return;
                        }

                        // 2. Enforce 4:3 Aspect Ratio (Center Crop)
                        const TARGET_RATIO = 4 / 3;
                        const sourceRatio = width / height;

                        let srcX = 0;
                        let srcY = 0;
                        let srcW = width;
                        let srcH = height;

                        if (sourceRatio > TARGET_RATIO) {
                            // Image is wider than 4:3 - Crop sides
                            srcW = height * TARGET_RATIO;
                            srcX = (width - srcW) / 2;
                        } else {
                            // Image is taller than 4:3 - Crop top/bottom
                            srcH = width / TARGET_RATIO;
                            srcY = (height - srcH) / 2;
                        }

                        // 3. Determine Final Output Size (Max 2560x1920)
                        let targetWidth = srcW;
                        let targetHeight = srcH;

                        if (targetWidth > MAX_WIDTH) {
                            targetWidth = MAX_WIDTH;
                            targetHeight = MAX_WIDTH / TARGET_RATIO;
                        }

                        targetWidth = Math.round(targetWidth);
                        targetHeight = Math.round(targetHeight);

                        // 4. Draw to Canvas
                        const canvas = document.createElement('canvas');
                        canvas.width = targetWidth;
                        canvas.height = targetHeight;
                        const ctx = canvas.getContext('2d');

                        // Better quality resizing
                        ctx.imageSmoothingEnabled = true;
                        ctx.imageSmoothingQuality = 'high';

                        ctx.drawImage(img, srcX, srcY, srcW, srcH, 0, 0, targetWidth, targetHeight);

                        canvas.toBlob((blob) => {
                            if (!blob) {
                                reject("We couldn't process this image. Please try another one.");
                                return;
                            }

                            const safeName = `property-upload-${Date.now()}.jpg`;
                            const newFile = new File([blob], safeName, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });

                            resolve(newFile);
                        }, 'image/jpeg', QUALITY);
                    };

                    img.onerror = () => reject("This file doesn't look like a valid image.");
                    img.src = e.target.result;
                };

                reader.onerror = () => reject("We couldn't read this file. Please try again.");
                reader.readAsDataURL(file);
            });
        };
    </script>
</x-filament-panels::page>
