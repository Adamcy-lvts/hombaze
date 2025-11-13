<div>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="min-h-screen bg-linear-to-br from-gray-50 via-slate-50 to-gray-100 relative overflow-hidden py-4 sm:py-6 lg:py-8">
        <!-- Subtle Background Elements -->
        <div class="absolute inset-0 opacity-30">
            <div class="floating-element absolute top-1/4 right-1/4 w-32 h-32 bg-linear-to-br from-emerald-400/8 to-teal-500/6 rounded-full blur-3xl"></div>
            <div class="floating-element absolute bottom-1/3 left-1/4 w-40 h-40 bg-linear-to-br from-blue-400/6 to-indigo-500/4 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-30 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0 mb-6 sm:mb-8">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Search Preferences</h1>
                    <p class="text-sm sm:text-base text-gray-600 mt-1">Set your preferences to get better property recommendations</p>
                </div>
                <a href="{{ route('customer.settings') }}"
                   class="inline-flex items-center px-4 py-2 bg-white/95 backdrop-blur-xl border border-gray-300/60 rounded-xl text-gray-700 hover:bg-white transition-all duration-500 transform hover:scale-105 shadow-lg text-sm sm:text-base">
                    <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                    Back to Settings
                </a>
            </div>

            <!-- Progress Indicator -->
            <div x-data="progressIndicator"
            class="mb-8 bg-white/95 backdrop-blur-xl rounded-xl border border-gray-300/60 shadow-lg p-4 sm:p-6" x-cloak>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Setup Progress</h3>
                    <span class="text-sm text-gray-600" x-text="`Stage ${currentStage} of 4`"></span>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                    <div class="bg-linear-to-r from-emerald-500 to-teal-600 h-2 rounded-full transition-all duration-500"
                         :style="`width: ${progress}%`"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div class="flex items-center space-x-2" :class="currentStage >= 1 ? 'text-emerald-600' : 'text-gray-400'">
                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                             :class="currentStage >= 1 ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-gray-300'">
                            <span x-show="currentStage >= 1">✓</span>
                            <span x-show="currentStage < 1">1</span>
                        </div>
                        <span>Interests</span>
                    </div>
                    <div class="flex items-center space-x-2" :class="currentStage >= 2 ? 'text-emerald-600' : 'text-gray-400'">
                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                             :class="currentStage >= 2 ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-gray-300'">
                            <span x-show="currentStage >= 2">✓</span>
                            <span x-show="currentStage < 2">2</span>
                        </div>
                        <span>Categories</span>
                    </div>
                    <div class="flex items-center space-x-2" :class="currentStage >= 3 ? 'text-emerald-600' : 'text-gray-400'">
                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                             :class="currentStage >= 3 ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-gray-300'">
                            <span x-show="currentStage >= 3">✓</span>
                            <span x-show="currentStage < 3">3</span>
                        </div>
                        <span>Options</span>
                    </div>
                    <div class="flex items-center space-x-2" :class="currentStage >= 4 ? 'text-emerald-600' : 'text-gray-400'">
                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center"
                             :class="currentStage >= 4 ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-gray-300'">
                            <span x-show="currentStage >= 4">✓</span>
                            <span x-show="currentStage < 4">4</span>
                        </div>
                        <span>Budget</span>
                    </div>
                </div>
            </div>

            <!-- Preferences Form -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6">
                <div class="flex items-center mb-6">
                    <div class="p-3 rounded-xl bg-linear-to-br from-blue-500 to-indigo-600 shadow-lg mr-3">
                        <x-heroicon-o-cog-6-tooth class="w-6 h-6 text-white" />
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">Your Preferences</h2>
                </div>

                <form wire:submit="savePreferences" class="space-y-8">
                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="shrink-0">
                                    <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Stage 1: User Interests -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-linear-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                            <div class="flex items-center space-x-3">
                                <div class="shrink-0">
                                    <div class="w-8 h-8 flex items-center justify-center bg-blue-100 rounded-lg">
                                        <span class="text-blue-600 font-semibold">1</span>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">What are you interested in?</h3>
                                    <p class="text-sm text-gray-600">Choose your property search interests (you can select multiple)</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach(['buying' => ['title' => 'Buying', 'description' => 'Looking to purchase property'], 'renting' => ['title' => 'Renting', 'description' => 'Looking for long-term rental'], 'shortlet' => ['title' => 'Short Let', 'description' => 'Looking for short-term rental']] as $value => $option)
                                    <label class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-xs focus-within:ring-2 focus-within:ring-blue-500 transition-all duration-200 hover:border-blue-400 hover:shadow-md @if (in_array($value, $form->interested_in ?? [])) border-blue-500 bg-blue-50 @endif">
                                        <input type="checkbox" wire:model.live="form.interested_in" value="{{ $value }}" class="sr-only">
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="flex items-center">
                                                    <span class="block text-sm font-medium text-gray-900">{{ $option['title'] }}</span>
                                                    @if (in_array($value, $form->interested_in ?? []))
                                                        <svg class="ml-2 h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </span>
                                                <span class="mt-1 flex items-center text-sm text-gray-500">{{ $option['description'] }}</span>
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Stage 2: Property Categories (shown when interests are selected) -->
                    <div x-data="categorySelection" x-cloak>
                        <div x-show="showPropertyCategorySelection" x-transition class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 bg-linear-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <div class="shrink-0">
                                        <div class="w-8 h-8 flex items-center justify-center bg-indigo-100 rounded-lg">
                                            <span class="text-indigo-600 font-semibold">2</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Property Categories</h3>
                                        <p class="text-sm text-gray-600">What type of properties do you want to focus on?</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($availablePropertyCategories as $category)
                                        <label class="relative flex cursor-pointer rounded-lg border border-gray-300 bg-white p-4 shadow-xs focus-within:ring-2 focus-within:ring-indigo-500 transition-all duration-200 hover:border-indigo-400 hover:shadow-md"
                                               :class="(Array.isArray(propertyCategories) ? propertyCategories : []).includes('{{ $category['value'] }}') ? 'border-indigo-500 bg-indigo-50' : ''">
                                            <input type="checkbox" value="{{ $category['value'] }}" wire:model.live="form.property_categories" class="sr-only">
                                            <span class="flex flex-1">
                                                <span class="flex flex-col">
                                                    <span class="flex items-center">
                                                        <span class="block text-sm font-medium text-gray-900">{{ $category['label'] }}</span>
                                                        <svg x-show="(Array.isArray(propertyCategories) ? propertyCategories : []).includes('{{ $category['value'] }}')" class="ml-2 h-4 w-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                    <span class="mt-1 flex items-center text-sm text-gray-500">{{ $category['description'] }}</span>
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- No Interest Selected Message -->
                        <div x-show="!(Array.isArray(interests) ? interests : []).length" class="text-center py-8 text-gray-500 bg-white rounded-xl border border-gray-200">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm">Please select your interests above to see relevant property options</p>
                        </div>
                    </div>

                    <!-- Stage 3: Specific Options (shown when categories are selected) -->
                    <div x-data="specificOptions" x-cloak>
                        <div x-show="showSpecificOptions" x-transition class="space-y-6">

                            <!-- House/Apartment Options -->
                            <div x-show="showHouseOptions" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-linear-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="shrink-0">
                                            <div class="w-8 h-8 flex items-center justify-center bg-green-100 rounded-lg">
                                                <span class="text-green-600 font-semibold">3</span>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Houses & Apartments</h3>
                                            <p class="text-sm text-gray-600">Select residential property types you're interested in</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-6 space-y-6">
                                    <!-- Apartment Subtypes -->
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700 mb-3">Apartments by Bedrooms</h4>
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                            @foreach ($apartmentSubtypes as $subtype)
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" value="{{ $subtype['id'] }}" wire:model.live="form.apartment_subtypes" class="rounded-sm border-gray-300 text-blue-600 shadow-xs focus:ring-blue-500">
                                                    <span class="text-sm text-gray-700">{{ $subtype['name'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- House Subtypes -->
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700 mb-3">House Types</h4>
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                            @foreach ($houseSubtypes as $subtype)
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" value="{{ $subtype['id'] }}" wire:model.live="form.house_subtypes" class="rounded-sm border-gray-300 text-blue-600 shadow-xs focus:ring-blue-500">
                                                    <span class="text-sm text-gray-700">{{ $subtype['name'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Land Options -->
                            <div x-show="showLandOptions" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-linear-to-r from-yellow-50 to-amber-50 border-b border-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="shrink-0">
                                            <div class="w-8 h-8 flex items-center justify-center bg-yellow-100 rounded-lg">
                                                <span class="text-yellow-600 font-semibold">3</span>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Land & Plots</h3>
                                            <p class="text-sm text-gray-600">Select land sizes you're interested in purchasing</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                                        @foreach ($landSizes as $size)
                                            <label class="flex items-center space-x-2 cursor-pointer">
                                                <input type="checkbox" value="{{ $size['id'] }}" wire:model.live="form.land_sizes" class="rounded-sm border-gray-300 text-green-600 shadow-xs focus:ring-green-500">
                                                <span class="text-sm text-gray-700">{{ $size['name'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Shop Options -->
                            <div x-show="showShopOptions" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-linear-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="shrink-0">
                                            <div class="w-8 h-8 flex items-center justify-center bg-purple-100 rounded-lg">
                                                <span class="text-purple-600 font-semibold">3</span>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Commercial Shops</h3>
                                            <p class="text-sm text-gray-600">Get notified about shop and retail spaces</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input type="checkbox" wire:model.live="form.shop_selected" class="rounded-sm border-gray-300 text-purple-600 shadow-xs focus:ring-purple-500">
                                        <span class="text-sm text-gray-700">Yes, I'm interested in commercial shops and retail spaces</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stage 4: Budget Ranges (shown when options are selected) -->
                    <div x-data="budgetOptions" x-cloak>
                        <div x-show="showBudgets" x-transition class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 bg-linear-to-r from-rose-50 to-red-50 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <div class="shrink-0">
                                        <div class="w-8 h-8 flex items-center justify-center bg-rose-100 rounded-lg">
                                            <span class="text-rose-600 font-semibold">4</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Budget Ranges</h3>
                                        <p class="text-sm text-gray-600">Set separate budgets for your different property interests</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 space-y-6">
                                <!-- Dynamic budget sections will be here -->
                                <!-- This is a simplified version - you can expand with specific budget sections for each category -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="shrink-0">
                                            <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-700">Budget ranges will automatically appear based on your property interests. Leave fields blank for no budget restrictions.</p>
                                            <p class="mt-1 text-xs text-blue-600">💡 Tip: Separate budgets help us match you with the right properties for each category</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Preferences -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-visible">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center space-x-3">
                                <div class="shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Location Preferences</h3>
                                    <p class="text-sm text-gray-600">Choose your preferred location for property search</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 overflow-visible">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 overflow-visible">
                                <div class="overflow-visible">
                                    <x-forms.select
                                        label="State"
                                        placeholder="Select State"
                                        wire:model.live="form.preferred_location_state"
                                        :options="$states->pluck('name', 'id')->toArray()" />
                                    <p class="mt-1 text-xs text-gray-500">Choose the state you want to search in</p>
                                </div>
                                <div class="overflow-visible">
                                    <x-forms.select
                                        label="City"
                                        placeholder="Select City"
                                        wire:model.live="form.preferred_location_city"
                                        :disabled="empty($form->preferred_location_state)"
                                        :options="$cities->pluck('name', 'id')->toArray()" />
                                    <p class="mt-1 text-xs text-gray-500">Narrow down to a specific city</p>
                                </div>
                                <div class="overflow-visible">
                                    <x-forms.select
                                        label="Area"
                                        placeholder="Select Area"
                                        wire:model="form.preferred_location_area"
                                        :disabled="empty($form->preferred_location_city)"
                                        :options="$areas->pluck('name', 'id')->toArray()" />
                                    <p class="mt-1 text-xs text-gray-500">Choose specific area/neighborhood</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Preferences -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center space-x-3">
                                <div class="shrink-0">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Notification Preferences</h3>
                                    <p class="text-sm text-gray-600">How would you like to be notified about new properties?</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-forms.checkbox
                                        wire:model="form.email_alerts"
                                        label="Email Alerts"
                                        description="Receive email notifications for new matching properties" />
                                </div>
                                <div>
                                    <x-forms.checkbox
                                        wire:model="form.sms_alerts"
                                        label="SMS Alerts"
                                        description="Receive SMS notifications for urgent updates" />
                                </div>
                                <div>
                                    <x-forms.checkbox
                                        wire:model="form.whatsapp_alerts"
                                        label="WhatsApp Alerts"
                                        description="Receive WhatsApp notifications for property matches" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-end">
                        <a href="{{ route('customer.settings') }}"
                           class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Settings
                        </a>
                        <button type="submit"
                                class="inline-flex items-center justify-center px-8 py-3 bg-linear-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-700 hover:via-emerald-600 hover:to-teal-600 text-white font-semibold rounded-xl transition-all duration-500 transform hover:scale-105 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save Preferences
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@script
<script>
Alpine.data('progressIndicator', () => ({
    init() {
        this.interests = this.$wire.entangle('form.interested_in');
        this.propertyCategories = this.$wire.entangle('form.property_categories');
        this.checkWireData();
    },
    interests: [],
    propertyCategories: [],

    checkWireData() {
        if (this.$wire.form && this.$wire.form.interested_in) {
            this.interests = this.$wire.form.interested_in;
        }
        if (this.$wire.form && this.$wire.form.property_categories) {
            this.propertyCategories = this.$wire.form.property_categories;
        }
    },

    get currentStage() {
        let interests = Array.isArray(this.interests) ? this.interests : [];
        let categories = Array.isArray(this.propertyCategories) ? this.propertyCategories : [];

        // Fallback to wire data
        if (interests.length === 0 && this.$wire.form && this.$wire.form.interested_in) {
            interests = Array.isArray(this.$wire.form.interested_in) ? this.$wire.form.interested_in : [];
        }
        if (categories.length === 0 && this.$wire.form && this.$wire.form.property_categories) {
            categories = Array.isArray(this.$wire.form.property_categories) ? this.$wire.form.property_categories : [];
        }

        if (interests.length === 0) return 1;
        if (categories.length === 0) return 2;
        return 3;
    },

    get progress() {
        return (this.currentStage / 4) * 100;
    }
}));

Alpine.data('categorySelection', () => ({
    init() {
        this.interests = this.$wire.entangle('form.interested_in');
        this.propertyCategories = this.$wire.entangle('form.property_categories');

        // Also check wire data directly for initial state
        this.checkWireData();
    },
    interests: [],
    propertyCategories: [],

    checkWireData() {
        // Check if wire data has the values we need
        if (this.$wire.form && this.$wire.form.interested_in) {
            this.interests = this.$wire.form.interested_in;
        }
        if (this.$wire.form && this.$wire.form.property_categories) {
            this.propertyCategories = this.$wire.form.property_categories;
        }
    },

    get showPropertyCategorySelection() {
        // First try entangled data, then fallback to wire data
        let interests = Array.isArray(this.interests) ? this.interests : [];

        // Fallback to wire data if entangled data is empty
        if (interests.length === 0 && this.$wire.form && this.$wire.form.interested_in) {
            interests = Array.isArray(this.$wire.form.interested_in) ? this.$wire.form.interested_in : [];
        }

        return interests.length > 0;
    }
}));

Alpine.data('specificOptions', () => ({
    init() {
        this.propertyCategories = this.$wire.entangle('form.property_categories');
        this.checkWireData();
    },
    propertyCategories: [],

    checkWireData() {
        if (this.$wire.form && this.$wire.form.property_categories) {
            this.propertyCategories = this.$wire.form.property_categories;
        }
    },

    getCategories() {
        let categories = Array.isArray(this.propertyCategories) ? this.propertyCategories : [];

        // Fallback to wire data if entangled data is empty
        if (categories.length === 0 && this.$wire.form && this.$wire.form.property_categories) {
            categories = Array.isArray(this.$wire.form.property_categories) ? this.$wire.form.property_categories : [];
        }

        return categories;
    },

    get showSpecificOptions() {
        return this.getCategories().length > 0;
    },

    get showHouseOptions() {
        const categories = this.getCategories();
        return categories.includes('house_buy') || categories.includes('house_rent');
    },

    get showLandOptions() {
        const categories = this.getCategories();
        return categories.includes('land_buy');
    },

    get showShopOptions() {
        const categories = this.getCategories();
        return categories.includes('shop_buy') || categories.includes('shop_rent');
    }
}));

Alpine.data('budgetOptions', () => ({
    init() {
        this.propertyCategories = this.$wire.entangle('form.property_categories');
        this.checkWireData();
    },
    propertyCategories: [],

    checkWireData() {
        if (this.$wire.form && this.$wire.form.property_categories) {
            this.propertyCategories = this.$wire.form.property_categories;
        }
    },

    get showBudgets() {
        let categories = Array.isArray(this.propertyCategories) ? this.propertyCategories : [];

        // Fallback to wire data if entangled data is empty
        if (categories.length === 0 && this.$wire.form && this.$wire.form.property_categories) {
            categories = Array.isArray(this.$wire.form.property_categories) ? this.$wire.form.property_categories : [];
        }

        return categories.length > 0;
    }
}));
</script>
@endscript
</div>