<x-filament-panels::page.simple>
    @php
        $appName = config('app.name', 'HomeBaze');
    @endphp
    
    <!-- Custom Styles -->
    <style>
        .fi-simple-main {
            max-width: 800px !important;
            @apply mx-auto bg-white dark:bg-gray-900 shadow-2xl rounded-2xl overflow-hidden;
            border: 1px solid rgba(0, 0, 0, 0.08);
        }
        
        .fi-simple-page {
            @apply bg-linear-to-br from-blue-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen py-8;
        }
    
        @media (max-width: 1024px) {
            .fi-simple-main {
                max-width: 90% !important;
                @apply mx-4;
            }
        }
        
        @media (max-width: 768px) {
            .fi-simple-main {
                max-width: 95% !important;
                @apply mx-3 rounded-xl;
            }
            .fi-simple-page {
                @apply py-6;
            }
        }
        
        @media (max-width: 640px) {
            .fi-simple-main {
                max-width: 98% !important;
                @apply mx-2 rounded-lg;
            }
            .fi-simple-page {
                @apply py-4;
            }
        }
        
        .tenant-form-container {
            @apply p-10;
        }
        
        @media (max-width: 1024px) {
            .tenant-form-container {
                @apply p-8;
            }
        }
        
        @media (max-width: 768px) {
            .tenant-form-container {
                @apply p-6;
            }
        }
        
        @media (max-width: 640px) {
            .tenant-form-container {
                @apply p-5;
            }
        }
        
        /* Compact section styling */
        .fi-section {
            @apply mb-8 last:mb-0;
            border: none !important;
        }
        
        .fi-section > div:first-child {
            @apply mb-5;
        }
        
        .fi-section-header {
            border-bottom: none !important;
            padding-bottom: 0 !important;
        }
        
        .fi-section-content {
            border-top: none !important;
            padding-top: 1.25rem !important;
        }
        
        /* Remove any section separators */
        .fi-section::after,
        .fi-section::before,
        .fi-section-header::after,
        .fi-section-header::before {
            display: none !important;
        }
        
        .fi-section-header-heading {
            @apply text-lg font-semibold text-gray-900 dark:text-white;
        }
        
        .fi-section-header-description {
            @apply text-sm text-gray-600 dark:text-gray-400 mt-1;
        }
        
        /* Form inputs styling */
        .fi-input {
            @apply text-sm;
        }
        
        .fi-input-wrapper {
            @apply mb-4;
        }
        
        /* Grid responsive adjustments */
        @media (max-width: 768px) {
            .fi-grid-cols-3 > * {
                grid-column: span 1 !important;
            }
            .fi-grid-cols-2 > * {
                grid-column: span 1 !important;
            }
        }
        
        @media (max-width: 640px) {
            .fi-section {
                @apply mb-6;
            }
            .fi-section > div:first-child {
                @apply mb-4;
            }
            .fi-section-header-heading {
                @apply text-base;
            }
            .fi-input-wrapper {
                @apply mb-3;
            }
        }
        
        /* Tab styling for tenant theme */
        .fi-tabs-tab {
            @apply transition-colors duration-200;
        }
        
        .fi-tabs-tab[aria-selected="true"] {
            @apply bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700;
        }
        
        /* Primary button tenant styling */
        .fi-btn-primary {
            @apply bg-linear-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 border-blue-600;
        }
    </style>

    <div class="tenant-form-container">
        <!-- Compact Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-linear-to-br from-blue-500 to-blue-600 rounded-xl mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Join {{ $appName }}
            </h1>
            <p class="text-base text-gray-600 dark:text-gray-400 mb-1">
                Find your perfect home
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-500">
                Search properties, manage rentals, and connect with landlords
            </p>
            
            @if (filament()->hasLogin())
                <div class="mt-4 text-sm">
                    <span class="text-gray-600 dark:text-gray-400">
                        {{ __('filament-panels::pages/auth/register.actions.login.before') }}
                    </span>
                    {{ $this->loginAction }}
                </div>
            @endif
        </div>

        <!-- Form Content -->
        <div class="space-y-1">
            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_REGISTER_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

            <x-filament-panels::form wire:submit="register">
                {{ $this->form }}
                
                <div class="mt-8">
                    <x-filament::button
                        type="submit"
                        class="w-full bg-linear-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700"
                        size="lg"
                    >
                        Create Tenant Account
                    </x-filament::button>
                </div>
            </x-filament-panels::form>

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_REGISTER_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
        </div>

        <!-- Compact Footer -->
        <div class="mt-8 pt-8">
            <div class="text-center text-sm text-gray-500 dark:text-gray-400 space-y-3">
                <p>
                    By registering, you agree to our
                    <a href="#" target="_blank" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 underline">Terms</a>
                    and
                    <a href="#" target="_blank" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 underline">Privacy Policy</a>
                </p>
                <p class="text-xs">&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
                <div class="flex items-center justify-center space-x-4 text-xs pt-2">
                    <span class="inline-flex items-center text-green-600">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Secure
                    </span>
                    <span class="inline-flex items-center text-blue-600">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                        </svg>
                        Verified
                    </span>
                    <span class="inline-flex items-center text-purple-600">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                        Tenant Portal
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page.simple>
