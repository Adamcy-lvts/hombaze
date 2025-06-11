<!-- Premium Property Search Component -->
<div class="property-search-component" x-data="{ showAdvanced: false }">
    <!-- Main Search Bar -->
    <form wire:submit.prevent="search">
        <div class="bg-white/10 backdrop-blur-2xl rounded-3xl border border-white/20 p-6 shadow-2xl">
            <!-- Quick Search Row -->
            <div class="flex flex-col lg:flex-row gap-4 items-end">
                <!-- Location Search -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Location</label>
                    <div class="relative">
                        <select wire:model.live="city_id" class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 appearance-none">
                            <option value="" class="text-gray-900">Choose City</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" class="text-gray-900">{{ $city->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Property Type -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Property Type</label>
                    <div class="relative">
                        <select wire:model.live="property_type_id" class="w-full bg-white/10 backdrop-blur-md border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 appearance-none">
                            <option value="" class="text-gray-900">Any Type</option>
                            @foreach($propertyTypes as $type)
                                <option value="{{ $type->id }}" class="text-gray-900">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Price Range -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Budget</label>
                    <div class="flex gap-2">
                        <input type="number" wire:model="min_price" placeholder="Min" class="w-1/2 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                        <input type="number" wire:model="max_price" placeholder="Max" class="w-1/2 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                    </div>
                </div>

                <!-- Advanced Filters Toggle & Search Button -->
                <div class="flex gap-3">
                    <!-- Advanced Filters Toggle -->
                    <button type="button" @click="showAdvanced = !showAdvanced" 
                        class="group bg-white/10 backdrop-blur-md border border-white/20 rounded-xl px-4 py-3 text-white hover:bg-white/20 transition-all duration-300 flex items-center gap-2"
                        :class="{ 'bg-blue-500/20 border-blue-400/50': showAdvanced }" aria-label="Show advanced filters">
                        <svg class="w-5 h-5 transition-transform duration-300" :class="{ 'rotate-180': showAdvanced }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17l6-6-6-6M21 17l-6-6 6-6" />
                        </svg>
                        <span class="hidden sm:inline">Filters</span>
                    </button>

                    <!-- Search Button -->
                    <button type="submit" class="group bg-gradient-to-r from-emerald-500 to-blue-500 hover:from-emerald-600 hover:to-blue-600 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="hidden sm:inline">Search</span>
                    </button>
                </div>
            </div>

            <!-- Advanced Filters Section -->
            <div x-show="showAdvanced" x-transition.opacity.duration.400ms class="mt-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Area Filter -->
                    <div class="group">
                        <label for="area" class="block text-sm font-semibold text-gray-300 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16l4-4 4 4V5z"></path>
                            </svg>
                            Area
                        </label>
                        <div class="relative">
                            <select wire:model="area_id" id="area" class="w-full bg-gray-700/50 backdrop-blur-md border border-gray-600/50 rounded-2xl px-6 py-4 text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-300 appearance-none">
                                <option value="" class="text-gray-900">Select Area</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" class="text-gray-900">{{ $area->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Property Subtype Filter -->
                    <div class="group">
                        <label for="property_subtype" class="block text-sm font-semibold text-gray-300 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                            Subtype
                        </label>
                        <div class="relative">
                            <select wire:model="property_subtype_id" id="property_subtype" class="w-full bg-gray-700/50 backdrop-blur-md border border-gray-600/50 rounded-2xl px-6 py-4 text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-300 appearance-none">
                                <option value="" class="text-gray-900">Any Subtype</option>
                                @foreach($propertySubtypes as $subtype)
                                    <option value="{{ $subtype->id }}" class="text-gray-900">{{ $subtype->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Listing Type Filter -->
                    <div class="group">
                        <label for="listing_type" class="block text-sm font-semibold text-gray-300 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Listing Type
                    </label>
                    <div class="relative">
                        <select wire:model="listing_type" id="listing_type" class="w-full bg-gray-700/50 backdrop-blur-md border border-gray-600/50 rounded-2xl px-6 py-4 text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-300 appearance-none">
                            <option value="" class="text-gray-900">Any Listing</option>
                            <option value="sale" class="text-gray-900">For Sale</option>
                            <option value="rent" class="text-gray-900">For Rent</option>
                            <option value="lease" class="text-gray-900">For Lease</option>
                            <option value="shortlet" class="text-gray-900">Shortlet</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Bedrooms -->
                    <div class="group">
                        <label for="bedrooms" class="block text-sm font-semibold text-gray-300 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21v-4a2 2 0 012-2h4a2 2 0 012 2v4"></path>
                            </svg>
                            Bedrooms
                        </label>
                        <div class="relative">
                            <select wire:model="bedrooms" id="bedrooms" class="w-full bg-gray-700/50 backdrop-blur-md border border-gray-600/50 rounded-2xl px-6 py-4 text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 appearance-none">
                                <option value="" class="text-gray-900">Any Bedrooms</option>
                                <option value="1" class="text-gray-900">1 Bedroom</option>
                                <option value="2" class="text-gray-900">2 Bedrooms</option>
                                <option value="3" class="text-gray-900">3 Bedrooms</option>
                                <option value="4" class="text-gray-900">4 Bedrooms</option>
                                <option value="5" class="text-gray-900">5+ Bedrooms</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keyword Search -->
            <div class="relative mt-8">
                <label for="keyword" class="block text-sm font-semibold text-gray-300 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search by keyword
                </label>
                <div class="relative">
                    <input wire:model="keyword" type="text" id="keyword" placeholder="e.g. apartment, duplex, shortlet, Gwarinpa, Victoria Island..." class="w-full bg-gray-700/50 backdrop-blur-md border border-gray-600/50 rounded-2xl px-6 py-4 text-gray-200 placeholder-gray-400 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-300 pr-16">
                    <div class="absolute inset-y-0 right-0 flex items-center px-4">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Premium Dark Search Button -->
            <div class="flex justify-center pt-4 hidden lg:block">
                <button type="submit" class="group relative overflow-hidden bg-gradient-to-r from-gray-700 to-gray-800 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-5 px-12 rounded-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-2xl flex items-center justify-center min-w-[200px] border border-gray-600/50">
                    <!-- Animated Background -->
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-600 to-gray-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <!-- Button Content -->
                    <div class="relative flex items-center">
                        <svg class="w-6 h-6 mr-3 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="text-lg tracking-wide">Search Properties</span>
                    </div>
                    <!-- Shine Effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                </button>
            </div>
        </div>
    </form>
</div>
