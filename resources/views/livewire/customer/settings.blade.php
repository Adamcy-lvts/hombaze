<div class="min-h-screen bg-gray-50 font-sans text-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Account Settings</h1>
                <p class="text-gray-500 mt-2">Manage your profile, preferences, and account security</p>
            </div>
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                <x-heroicon-o-arrow-left class="w-4 h-4 mr-1" />
                Back to Dashboard
            </a>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl flex items-start gap-3"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                <p class="text-sm font-medium text-emerald-900">{{ session('success') }}</p>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-xl flex items-start gap-3"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <x-heroicon-o-exclamation-circle class="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
                <p class="text-sm font-medium text-red-900">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Loading Overlay -->
        <div wire:loading.delay class="fixed inset-0 bg-black/20 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="bg-white rounded-xl p-4 shadow-lg flex items-center gap-3">
                <div class="animate-spin h-5 w-5 border-2 border-emerald-500 border-t-transparent rounded-full"></div>
                <span class="text-sm font-medium text-gray-700">Updating...</span>
            </div>
        </div>

        <div class="space-y-8">
            <!-- Profile Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                        <x-heroicon-o-user class="w-6 h-6" />
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Profile Information</h2>
                </div>

                <form wire:submit="updateProfile" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-forms.input
                            label="Full Name"
                            wire:model="name"
                            required
                            placeholder="Enter your full name"
                            :error="$errors->first('name')"
                            class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                        />

                        <x-forms.input
                            label="Email Address"
                            type="email"
                            wire:model="email"
                            required
                            placeholder="Enter your email address"
                            :error="$errors->first('email')"
                            class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                        />

                        <x-forms.input
                            label="Phone Number"
                            type="tel"
                            wire:model="phone"
                            placeholder="Enter your phone number"
                            :error="$errors->first('phone')"
                            class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                        />

                        <x-forms.input
                            label="Address"
                            wire:model="address"
                            placeholder="Enter your address"
                            :error="$errors->first('address')"
                            class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                        />
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm hover:shadow-md">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Search Preferences -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                            <x-heroicon-o-magnifying-glass class="w-6 h-6" />
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Search Preferences</h2>
                    </div>
                    <a href="{{ route('customer.preferences') }}"
                       class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 text-sm font-semibold rounded-xl hover:bg-emerald-100 transition-colors">
                        <x-heroicon-o-sparkles class="w-4 h-4 mr-2" />
                        Enhanced Preferences
                    </a>
                </div>

                <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-100 flex items-start gap-3">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" />
                    <div>
                        <p class="text-sm font-medium text-blue-900">Try our Enhanced Preferences!</p>
                        <p class="text-sm text-blue-700 mt-1">Get better property recommendations with our improved preference system featuring smart filtering and instant notifications.</p>
                    </div>
                </div>

                <form wire:submit="updateSearchPreferences" class="space-y-6">
                    <!-- Location Preferences -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Preferred Location</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <x-forms.select
                                label="State"
                                wire:model.live="preferred_location_state"
                                placeholder="Select State"
                                :options="$states"
                                :error="$errors->first('preferred_location_state')"
                                class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                            />

                            <x-forms.select
                                label="City"
                                wire:model.live="preferred_location_city"
                                placeholder="Select City"
                                :options="$cities"
                                :disabled="!$preferred_location_state"
                                :error="$errors->first('preferred_location_city')"
                                class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                            />

                            <x-forms.select
                                label="Area"
                                wire:model="preferred_location_area"
                                placeholder="Select Area"
                                :options="$areas"
                                :disabled="!$preferred_location_city"
                                :error="$errors->first('preferred_location_area')"
                                class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                            />
                        </div>
                    </div>

                    <!-- Property Preferences -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Property Preferences</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Property Types</label>
                                <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-xl p-4 space-y-2 bg-gray-50">
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
                                :options="['' => 'Any', 'rent' => 'Rent', 'sale' => 'Sale']"
                                :error="$errors->first('preferred_listing_type')"
                                class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 text-sm font-semibold rounded-xl transition-colors">
                            Update Preferences
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notification Settings -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                        <x-heroicon-o-bell class="w-6 h-6" />
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Notification Settings</h2>
                </div>

                <form wire:submit="updateNotifications" class="space-y-6">
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
                        <button type="submit" class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 text-sm font-semibold rounded-xl transition-colors">
                            Update Notifications
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-red-50 rounded-lg text-red-600">
                        <x-heroicon-o-lock-closed class="w-6 h-6" />
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Security</h2>
                </div>

                <form wire:submit="updatePassword" class="space-y-6">
                    <x-forms.input
                        label="Current Password"
                        type="password"
                        wire:model="current_password"
                        placeholder="Enter your current password"
                        :error="$errors->first('current_password')"
                        class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-forms.input
                            label="New Password"
                            type="password"
                            wire:model="new_password"
                            placeholder="Enter new password"
                            :error="$errors->first('new_password')"
                            class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                        />

                        <x-forms.input
                            label="Confirm New Password"
                            type="password"
                            wire:model="new_password_confirmation"
                            placeholder="Confirm new password"
                            :error="$errors->first('new_password_confirmation')"
                            class="rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500"
                        />
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-red-50 text-red-700 hover:bg-red-100 text-sm font-semibold rounded-xl transition-colors">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
