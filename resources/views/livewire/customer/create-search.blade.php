<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 py-6 sm:py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex items-center space-x-3 mb-2">
                <a href="{{ route('customer.searches.index') }}"
                   class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <x-heroicon-o-arrow-left class="w-5 h-5" />
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Create New Search</h1>
            </div>
            <p class="text-sm text-gray-600">Set up a new saved search to get personalized property recommendations</p>
        </div>

        <!-- Form -->
        <form wire:submit="createSearch" class="space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>

                <div class="grid grid-cols-1 gap-4">
                    <x-forms.input
                        wire:model="name"
                        label="Search Name"
                        placeholder="e.g., 3BR House in Lagos"
                        required
                        :error="$errors->first('name')"
                    />

                    <x-forms.textarea
                        wire:model="description"
                        label="Description (Optional)"
                        placeholder="Describe what you're looking for..."
                        rows="3"
                        :error="$errors->first('description')"
                    />
                </div>
            </div>

            <!-- Interest -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">What are you interested in? *</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach(['buying' => 'Buying', 'renting' => 'Renting', 'shortlet' => 'Short Let'] as $value => $label)
                        <label class="relative flex items-center">
                            <input type="radio"
                                   wire:model.live="interested_in"
                                   value="{{ $value }}"
                                   class="sr-only">
                            <div class="flex items-center justify-center w-full p-3 text-sm font-medium rounded-lg border-2 cursor-pointer transition-all duration-200 {{ $interested_in === $value ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700 hover:border-blue-300' }}">
                                {{ $label }}
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('interested_in') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Property Types -->
            @if(count($availablePropertyTypes) > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Property Type *</h3>
                    <p class="text-sm text-gray-600 mb-4">Select the type of property you're interested in.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($availablePropertyTypes as $typeId => $typeName)
                            <label class="relative flex items-center">
                                <input type="radio"
                                       wire:model.live="selected_property_type"
                                       value="{{ $typeId }}"
                                       class="sr-only">
                                <div class="flex items-center justify-center w-full p-3 text-sm font-medium rounded-lg border-2 cursor-pointer transition-all duration-200 {{ $selected_property_type == $typeId ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700 hover:border-blue-300' }}">
                                    {{ $typeName }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('selected_property_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            @endif

            <!-- Property Subtypes -->
            @if(count($availableSubtypes) > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Specific Property Types</h3>
                    <p class="text-sm text-gray-600 mb-4">Select specific types of properties you're interested in (optional).</p>

                    @foreach($availableSubtypes as $propertyTypeName => $subtypes)
                        <div class="mb-6 last:mb-0">
                            <h4 class="text-base font-medium text-gray-800 mb-3">{{ $propertyTypeName }}</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                @foreach($subtypes as $subtype)
                                    <x-forms.checkbox
                                        wire:model.live="selected_subtypes"
                                        value="{{ $subtype->id }}"
                                        label="{{ $subtype->name }}"
                                        size="sm"
                                    />
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Location Preferences -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Location Preferences</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <x-forms.select
                        wire:model.live="state_id"
                        label="State"
                        placeholder="Any State"
                        :options="$states"
                        :selected="$state_id"
                    />

                    <x-forms.select
                        wire:model.live="city_id"
                        label="City"
                        placeholder="Any City"
                        :options="$cities"
                        :selected="$city_id"
                        :disabled="!$state_id"
                    />
                </div>

                <!-- Area Selection -->
                <div class="space-y-4">
                    <label class="text-sm font-medium text-gray-700">Area Preferences</label>

                    <!-- Area Selection Type -->
                    <div class="space-y-3">
                        <label class="relative flex items-center">
                            <input type="radio"
                                   wire:model.live="area_selection_type"
                                   value="any"
                                   class="sr-only">
                            <div class="flex items-center justify-center w-full p-3 text-sm font-medium rounded-lg border-2 cursor-pointer transition-all duration-200 {{ $area_selection_type == 'any' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700 hover:border-blue-300' }}">
                                Any Area
                                <span class="ml-2 text-xs text-gray-500">(Open to any area in the city)</span>
                            </div>
                        </label>

                        <label class="relative flex items-center">
                            <input type="radio"
                                   wire:model.live="area_selection_type"
                                   value="all"
                                   class="sr-only">
                            <div class="flex items-center justify-center w-full p-3 text-sm font-medium rounded-lg border-2 cursor-pointer transition-all duration-200 {{ $area_selection_type == 'all' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700 hover:border-blue-300' }}">
                                All Areas
                                <span class="ml-2 text-xs text-gray-500">(Interested in all areas within the city)</span>
                            </div>
                        </label>

                        <label class="relative flex items-center">
                            <input type="radio"
                                   wire:model.live="area_selection_type"
                                   value="specific"
                                   class="sr-only">
                            <div class="flex items-center justify-center w-full p-3 text-sm font-medium rounded-lg border-2 cursor-pointer transition-all duration-200 {{ $area_selection_type == 'specific' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700 hover:border-blue-300' }}">
                                Specific Areas
                                <span class="ml-2 text-xs text-gray-500">(Choose specific areas you're interested in)</span>
                            </div>
                        </label>
                    </div>

                    <!-- Specific Areas Selection -->
                    @if($area_selection_type === 'specific' && $city_id && count($availableAreas) > 0)
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Select Areas:</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($availableAreas as $areaId => $areaName)
                                    <x-forms.checkbox
                                        wire:model.live="selected_areas"
                                        value="{{ $areaId }}"
                                        label="{{ $areaName }}"
                                        size="sm"
                                    />
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($area_selection_type === 'specific' && $city_id && count($availableAreas) === 0)
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800">No areas available for the selected city.</p>
                        </div>
                    @endif

                    @if($area_selection_type === 'specific' && !$city_id)
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">Please select a city first to see available areas.</p>
                        </div>
                    @endif

                    <!-- Error handling for new fields -->
                    @error('area_selection_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    @error('selected_areas') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Legacy single area dropdown (hidden, kept as fallback) -->
                <div wire:ignore class="hidden">
                    <x-forms.select
                        wire:model.live="area_id"
                        label="Area"
                        placeholder="Any Area"
                        :options="$areas"
                        :selected="$area_id"
                        :disabled="!$city_id"
                    />
                </div>
            </div>

             <!-- Land Size Preferences (Separate Section) -->
            @if($selected_property_type == 3 && $interested_in === 'buying')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Land Size Preferences</h3>

                    <!-- Predefined Plot Sizes -->
                    @if(count($availablePlotSizes) > 0)
                        <div class="mb-4">
                            <h6 class="text-sm font-medium text-gray-600 mb-3">Select a Standard Plot Size</h6>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach($availablePlotSizes as $plotSizeId => $plotSizeLabel)
                                    <label class="relative flex items-center cursor-pointer">
                                        <input type="radio"
                                               wire:model.live="land_sizes.land_buy.predefined_size_id"
                                               value="{{ $plotSizeId }}"
                                               class="sr-only">
                                        <div class="flex items-center justify-center w-full p-3 text-xs font-medium rounded-lg border-2 cursor-pointer transition-all duration-200 {{ ($land_sizes['land_buy']['predefined_size_id'] ?? '') == $plotSizeId ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-700 hover:border-blue-300' }}">
                                            {{ $plotSizeLabel }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Custom Size Toggle -->
                    <div class="mb-4">
                        <x-forms.checkbox
                            wire:model.live="land_sizes.land_buy.use_custom_size"
                            label="Specify Custom Size"
                            description="Enter a specific size not listed above"
                        />
                    </div>

                    <!-- Custom Size Inputs -->
                    @if($land_sizes['land_buy']['use_custom_size'] ?? false)
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <h6 class="text-sm font-medium text-gray-600 mb-3">Custom Size</h6>
                            <div class="grid grid-cols-2 gap-4">
                                <x-forms.input
                                    wire:model="land_sizes.land_buy.custom_size_value"
                                    label="Size Value"
                                    placeholder="e.g., 1200"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                />

                                <x-forms.select
                                    wire:model="land_sizes.land_buy.custom_size_unit"
                                    label="Unit"
                                    :options="$plotSizeUnits"
                                    :selected="$land_sizes['land_buy']['custom_size_unit'] ?? 'sqm'"
                                />
                            </div>
                        </div>
                    @endif

                </div>
            @endif

            <!-- Budget Preferences -->
            @if($selected_property_type)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Budget Preferences</h3>
                    <p class="text-sm text-gray-600 mb-4">Set your budget range for the selected property type.</p>

                    @php
                        $typeId = $selected_property_type;
                        $propertyType = collect($availablePropertyTypes)->get($typeId);
                        $budgetKey = $typeId;

                        // Create budget categories based on property type and interest
                        if ($typeId == 1 || $typeId == 2) { // Apartment or House
                            $budgetKey = $interested_in === 'buying' ? 'house_buy' : 'house_rent';
                            $categoryLabel = $propertyType . ' (' . ucfirst($interested_in) . ')';
                        } elseif ($typeId == 3) { // Land
                            $budgetKey = 'land_buy';
                            $categoryLabel = $propertyType . ' (Buy)';
                        } elseif (in_array($typeId, [4, 5, 6])) { // Commercial, Office, Warehouse
                            $budgetKey = $interested_in === 'buying' ? 'shop_buy' : 'shop_rent';
                            $categoryLabel = $propertyType . ' (' . ucfirst($interested_in) . ')';
                        } else {
                            $categoryLabel = $propertyType;
                        }
                    @endphp

                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">{{ $categoryLabel }}</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <x-forms.currency
                                wire:model="budgets.{{ $budgetKey }}.min"
                                label="Minimum Budget"
                                placeholder="500,000"
                            />

                            <x-forms.currency
                                wire:model="budgets.{{ $budgetKey }}.max"
                                label="Maximum Budget"
                                placeholder="1,000,000"
                            />
                        </div>
                    </div>
                </div>
            @endif

           

            <!-- Notification Settings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Notification Settings</h3>

                <div class="space-y-3">
                    <x-forms.checkbox
                        wire:model="notification_settings.email_alerts"
                        label="Email Alerts"
                        description="Receive notifications via email"
                    />

                    <x-forms.checkbox
                        wire:model="notification_settings.sms_alerts"
                        label="SMS Alerts"
                        description="Receive notifications via SMS"
                    />

                    <x-forms.checkbox
                        wire:model="notification_settings.whatsapp_alerts"
                        label="WhatsApp Alerts"
                        description="Receive notifications via WhatsApp"
                    />
                </div>
            </div>

            <!-- Search Settings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Search Settings</h3>

                <div class="space-y-3">
                    <x-forms.checkbox
                        wire:model="is_active"
                        label="Active Search"
                        description="Enable notifications for this search"
                    />

                    <x-forms.checkbox
                        wire:model="is_default"
                        label="Set as Default"
                        description="Use this as your primary search criteria"
                    />
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                <a href="{{ route('customer.searches.index') }}"
                   class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                    Create Search
                </button>
            </div>
        </form>
    </div>
</div>