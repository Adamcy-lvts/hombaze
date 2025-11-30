<div>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Search Preferences</h1>
                    <p class="text-gray-500 mt-2">Set your preferences to get better property recommendations</p>
                </div>
                <a href="{{ route('customer.settings') }}"
                   class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                    <x-heroicon-o-arrow-left class="w-4 h-4 mr-1" />
                    Back to Settings
                </a>
            </div>

            <!-- Progress Indicator -->
            <div x-data="progressIndicator"
                 class="mb-8 bg-white rounded-2xl border border-gray-100 shadow-sm p-6" x-cloak>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Setup Progress</h3>
                    <span class="text-sm font-medium text-emerald-600" x-text="`Stage ${currentStage} of 4`"></span>
                </div>

                <div class="w-full bg-gray-100 rounded-full h-2 mb-6">
                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500"
                         :style="`width: ${progress}%`"></div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="flex items-center gap-2" :class="currentStage >= 1 ? 'text-emerald-700 font-medium' : 'text-gray-400'">
                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center text-xs"
                             :class="currentStage >= 1 ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-gray-200'">
                            <span x-show="currentStage >= 1">✓</span>
                            <span x-show="currentStage < 1">1</span>
                        </div>
                        <span>Interests</span>
                    </div>
                    <div class="flex items-center gap-2" :class="currentStage >= 2 ? 'text-emerald-700 font-medium' : 'text-gray-400'">
                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center text-xs"
                             :class="currentStage >= 2 ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-gray-200'">
                            <span x-show="currentStage >= 2">✓</span>
                            <span x-show="currentStage < 2">2</span>
                        </div>
                        <span>Categories</span>
                    </div>
                    <div class="flex items-center gap-2" :class="currentStage >= 3 ? 'text-emerald-700 font-medium' : 'text-gray-400'">
                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center text-xs"
                             :class="currentStage >= 3 ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-gray-200'">
                            <span x-show="currentStage >= 3">✓</span>
                            <span x-show="currentStage < 3">3</span>
                        </div>
                        <span>Options</span>
                    </div>
                    <div class="flex items-center gap-2" :class="currentStage >= 4 ? 'text-emerald-700 font-medium' : 'text-gray-400'">
                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center text-xs"
                             :class="currentStage >= 4 ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-gray-200'">
                            <span x-show="currentStage >= 4">✓</span>
                            <span x-show="currentStage < 4">4</span>
                        </div>
                        <span>Budget</span>
                    </div>
                </div>
            </div>

            <!-- Preferences Form -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-8">
                    <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                        <x-heroicon-o-adjustments-horizontal class="w-6 h-6" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Your Preferences</h2>
                </div>

                <form wire:submit="savePreferences" class="space-y-8">
                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-100 rounded-xl p-4 flex items-start gap-3">
                            <x-heroicon-o-x-circle class="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
                            <div>
                                <h3 class="text-sm font-bold text-red-900">Please fix the following errors:</h3>
                                <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Stage 1: User Interests -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-900">1</div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">What are you interested in?</h3>
                                    <p class="text-xs text-gray-500">Choose your property search interests</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach(['buying' => ['title' => 'Buying', 'description' => 'Looking to purchase property'], 'renting' => ['title' => 'Renting', 'description' => 'Looking for long-term rental'], 'shortlet' => ['title' => 'Short Let', 'description' => 'Looking for short-term rental']] as $value => $option)
                                    <label class="relative flex cursor-pointer rounded-xl border-2 p-4 transition-all duration-200 hover:border-emerald-200 hover:bg-emerald-50/30 @if (in_array($value, $form->interested_in ?? [])) border-emerald-500 bg-emerald-50 @else border-gray-100 bg-white @endif">
                                        <input type="checkbox" wire:model.live="form.interested_in" value="{{ $value }}" class="sr-only">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="font-bold text-gray-900">{{ $option['title'] }}</span>
                                                @if (in_array($value, $form->interested_in ?? []))
                                                    <x-heroicon-s-check-circle class="w-5 h-5 text-emerald-600" />
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500">{{ $option['description'] }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Stage 2: Property Categories -->
                    <div x-data="categorySelection" x-cloak>
                        <div x-show="showPropertyCategorySelection" x-transition class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-900">2</div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">Property Categories</h3>
                                        <p class="text-xs text-gray-500">What type of properties do you want to focus on?</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($availablePropertyCategories as $category)
                                        <label class="relative flex cursor-pointer rounded-xl border-2 p-4 transition-all duration-200 hover:border-emerald-200 hover:bg-emerald-50/30 @if (in_array($category['value'], $form->property_categories ?? [])) border-emerald-500 bg-emerald-50 @else border-gray-100 bg-white @endif">
                                            <input type="checkbox" value="{{ $category['value'] }}" wire:model.live="form.property_categories" class="sr-only">
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="font-bold text-gray-900">{{ $category['label'] }}</span>
                                                    @if (in_array($category['value'], $form->property_categories ?? []))
                                                        <x-heroicon-s-check-circle class="w-5 h-5 text-emerald-600" />
                                                    @endif
                                                </div>
                                                <p class="text-xs text-gray-500">{{ $category['description'] }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- No Interest Selected Message -->
                        <div x-show="!(Array.isArray(interests) ? interests : []).length" class="text-center py-12 bg-gray-50 rounded-xl border border-gray-100 border-dashed">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                                <x-heroicon-o-cursor-arrow-rays class="w-6 h-6 text-gray-400" />
                            </div>
                            <p class="text-sm text-gray-500">Please select your interests above to see relevant property options</p>
                        </div>
                    </div>

                    <!-- Stage 3: Specific Options -->
                    <div x-data="specificOptions" x-cloak>
                        <div x-show="showSpecificOptions" x-transition class="space-y-6">

                            <!-- House/Apartment Options -->
                            <div x-show="showHouseOptions" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-900">3</div>
                                        <div>
                                            <h3 class="text-base font-bold text-gray-900">Houses & Apartments</h3>
                                            <p class="text-xs text-gray-500">Select residential property types</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-6 space-y-6">
                                    <!-- Apartment Subtypes -->
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 mb-3">Apartments by Bedrooms</h4>
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                            @foreach ($apartmentSubtypes as $subtype)
                                                <label class="flex items-center space-x-3 cursor-pointer group">
                                                    <x-forms.checkbox
                                                        wire:model.live="form.apartment_subtypes"
                                                        value="{{ $subtype['id'] }}"
                                                        label="{{ $subtype['name'] }}"
                                                        size="sm"
                                                    />
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- House Subtypes -->
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 mb-3">House Types</h4>
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                            @foreach ($houseSubtypes as $subtype)
                                                <label class="flex items-center space-x-3 cursor-pointer group">
                                                    <x-forms.checkbox
                                                        wire:model.live="form.house_subtypes"
                                                        value="{{ $subtype['id'] }}"
                                                        label="{{ $subtype['name'] }}"
                                                        size="sm"
                                                    />
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Land Options -->
                            <div x-show="showLandOptions" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-900">3</div>
                                        <div>
                                            <h3 class="text-base font-bold text-gray-900">Land & Plots</h3>
                                            <p class="text-xs text-gray-500">Select land sizes</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        @foreach ($landSizes as $size)
                                            <label class="flex items-center space-x-3 cursor-pointer group">
                                                <x-forms.checkbox
                                                    wire:model.live="form.land_sizes"
                                                    value="{{ $size['id'] }}"
                                                    label="{{ $size['name'] }}"
                                                    size="sm"
                                                />
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Shop Options -->
                            <div x-show="showShopOptions" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-900">3</div>
                                        <div>
                                            <h3 class="text-base font-bold text-gray-900">Commercial Shops</h3>
                                            <p class="text-xs text-gray-500">Retail spaces</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <x-forms.checkbox
                                        wire:model.live="form.shop_selected"
                                        label="Yes, I'm interested in commercial shops and retail spaces"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stage 4: Budget Ranges -->
                    <div x-data="budgetOptions" x-cloak>
                        <div x-show="showBudgets" x-transition class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-900">4</div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">Budget Ranges</h3>
                                        <p class="text-xs text-gray-500">Set separate budgets for your different property interests</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 space-y-6">
                                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
                                    <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" />
                                    <div>
                                        <p class="text-sm text-blue-900 font-medium">Budget ranges will automatically appear based on your property interests.</p>
                                        <p class="text-xs text-blue-700 mt-1">Leave fields blank for no budget restrictions.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Preferences -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-visible">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="p-1.5 bg-white border border-gray-200 rounded-lg text-gray-500">
                                    <x-heroicon-o-map-pin class="w-4 h-4" />
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">Location Preferences</h3>
                                    <p class="text-xs text-gray-500">Choose your preferred location</p>
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
                                        :options="$states->pluck('name', 'id')->toArray()"
                                        class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                                    />
                                </div>
                                <div class="overflow-visible">
                                    <x-forms.select
                                        label="City"
                                        placeholder="Select City"
                                        wire:model.live="form.preferred_location_city"
                                        :disabled="empty($form->preferred_location_state)"
                                        :options="$cities->pluck('name', 'id')->toArray()"
                                        class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                                    />
                                </div>
                                <div class="overflow-visible">
                                    <x-forms.select
                                        label="Area"
                                        placeholder="Select Area"
                                        wire:model.live="form.preferred_location_area"
                                        :disabled="empty($form->preferred_location_city)"
                                        :options="$areas->pluck('name', 'id')->toArray()"
                                        class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Preferences -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="p-1.5 bg-white border border-gray-200 rounded-lg text-gray-500">
                                    <x-heroicon-o-bell class="w-4 h-4" />
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">Notification Preferences</h3>
                                    <p class="text-xs text-gray-500">How would you like to be notified?</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <x-forms.checkbox
                                    wire:model="form.email_alerts"
                                    label="Email Alerts"
                                    description="Receive email notifications"
                                />
                                <x-forms.checkbox
                                    wire:model="form.sms_alerts"
                                    label="SMS Alerts"
                                    description="Receive SMS notifications"
                                />
                                <x-forms.checkbox
                                    wire:model="form.whatsapp_alerts"
                                    label="WhatsApp Alerts"
                                    description="Receive WhatsApp notifications"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-end pt-4">
                        <a href="{{ route('customer.settings') }}"
                           class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors font-semibold text-sm">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center justify-center px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-colors shadow-sm hover:shadow-md text-sm">
                            <x-heroicon-o-check class="w-5 h-5 mr-2" />
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