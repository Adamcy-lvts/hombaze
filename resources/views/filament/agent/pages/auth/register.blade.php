<x-filament-panels::page.simple>
    {{-- Create a legal document service for terms and privacy policy links --}}
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
            @apply bg-linear-to-br from-primary-50 via-white to-primary-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen py-8;
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
        
        .agent-form-container {
            @apply p-10;
        }
        
        @media (max-width: 1024px) {
            .agent-form-container {
                @apply p-8;
            }
        }
        
        @media (max-width: 768px) {
            .agent-form-container {
                @apply p-6;
            }
        }
        
        @media (max-width: 640px) {
            .agent-form-container {
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
        
        /* Progress indicator - REMOVED */
        .form-progress {
            display: none !important;
        }
    </style>

    <div class="agent-form-container">
        <!-- Compact Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-linear-to-br from-primary-500 to-primary-600 rounded-xl mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Join {{ $appName }}
            </h1>
            <p class="text-base text-gray-600 dark:text-gray-400 mb-1">
                Become a certified real estate agent
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-500">
                Start managing properties and connect with clients
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

        <!-- Progress indicator -->
        <div class="form-progress"></div>

        <!-- Form Content -->
        <div class="space-y-1">
            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_REGISTER_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

            <x-filament-panels::form wire:submit="register">
                {{ $this->form }}
                
                <div class="mt-8">
                    <x-filament::button
                        type="submit"
                        class="w-full bg-linear-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700"
                        size="lg"
                    >
                        Create Agent Account
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
                    <a href="#" target="_blank" class="text-primary-600 hover:text-primary-500 dark:text-primary-400 underline">Terms</a>
                    and
                    <a href="{$" target="_blank" class="text-primary-600 hover:text-primary-500 dark:text-primary-400 underline">Privacy Policy</a>
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
                        Professional
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page.simple>
