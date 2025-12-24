<style>
    #navbar,
    #page-loader,
    #navbar + div {
        display: none !important;
    }
</style>
<div class="min-h-screen bg-gray-50 font-sans text-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Account Settings</h1>
                <p class="text-gray-500 mt-2">Manage your notification preferences</p>
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
        </div>
    </div>
</div>
