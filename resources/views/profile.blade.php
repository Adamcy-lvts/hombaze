<x-app-layout>
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
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Profile</h1>
                    <p class="text-sm sm:text-base text-gray-600 mt-1">Manage your profile information and account security</p>
                </div>
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-4 py-2 bg-white/95 backdrop-blur-xl border border-gray-300/60 rounded-xl text-gray-700 hover:bg-white transition-all duration-500 transform hover:scale-105 shadow-lg text-sm sm:text-base">
                    <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                    Back to Dashboard
                </a>
            </div>

            <div class="space-y-6 sm:space-y-8">
                <!-- Profile Information -->
                <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6">
                    <livewire:profile.update-profile-information-form />
                </div>

                <!-- Security -->
                <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6">
                    <livewire:profile.update-password-form />
                </div>

                <!-- Delete Account -->
                <div class="bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl border border-gray-300/60 shadow-lg p-4 sm:p-6">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
