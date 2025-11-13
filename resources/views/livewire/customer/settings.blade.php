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
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Account Settings</h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Manage your profile, preferences, and account security</p>
            </div>
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-white/95 backdrop-blur-xl border border-gray-300/60 rounded-xl text-gray-700 hover:bg-white transition-all duration-500 transform hover:scale-105 shadow-lg text-sm sm:text-base">
                <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                Back to Dashboard
            </a>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="fixed top-4 right-4 z-50 bg-emerald-500 text-white px-6 py-3 rounded-xl shadow-lg"
                 x-data="{ show: true }"
                 x-show="show"
                 x-transition
                 x-init="setTimeout(() => show = false, 5000)">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg"
                 x-data="{ show: true }"
                 x-show="show"
                 x-transition
                 x-init="setTimeout(() => show = false, 5000)">
                {{ session('error') }}
            </div>
        @endif

        <!-- Loading Overlay -->
        <div wire:loading.delay class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                <div class="animate-spin h-5 w-5 border-2 border-emerald-500 border-t-transparent rounded-full"></div>
                <span class="text-gray-700">Updating...</span>
            </div>
        </div>

        <div class="space-y-6 sm:space-y-8">
            <!-- Profile Information -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 shadow-lg mr-3">
                        <x-heroicon-o-user class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                    </div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Profile Information</h2>
                </div>

                <form wire:submit="updateProfile" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-forms.input
                            label="Full Name"
                            wire:model="name"
                            required
                            placeholder="Enter your full name"
                            :error="$errors->first('name')"
                        />

                        <x-forms.input
                            label="Email Address"
                            type="email"
                            wire:model="email"
                            required
                            placeholder="Enter your email address"
                            :error="$errors->first('email')"
                        />

                        <x-forms.input
                            label="Phone Number"
                            type="tel"
                            wire:model="phone"
                            placeholder="Enter your phone number"
                            :error="$errors->first('phone')"
                        />

                        <x-forms.input
                            label="Address"
                            wire:model="address"
                            placeholder="Enter your address"
                            :error="$errors->first('address')"
                        />
                    </div>


                    <div class="flex justify-end">
                        <x-forms.button type="submit" variant="primary">
                            Update Profile
                        </x-forms.button>
                    </div>
                </form>
            </div>

            <!-- Search Preferences -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <div class="flex items-center">
                        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-linear-to-br from-blue-500 to-indigo-600 shadow-lg mr-3">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                        </div>
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Search Preferences</h2>
                    </div>
                    <a href="{{ route('customer.preferences') }}"
                       class="inline-flex items-center px-4 py-2 bg-linear-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-700 hover:via-emerald-600 hover:to-teal-600 text-white font-semibold rounded-xl transition-all duration-500 transform hover:scale-105 shadow-lg text-sm">
                        <x-heroicon-o-sparkles class="w-4 h-4 mr-2" />
                        Enhanced Preferences
                    </a>
                </div>

                <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-start">
                        <x-heroicon-o-sparkles class="w-5 h-5 text-blue-600 mt-0.5 mr-3 shrink-0" />
                        <div>
                            <p class="text-sm font-medium text-blue-900">Try our Enhanced Preferences!</p>
                            <p class="text-sm text-blue-700 mt-1">Get better property recommendations with our improved preference system featuring smart filtering and instant notifications.</p>
                        </div>
                    </div>
                </div>

                <form wire:submit="updateSearchPreferences" class="space-y-4">
                    <!-- Location Preferences -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Preferred Location</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <x-forms.select
                                label="State"
                                wire:model.live="preferred_location_state"
                                placeholder="Select State"
                                :options="$states"
                                :error="$errors->first('preferred_location_state')"
                            />

                            <x-forms.select
                                label="City"
                                wire:model.live="preferred_location_city"
                                placeholder="Select City"
                                :options="$cities"
                                :disabled="!$preferred_location_state"
                                :error="$errors->first('preferred_location_city')"
                            />

                            <x-forms.select
                                label="Area"
                                wire:model="preferred_location_area"
                                placeholder="Select Area"
                                :options="$areas"
                                :disabled="!$preferred_location_city"
                                :error="$errors->first('preferred_location_area')"
                            />
                        </div>
                    </div>

                    <!-- Property Preferences -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Property Preferences</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Property Types</label>
                                <div class="space-y-2 max-h-32 overflow-y-auto border border-gray-300/60 rounded-xl p-3 bg-white/95 backdrop-blur-xl">
                                    @foreach($propertyTypes as $type)
                                        <x-forms.checkbox
                                            wire:model.defer="preferred_property_types"
                                            value="{{ $type->id }}"
                                            label="{{ $type->name }}"
                                            size="sm"
                                        />
                                    @endforeach
                                </div>
                                @error('preferred_property_types')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <x-forms.select
                                label="Listing Type"
                                wire:model="preferred_listing_type"
                                placeholder="Any"
                                :options="[
                                    '' => 'Any',
                                    'rent' => 'Rent',
                                    'sale' => 'Sale'
                                ]"
                                :error="$errors->first('preferred_listing_type')"
                            />
                        </div>
                    </div>

                    <!-- Budget Range -->
                    {{-- <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Budget Range</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-forms.currency
                                label="Minimum Budget"
                                wire:model="min_budget"
                                placeholder="0.00"
                                :error="$errors->first('min_budget')"
                            />

                            <x-forms.currency
                                label="Maximum Budget"
                                wire:model="max_budget"
                                placeholder="0.00"
                                :error="$errors->first('max_budget')"
                            />
                        </div>
                    </div> --}}

                    <div class="flex justify-end">
                        <x-forms.button type="submit" variant="info">
                            Update Preferences
                        </x-forms.button>
                    </div>
                </form>
            </div>

            <!-- Notification Settings -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-linear-to-br from-yellow-500 to-orange-500 shadow-lg mr-3">
                        <x-heroicon-o-bell class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                    </div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Notification Settings</h2>
                </div>

                <form wire:submit="updateNotifications" class="space-y-4">
                    <div class="space-y-4">
                        <x-forms.toggle
                            wire:model="email_notifications"
                            label="Email Notifications"
                            description="Receive email notifications for important updates"
                            :checked="$email_notifications"
                        />

                        <x-forms.toggle
                            wire:model="new_properties"
                            label="New Properties"
                            description="Get notified when new properties match your preferences"
                            :checked="$new_properties"
                        />

                        <x-forms.toggle
                            wire:model="price_alerts"
                            label="Price Alerts"
                            description="Receive alerts when saved property prices change"
                            :checked="$price_alerts"
                        />

                        <x-forms.toggle
                            wire:model="inquiry_responses"
                            label="Inquiry Responses"
                            description="Get notified when agents respond to your inquiries"
                            :checked="$inquiry_responses"
                        />

                        <x-forms.toggle
                            wire:model="marketing_emails"
                            label="Marketing Emails"
                            description="Receive promotional emails and newsletters"
                            :checked="$marketing_emails"
                        />
                    </div>

                    <div class="flex justify-end">
                        <x-forms.button type="submit" variant="warning">
                            Update Notifications
                        </x-forms.button>
                    </div>
                </form>
            </div>

            <!-- Security -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6">
                <div class="flex items-center mb-4 sm:mb-6">
                    <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-linear-to-br from-red-500 to-pink-600 shadow-lg mr-3">
                        <x-heroicon-o-lock-closed class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                    </div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Security</h2>
                </div>

                <form wire:submit="updatePassword" class="space-y-4">
                    <x-forms.input
                        label="Current Password"
                        type="password"
                        wire:model="current_password"
                        placeholder="Enter your current password"
                        :error="$errors->first('current_password')"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-forms.input
                            label="New Password"
                            type="password"
                            wire:model="new_password"
                            placeholder="Enter new password"
                            :error="$errors->first('new_password')"
                        />

                        <x-forms.input
                            label="Confirm New Password"
                            type="password"
                            wire:model="new_password_confirmation"
                            placeholder="Confirm new password"
                            :error="$errors->first('new_password_confirmation')"
                        />
                    </div>

                    <div class="flex justify-end">
                        <x-forms.button type="submit" variant="danger">
                            Update Password
                        </x-forms.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

