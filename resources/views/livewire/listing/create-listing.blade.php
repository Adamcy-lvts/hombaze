<div>
    {{-- Top bar with close button --}}
    <div class="fixed top-0 inset-x-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-100/50 flex items-center justify-center text-emerald-600 shadow-sm">
                    <x-heroicon-s-arrow-left class="w-4 h-4" />
                </div>
                <span class="font-bold text-gray-900 tracking-tight hidden sm:block">Exit Creation</span>
            </a>
            <div class="flex items-center">
                <x-application-logo class="h-6 w-auto text-emerald-600" />
            </div>
            <div class="w-[85px]"></div>
        </div>
    </div>

    <!-- Main Content Container with proper padding for fixed header -->
    <div class="pt-20 w-full flex justify-center bg-gray-50 min-h-screen">
        <div class="w-full max-w-2xl bg-white md:shadow-xl md:shadow-gray-200/50 md:rounded-3xl md:my-8 md:border border-gray-100 relative flex flex-col min-h-[calc(100vh-5rem)] md:min-h-[auto] md:h-[800px] overflow-hidden">
            
            <div x-data="{ 
                    step: @entangle('step'), 
                    totalSteps: 4,
                    next() { $wire.nextStep() },
                    prev() { $wire.previousStep() },
                    goTo(s) { $wire.set('step', s) }
                 }" 
                 class="relative flex-1 flex flex-col h-full"
                 x-on:keydown.window.prevent.enter="$event.preventDefault();"
            >
                @if (session()->has('error'))
                    <div class="m-4 p-4 mb-0 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
                
                {{-- Selection Screen --}}
                @if(!$this->selectedCategory)
                    <div class="flex-1 overflow-y-auto w-full p-6 animate-fade-in flex flex-col items-center pt-10 pb-20 space-y-6">
                        <div class="text-center space-y-2 mb-4 w-full">
                            <h2 class="text-2xl font-bold text-gray-900">What type of property?</h2>
                            <p class="text-gray-500 block">Select the category that best describes your listing</p>
                        </div>
                        
                        <button wire:click="selectCategory('residential')" class="w-full max-w-sm p-6 bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-all group">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600 group-hover:scale-110 transition-transform">
                                    <x-heroicon-o-home class="w-8 h-8" />
                                </div>
                                <div class="text-left">
                                    <h3 class="text-lg font-semibold text-gray-900">Residential</h3>
                                    <p class="text-sm text-gray-500">Apartments, Houses, Condos</p>
                                </div>
                            </div>
                        </button>

                        <button wire:click="selectCategory('commercial')" class="w-full max-w-sm p-6 bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-all group">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 bg-blue-50 rounded-xl text-blue-600 group-hover:scale-110 transition-transform">
                                    <x-heroicon-o-building-office-2 class="w-8 h-8" />
                                </div>
                                <div class="text-left">
                                    <h3 class="text-lg font-semibold text-gray-900">Commercial</h3>
                                    <p class="text-sm text-gray-500">Offices, Shops, Warehouses</p>
                                </div>
                            </div>
                        </button>

                        <button wire:click="selectCategory('land')" class="w-full max-w-sm p-6 bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-all group">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 bg-amber-50 rounded-xl text-amber-600 group-hover:scale-110 transition-transform">
                                    <x-heroicon-o-map class="w-8 h-8" />
                                </div>
                                <div class="text-left">
                                    <h3 class="text-lg font-semibold text-gray-900">Land</h3>
                                    <p class="text-sm text-gray-500">Plots, Acres, Farm Land</p>
                                </div>
                            </div>
                        </button>
                    </div>
                @else
                    {{-- Wizard Interface --}}
                    <div class="flex-1 flex flex-col h-full bg-white">
                        {{-- Top Navigation --}}
                        <div class="flex items-center justify-between p-6 pb-2 border-b border-gray-100 bg-white z-10 shrink-0">
                            <button wire:click="$set('selectedCategory', null)" class="text-sm text-gray-500 hover:text-gray-700 flex items-center p-2 -ml-2 rounded-lg hover:bg-gray-50 transition-colors">
                                <x-heroicon-s-arrow-left class="w-5 h-5"/>
                            </button>
                            <div class="flex space-x-1.5 px-2">
                                <template x-for="i in totalSteps">
                                    <div class="h-1.5 w-6 rounded-full transition-colors duration-300"
                                         :class="i <= step ? 'bg-emerald-600' : 'bg-gray-200'"></div>
                                </template>
                            </div>
                        </div>

                        {{-- Steps Container with overflow --}}
                        <div class="flex-1 relative overflow-x-hidden overflow-y-auto">
                            
                            {{-- Container for steps to enforce padding without breaking absolute positioning --}}
                            <div class="absolute inset-0 w-full h-full">

                            {{-- Step 1: Essentials --}}
                            <div x-show="step === 1"
                                 x-transition:enter="transition ease-out duration-300 transform"
                                 x-transition:enter-start="opacity-0 translate-x-full"
                                 x-transition:enter-end="opacity-100 translate-x-0"
                                 x-transition:leave="transition ease-in duration-300 transform"
                                 x-transition:leave-start="opacity-100 translate-x-0"
                                 x-transition:leave-end="opacity-0 -translate-x-full"
                                 class="absolute inset-0 flex flex-col space-y-6 px-6 py-6 pb-24 overflow-y-auto bg-white w-full h-full"
                            >
                                <h2 class="text-xl font-bold text-gray-900">The Essentials</h2>

                                <div class="space-y-5">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Property Type</label>
                                            <select wire:model.live="property_type_id" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/50 transition-colors py-3.5 px-4 outline-none appearance-none">
                                                <option value="">Select Type</option>
                                                @foreach($this->propertyTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('property_type_id') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        @if($this->propertySubtypes->isNotEmpty())
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Property Subtype</label>
                                            <select wire:model="property_subtype_id" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/50 transition-colors py-3.5 px-4 outline-none appearance-none">
                                                <option value="">Select Subtype (Optional)</option>
                                                @foreach($this->propertySubtypes as $subtype)
                                                    <option value="{{ $subtype->id }}">{{ $subtype->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('property_subtype_id') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        @endif
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Property Title</label>
                                        <input type="text" wire:model.live="propertyTitle" placeholder="e.g. Modern 3-Bed Apartment" 
                                               class="w-full rounded-xl border-gray-300 bg-white text-gray-900 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/50 transition-colors py-3.5 px-4 outline-none">
                                        @error('propertyTitle') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Listing Type</label>
                                            <select wire:model="listing_type" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/50 transition-colors py-3.5 px-4 outline-none appearance-none">
                                                <option value="rent">Rent</option>
                                                <option value="sale">Sale</option>
                                                <option value="lease">Lease</option>
                                                <option value="shortlet">Shortlet</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Price (₦)</label>
                                            <input type="number" wire:model="price" placeholder="0.00" 
                                                   class="w-full rounded-xl border-gray-300 bg-white text-gray-900 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/50 transition-colors py-3.5 px-4 outline-none">
                                            @error('price') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    
                                </div>

                                <div class="pt-6 mt-auto shrink-0 pb-10 md:pb-0">
                                    <button @click="next()" type="button" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium shadow-lg shadow-emerald-500/30 transition-all active:scale-95 text-center"> Continue </button>
                                </div>
                            </div>

                            {{-- Step 2: Cover Image --}}
                            <div x-show="step === 2" style="display: none;"
                                 x-transition:enter="transition ease-out duration-300 transform"
                                 x-transition:enter-start="opacity-0 translate-x-full"
                                 x-transition:enter-end="opacity-100 translate-x-0"
                                 x-transition:leave="transition ease-in duration-300 transform"
                                 x-transition:leave-start="opacity-100 translate-x-0"
                                 x-transition:leave-end="opacity-0 -translate-x-full"
                                 class="absolute inset-0 flex flex-col space-y-6 px-6 py-6 pb-24 overflow-y-auto bg-white w-full h-full"
                            >
                                <div class="text-center space-y-2">
                                    <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-teal-600">
                                        Captivate Everyone
                                    </h2>
                                    <p class="text-gray-500 text-sm">Upload a stunning cover photo to grab attention immediately.</p>
                                </div>

                                <div class="relative group cursor-pointer w-full min-h-[300px] border-2 border-dashed border-gray-300 rounded-3xl flex flex-col items-center justify-center bg-gray-50 hover:bg-gray-100 transition-colors overflow-hidden shrink-0"
                                     x-data="{
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
                                >
                                    {{-- Loading Overlay --}}
                                    <div x-show="isProcessing" class="absolute inset-0 z-20 bg-white/80 flex flex-col items-center justify-center backdrop-blur-sm transition-opacity" style="display: none;">
                                        <svg class="animate-spin h-10 w-10 text-emerald-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <p class="text-sm font-medium text-emerald-600">Optimizing Image...</p>
                                    </div>

                                    {{-- Error Overlay --}}
                                     <div x-show="uploadError" class="absolute inset-0 z-20 bg-white/90 flex flex-col items-center justify-center p-6 text-center" style="display: none;">
                                        <x-heroicon-o-exclamation-circle class="w-12 h-12 text-red-500 mb-2"/>
                                        <p class="text-red-600 font-medium mb-4" x-text="uploadError"></p>
                                        <button @click="uploadError = null" type="button" class="px-4 py-2 bg-gray-100 rounded-lg text-sm text-gray-700 hover:bg-gray-200">Try Again</button>
                                    </div>

                                    <input type="file" accept="image/*" @change="handleImageUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                                    @if($featured_image)
                                        <img src="{{ $featured_image->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-cover">
                                        <div class="absolute bottom-4 right-4 bg-black/50 text-white px-3 py-1 rounded-full text-xs backdrop-blur-sm pointer-events-none z-10">
                                            Tap to change
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center text-center p-6 space-y-3 pointer-events-none">
                                            <div class="p-4 bg-white rounded-full shadow-lg text-emerald-600">
                                                <x-heroicon-o-camera class="w-8 h-8" />
                                            </div>
                                            <p class="font-medium text-gray-900">Take a Photo</p>
                                            <p class="text-xs text-gray-500">or upload from gallery</p>
                                            <p class="text-[10px] text-gray-400">Min: 1024x768 • Max: 5MP</p>
                                        </div>
                                    @endif
                                </div>
                                @error('featured_image') <span class="text-red-600 text-sm block text-center">{{ $message }}</span> @enderror
                                
                                {{-- Gallery Section --}}
                                <div class="space-y-4 pt-6 border-t border-gray-100 shrink-0">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Property Gallery (Optional)
                                        </label>
                                        <span class="text-xs text-gray-500">Max 10MB/image</span>
                                    </div>

                                    {{-- Gallery Uploader --}}
                                    <div class="flex items-center justify-center w-full">
                                        <label for="gallery-upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <x-heroicon-o-photo class="w-8 h-8 mb-2 text-gray-500" />
                                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> gallery images</p>
                                            </div>
                                            <input id="gallery-upload" type="file" multiple wire:model="gallery_images" accept="image/*" class="hidden" />
                                        </label>
                                    </div>
                                    @error('gallery_images.*') <span class="text-xs text-red-600 block text-center">{{ $message }}</span> @enderror

                                    {{-- Gallery Preview Grid --}}
                                    @if($gallery_images && count($gallery_images) > 0)
                                        <div class="grid grid-cols-2 gap-4 mt-4">
                                            @foreach($gallery_images as $index => $image)
                                                <div class="relative group bg-white p-2 rounded-lg border border-gray-200 shadow-sm">
                                                    <div class="relative aspect-video rounded-md overflow-hidden bg-gray-100 mb-2">
                                                        <img src="{{ $image->temporaryUrl() }}" class="object-cover w-full h-full">
                                                        <button type="button" wire:click="removeGalleryImage({{ $index }})" 
                                                                class="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full shadow-lg hover:bg-red-600 transition-colors">
                                                            <x-heroicon-m-x-mark class="w-3 h-3" />
                                                        </button>
                                                    </div>
                                                    <div>
                                                        <input type="text" 
                                                               wire:model="gallery_captions.{{ $index }}" 
                                                               placeholder="Caption (e.g. Kitchen)"
                                                               class="w-full text-xs border-gray-300 rounded-md focus:border-emerald-500 focus:ring-emerald-500 outline-none p-1.5"
                                                        >
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex space-x-3 pt-6 mt-auto shrink-0 pb-10 md:pb-0">
                                    <button @click="prev()" type="button" class="px-6 py-3.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors"> Back </button>
                                    <button @click="next()" type="button" class="flex-1 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
                                        Continue
                                    </button>
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
                                 class="absolute inset-0 flex flex-col space-y-6 px-6 py-6 pb-24 overflow-y-auto bg-white w-full h-full"
                            >
                                <h2 class="text-xl font-bold text-gray-900">Key Features</h2>

                                <div class="space-y-5">
                                    @if(!in_array($selectedCategory, ['land', 'commercial']))
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Bedrooms</label>
                                        <div class="flex items-center space-x-4">
                                            <button type="button" @click="$wire.set('bedrooms', Math.max(0, $wire.bedrooms - 1))" class="w-14 h-14 rounded-full border border-gray-300 bg-white text-gray-900 flex items-center justify-center text-xl font-bold hover:bg-gray-50 transition-colors shrink-0">-</button>
                                            <span class="text-3xl font-bold w-12 text-center text-gray-900 shrink-0" x-text="$wire.bedrooms || 0"></span>
                                            <button type="button" @click="$wire.set('bedrooms', ($wire.bedrooms || 0) + 1)" class="w-14 h-14 rounded-full bg-emerald-600 text-white border border-emerald-600 flex items-center justify-center text-xl font-bold hover:bg-emerald-700 transition-colors shrink-0">+</button>
                                        </div>
                                        <input type="hidden" wire:model="bedrooms">
                                        @error('bedrooms') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    @endif

                                    @if($selectedCategory === 'land')
                                    <div>
                                        <div class="flex justify-between items-center mb-1">
                                            <label class="block text-sm font-medium text-gray-700">Plot Size</label>
                                            <button type="button" @click="$wire.set('useCustomPlotSize', ! $wire.useCustomPlotSize)" class="text-xs text-emerald-600 hover:underline">
                                                {{ $useCustomPlotSize ? 'Select Standard Size' : 'Enter Custom Size' }}
                                            </button>
                                        </div>
                                        
                                        <div x-show="!$wire.useCustomPlotSize">
                                            <select wire:model="plot_size_id" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/50 py-3.5 px-4 outline-none appearance-none">
                                                <option value="">Select Plot Size</option>
                                                @foreach($this->plotSizes as $size)
                                                    <option value="{{ $size->id }}">{{ $size->display_text }}</option>
                                                @endforeach
                                            </select>
                                            @error('plot_size_id') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <div x-show="$wire.useCustomPlotSize" style="display: none;">
                                            <div class="flex space-x-3">
                                                <div class="flex-1">
                                                    <input type="number" wire:model="custom_plot_size" placeholder="Size" class="w-full rounded-xl border-gray-300 bg-white focus:border-emerald-500 focus:ring-1 py-3.5 px-4 outline-none">
                                                    @error('custom_plot_size') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                                                </div>
                                                <div class="w-1/3">
                                                    <select wire:model="custom_plot_unit" class="w-full rounded-xl border-gray-300 bg-white focus:border-emerald-500 py-3.5 px-4 outline-none appearance-none">
                                                        <option value="sqm">sqm</option>
                                                        <option value="sqft">sqft</option>
                                                        <option value="acres">acres</option>
                                                        <option value="hectares">hectares</option>
                                                        <option value="plots">plots</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                                        <textarea wire:model="description" rows="5" placeholder="Highlight unique features..." 
                                               class="w-full rounded-xl border-gray-300 bg-white text-gray-900 focus:border-emerald-500 focus:ring-1 py-3.5 px-4 outline-none resize-none"></textarea>
                                    </div>
                                </div>

                                <div class="flex space-x-3 pt-6 mt-auto shrink-0 pb-10 md:pb-0">
                                    <button @click="prev()" type="button" class="px-6 py-3.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors"> Back </button>
                                    <button @click="next()" type="button" class="px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium shadow-lg shadow-emerald-500/30 transition-all active:scale-95 flex-1"> Continue </button>
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
                                 class="absolute inset-0 flex flex-col space-y-6 px-6 py-6 pb-24 overflow-y-auto bg-white w-full h-full"
                            >
                                <h2 class="text-xl font-bold text-gray-900">Location</h2>

                                <div class="space-y-5">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                            <select wire:model.live="state_id" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 focus:border-emerald-500 py-3.5 px-4 outline-none appearance-none">
                                                <option value="">Select State</option>
                                                @foreach($this->states as $state)
                                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('state_id') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                            <select wire:model.live="city_id" class="w-full rounded-xl border-gray-300 bg-white text-gray-900 focus:border-emerald-500 py-3.5 px-4 outline-none appearance-none" @if(!$state_id) disabled @endif>
                                                <option value="">Select City</option>
                                                @foreach($this->cities as $city)
                                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('city_id') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Area</label>
                                        <div class="relative" x-data="{ open: false }">
                                            <input type="text" 
                                                   wire:model.live="areaSearch" 
                                                   placeholder="Search Area..." 
                                                   @focus="open = true" 
                                                   @click.outside="open = false"
                                                   class="w-full rounded-xl border-gray-300 bg-white text-gray-900 focus:border-emerald-500 py-3.5 px-4 outline-none"
                                            >
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400"/>
                                            </div>
                                            
                                            <div x-show="open" 
                                                 class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto"
                                                 style="display: none;">
                                                @if($this->getFilteredAreasProperty()->count() > 0)
                                                    <ul class="py-1">
                                                        @foreach($this->getFilteredAreasProperty() as $area)
                                                            <li @click="$wire.selectArea({{ $area->id }}); open = false" 
                                                                class="px-4 py-3 hover:bg-gray-50 cursor-pointer flex items-center justify-between group">
                                                                <span class="text-gray-900 group-hover:text-emerald-600">{{ $area->name }}</span>
                                                                @if($area_id === $area->id)
                                                                    <x-heroicon-s-check class="w-5 h-5 text-emerald-600"/>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <div class="px-4 py-3 text-sm text-gray-500">No areas found.</div>
                                                @endif
                                            </div>
                                        </div>
                                        @error('area_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                     <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                        <textarea wire:model="address" rows="3" placeholder="Street number, building name..." 
                                               class="w-full rounded-xl border-gray-300 bg-white text-gray-900 focus:border-emerald-500 focus:ring-1 py-3.5 px-4 outline-none resize-none"></textarea>
                                        @error('address') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="flex space-x-3 pt-6 mt-auto shrink-0 pb-10 md:pb-0">
                                    <button @click="prev()" type="button" class="px-6 py-3.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors"> Back </button>
                                    <button wire:click="create" wire:loading.attr="disabled" type="button" class="flex-1 py-3.5 bg-[#F59E0B] hover:bg-[#D97706] text-white rounded-xl font-semibold shadow-lg shadow-amber-500/30 transition-all active:scale-95 flex items-center justify-center space-x-2"> 
                                        <span wire:loading.remove>Submit for Review</span>
                                        <span wire:loading>Submitting...</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Step 5: Success --}}
                            <div x-show="step === 5" style="display: none;"
                                 x-transition:enter="transition ease-out duration-500 transform"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute inset-0 flex flex-col items-center justify-center space-y-8 text-center px-6 bg-white w-full h-full"
                            >
                                <div class="w-24 h-24 bg-amber-100 rounded-full flex items-center justify-center mb-4 shadow-inner shadow-amber-200/50 animate-pulse">
                                    <x-heroicon-o-clock class="w-14 h-14 text-amber-500" />
                                </div>

                                <div class="space-y-3">
                                    <h2 class="text-3xl font-bold text-gray-900">Listing Submitted!</h2>
                                    <p class="text-gray-500 text-sm max-w-sm mx-auto">Your property is under review by our team and will be live shortly.</p>
                                </div>

                                <div class="w-full max-w-sm space-y-3 pt-6">
                                    <button wire:click="viewMyListings" type="button" 
                                        class="block w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
                                        View My Listings
                                    </button>

                                    <button wire:click="createAnother" type="button" class="w-full py-3.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors">
                                        List Another
                                    </button>
                                </div>
                            </div>

                            </div>{{-- end absolute container --}}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
    
    <script>
        // Inline Image Processor 
        window.validateAndProcessImage = async function(file) {
            const MIN_WIDTH = 1024;
            const MIN_HEIGHT = 768;
            const MAX_WIDTH = 2560;
            const QUALITY = 0.8;

            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        const width = img.naturalWidth;
                        const height = img.naturalHeight;

                        if (width < MIN_WIDTH || height < MIN_HEIGHT) {
                            reject(`This photo is too small. Please select a clearer photo at least ${MIN_WIDTH}x${MIN_HEIGHT}px.`);
                            return;
                        }

                        const TARGET_RATIO = 4 / 3;
                        const sourceRatio = width / height;

                        let srcX = 0, srcY = 0, srcW = width, srcH = height;

                        if (sourceRatio > TARGET_RATIO) {
                            srcW = height * TARGET_RATIO;
                            srcX = (width - srcW) / 2;
                        } else {
                            srcH = width / TARGET_RATIO;
                            srcY = (height - srcH) / 2;
                        }

                        let targetWidth = srcW;
                        let targetHeight = srcH;

                        if (targetWidth > MAX_WIDTH) {
                            targetWidth = MAX_WIDTH;
                            targetHeight = MAX_WIDTH / TARGET_RATIO;
                        }

                        targetWidth = Math.round(targetWidth);
                        targetHeight = Math.round(targetHeight);

                        const canvas = document.createElement('canvas');
                        canvas.width = targetWidth;
                        canvas.height = targetHeight;
                        const ctx = canvas.getContext('2d');

                        ctx.imageSmoothingEnabled = true;
                        ctx.imageSmoothingQuality = 'high';
                        ctx.drawImage(img, srcX, srcY, srcW, srcH, 0, 0, targetWidth, targetHeight);

                        canvas.toBlob((blob) => {
                            if (!blob) {
                                reject("We couldn't process this image. Please try another one.");
                                return;
                            }
                            const safeName = `listing-upload-${Date.now()}.jpg`;
                            const newFile = new File([blob], safeName, { type: 'image/jpeg' });
                            resolve(newFile);
                        }, 'image/jpeg', QUALITY);
                    };
                    img.onerror = () => reject("This file doesn't look like a valid image.");
                    img.src = e.target.result;
                };
                reader.onerror = () => reject("We couldn't read this file.");
                reader.readAsDataURL(file);
            });
        };
    </script>
</div>
