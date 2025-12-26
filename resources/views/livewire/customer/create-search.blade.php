<div class="min-h-screen bg-gray-50 font-sans text-gray-900 py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-white border-b border-gray-100/50 sticky top-0 z-30 backdrop-blur-xl bg-white/80 transition-all duration-300 mb-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                     <!-- Breadcrumb -->
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2 font-medium">
                        <a href="{{ route('dashboard') }}" class="hover:text-emerald-600 transition-colors">Home</a>
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <a href="{{ route('customer.searches.index') }}" class="hover:text-emerald-600 transition-colors">SmartSearch</a>
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="text-gray-900">Create New</span>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Create New SmartSearch</h1>
                    <p class="text-gray-500 mt-1">Set up alerts for your perfect property.</p>
                </div>
                
                <a href="{{ route('customer.searches.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 px-4 py-2.5 rounded-xl transition-all">
                    <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                    Back to List
                </a>
            </div>
        </div>
    </div>

        <form wire:submit="createSearch" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Primary Criteria -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Basic Info Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                            <x-heroicon-o-tag class="w-5 h-5" />
                        </div>
                        Search Details
                    </h3>
                    <div class="grid gap-6">
                        <div class="grid grid-cols-1 gap-6">
                            <x-forms.input 
                                wire:model="name" 
                                label="Search Name" 
                                placeholder="e.g. 3BR Apartment in Lekki" 
                                class="bg-gray-50 border-gray-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" 
                            />
                            <p class="text-xs text-gray-400">Leave blank to auto-generate a name from your criteria.</p>
                        </div>
                        <x-forms.textarea 
                            wire:model="description" 
                            label="Notes (Optional)" 
                            placeholder="Any specific requirements..." 
                            rows="2" 
                            class="bg-gray-50 border-gray-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" 
                        />
                    </div>
                </div>

                <!-- Interest & Type Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                            <x-heroicon-o-home class="w-5 h-5" />
                        </div>
                        Property Criteria
                    </h3>
                    
                    <!-- Interest -->
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-700 mb-3">I want to</label>
                        <div class="grid grid-cols-3 gap-4">
                            @foreach(['buying' => 'Buy', 'renting' => 'Rent', 'shortlet' => 'Short Let'] as $value => $label)
                                <label class="cursor-pointer group">
                                    <input type="radio" wire:model.live="interested_in" value="{{ $value }}" class="peer sr-only">
                                    <div class="text-center py-3 px-4 rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-600 font-bold transition-all duration-200 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 peer-checked:shadow-md group-hover:border-emerald-200 group-hover:bg-white">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Property Types -->
                    @if(count($availablePropertyTypes) > 0)
                        <div class="mb-8">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Property Type</label>
                            <div class="flex flex-wrap gap-3">
                                @foreach($availablePropertyTypes as $typeId => $typeName)
                                    <label class="cursor-pointer group">
                                        <input type="radio" wire:model.live="selected_property_type" value="{{ $typeId }}" class="peer sr-only">
                                        <div class="px-5 py-2.5 rounded-xl border-2 border-gray-100 bg-white text-sm font-semibold text-gray-600 transition-all duration-200 peer-checked:border-emerald-500 peer-checked:text-emerald-700 peer-checked:bg-emerald-50 group-hover:border-emerald-200">
                                            {{ $typeName }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Subtypes -->
                    @if(count($availableSubtypes) > 0)
                        <div class="pt-6 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-4">Specific Types</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @foreach($availableSubtypes as $propertyTypeName => $subtypes)
                                    @foreach($subtypes as $subtype)
                                        <label class="flex items-center gap-3 cursor-pointer group p-3 rounded-xl border border-transparent hover:bg-gray-50 hover:border-gray-200 transition-all">
                                            <div class="relative flex items-center">
                                                <input type="checkbox" wire:model.live="selected_subtypes" value="{{ $subtype->id }}" 
                                                       class="w-5 h-5 rounded-md border-gray-300 text-emerald-600 focus:ring-emerald-500 transition-colors">
                                            </div>
                                            <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900">{{ $subtype->name }}</span>
                                        </label>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @php
                        $selectedTypeName = $selected_property_type ? ($availablePropertyTypes[$selected_property_type] ?? null) : null;
                        $isResidential = $selectedTypeName && (str_contains(strtolower($selectedTypeName), 'house') || str_contains(strtolower($selectedTypeName), 'apartment'));
                    @endphp
                    @if($isResidential)
                        <div class="pt-6 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-4">Bedrooms & Amenities</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                                <x-forms.select
                                    wire:model.live="bedrooms_min"
                                    label="Minimum Bedrooms"
                                    placeholder="Any"
                                    :options="collect(range(0, 10))->mapWithKeys(fn($value) => [$value => $value])->toArray()"
                                    class="bg-gray-50 border-gray-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl"
                                />
                                <x-forms.select
                                    wire:model.live="bedrooms_max"
                                    label="Maximum Bedrooms"
                                    placeholder="Any"
                                    :options="collect(range(0, 10))->mapWithKeys(fn($value) => [$value => $value])->toArray()"
                                    class="bg-gray-50 border-gray-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl"
                                />
                            </div>

                            @if($availableFeatures->count() > 0)
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-3">Amenities & Features</label>
                                    <div class="space-y-4">
                                        @foreach($availableFeatures as $category => $features)
                                            <div>
                                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">{{ $category }}</div>
                                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                                    @foreach($features as $feature)
                                                        <label class="inline-flex items-center gap-2.5 p-2.5 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:border-emerald-400 hover:shadow-sm transition-all">
                                                            <input type="checkbox" wire:model.live="selected_features" value="{{ $feature->id }}" class="rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                                            <span class="text-xs font-semibold text-gray-600 truncate">{{ $feature->name }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Location Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center text-purple-600">
                            <x-heroicon-o-map-pin class="w-5 h-5" />
                        </div>
                        Location
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <x-forms.select 
                            wire:model.live="state_id" 
                            label="State" 
                            placeholder="Select State" 
                            :options="$states" 
                            class="bg-gray-50 border-gray-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" 
                        />
                        <x-forms.select 
                            wire:model.live="city_id" 
                            label="City" 
                            placeholder="Select City" 
                            :options="$cities" 
                            :disabled="!$state_id" 
                            class="bg-gray-50 border-gray-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl" 
                        />
                    </div>

                    @if($city_id)
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-4">Preferred Areas</label>
                            <div class="space-y-4">
                                <div class="flex flex-wrap gap-4">
                                    @foreach(['any' => 'Any Area', 'specific' => 'Specific Areas'] as $value => $label)
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <div class="relative flex items-center">
                                                <input type="radio" wire:model.live="area_selection_type" value="{{ $value }}" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                            </div>
                                            <span class="text-sm font-medium text-gray-700 group-hover:text-emerald-700 transition-colors">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                @if($area_selection_type === 'specific' && count($availableAreas) > 0)
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-gray-200">
                                        @foreach($availableAreas as $areaId => $areaName)
                                            <label class="inline-flex items-center gap-2.5 p-2.5 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-emerald-400 hover:shadow-sm transition-all">
                                                <input type="checkbox" wire:model.live="selected_areas" value="{{ $areaId }}" class="rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                                <span class="text-xs font-semibold text-gray-600 truncate">{{ $areaName }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Land Size Preferences (Conditional) -->
                @if($selected_property_type == 3)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                                <x-heroicon-o-arrows-pointing-out class="w-5 h-5" />
                            </div>
                            Land Size
                        </h3>
                        
                        @if(count($availablePlotSizes) > 0)
                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 mb-3">Standard Plot Size</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                    @foreach($availablePlotSizes as $plotSizeId => $plotSizeLabel)
                                        <label class="cursor-pointer group">
                                            <input type="radio" wire:model.live="land_sizes.land_buy.predefined_size_id" value="{{ $plotSizeId }}" class="peer sr-only">
                                            <div class="text-center p-3 rounded-xl border-2 border-gray-100 bg-gray-50 text-sm font-medium text-gray-600 transition-all peer-checked:border-emerald-500 peer-checked:text-emerald-700 peer-checked:bg-emerald-50 group-hover:border-emerald-200">
                                                {{ $plotSizeLabel }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="space-y-4">
                            <x-forms.checkbox
                                wire:model.live="land_sizes.land_buy.use_custom_size"
                                label="Specify Custom Size"
                                description="Enter a specific size not listed above"
                            />

                            @if($land_sizes['land_buy']['use_custom_size'] ?? false)
                                <div class="p-6 bg-gray-50 rounded-xl border border-gray-100 grid grid-cols-2 gap-6">
                                    <x-forms.input
                                        wire:model="land_sizes.land_buy.custom_size_value"
                                        label="Size Value"
                                        placeholder="e.g., 1200"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="bg-white border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl"
                                    />
                                    <x-forms.select
                                        wire:model="land_sizes.land_buy.custom_size_unit"
                                        label="Unit"
                                        :options="$plotSizeUnits"
                                        :selected="$land_sizes['land_buy']['custom_size_unit'] ?? 'sqm'"
                                        class="bg-white border-gray-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl"
                                    />
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Budget & Settings -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Budget Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Budget Range</h3>
                    
                    @if($selected_property_type)
                        @php
                            $typeId = $selected_property_type;
                            $propertyType = collect($availablePropertyTypes)->get($typeId);
                            $budgetKey = $typeId;
                            if ($typeId == 1 || $typeId == 2) {
                                $budgetKey = $interested_in === 'buying' ? 'house_buy' : 'house_rent';
                            } elseif ($typeId == 3) {
                                $budgetKey = 'land_buy';
                            } elseif (in_array($typeId, [4, 5, 6])) {
                                $budgetKey = $interested_in === 'buying' ? 'shop_buy' : 'shop_rent';
                            }
                        @endphp

                        <div class="space-y-5">
                            <x-forms.currency 
                                wire:model="budgets.{{ $budgetKey }}.min" 
                                label="Minimum Price" 
                                placeholder="0" 
                                class="bg-gray-50 border-gray-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl"
                            />
                            <x-forms.currency 
                                wire:model="budgets.{{ $budgetKey }}.max" 
                                label="Maximum Price" 
                                placeholder="Any" 
                                class="bg-gray-50 border-gray-200 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl"
                            />
                        </div>
                    @else
                        <div class="text-center py-8 px-4 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <x-heroicon-o-currency-dollar class="w-8 h-8 text-gray-400 mx-auto mb-2" />
                            <p class="text-sm text-gray-500">Select a property type to set your budget preference.</p>
                        </div>
                    @endif
                </div>

                <!-- Notifications Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Notifications</h3>
                    @php
                        $emailAllowed = in_array('email', $allowedNotificationChannels ?? []);
                        $smsAllowed = in_array('sms', $allowedNotificationChannels ?? []);
                        $whatsappAllowed = in_array('whatsapp', $allowedNotificationChannels ?? []);
                    @endphp
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100 {{ $emailAllowed ? '' : 'opacity-60' }}">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg text-gray-500">
                                    <x-heroicon-o-envelope class="w-5 h-5" />
                                </div>
                                <span class="text-sm font-semibold text-gray-700">Email Alerts</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="notification_settings.email_alerts" class="sr-only peer" @disabled(!$emailAllowed)>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100 {{ $smsAllowed ? '' : 'opacity-60' }}">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg text-gray-500">
                                    <x-heroicon-o-device-phone-mobile class="w-5 h-5" />
                                </div>
                                <span class="text-sm font-semibold text-gray-700">SMS Alerts</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="notification_settings.sms_alerts" class="sr-only peer" @disabled(!$smsAllowed)>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100 {{ $whatsappAllowed ? '' : 'opacity-60' }}">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg text-green-600">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-700">WhatsApp Alerts</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="notification_settings.whatsapp_alerts" class="sr-only peer" @disabled(!$whatsappAllowed)>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="pt-4">
                     <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-lg rounded-2xl shadow-lg shadow-emerald-200 hover:shadow-xl transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <x-heroicon-o-magnifying-glass class="w-6 h-6" />
                        Start Searching
                    </button>
                    <p class="text-xs text-center text-gray-400 mt-4">
                        By creating this search, you agree to receive notifications based on your preferences.
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>
