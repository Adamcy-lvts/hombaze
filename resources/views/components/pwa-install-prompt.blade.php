{{--
    PWA Install Prompt Component

    This component shows a custom install prompt for the PWA.
    It appears as a bottom banner on mobile devices when the app can be installed.

    The prompt is controlled by Alpine.js and integrates with the
    beforeinstallprompt event from the browser.
--}}

<div
    x-data="pwaInstallPrompt()"
    x-show="showPrompt"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="transform translate-y-full opacity-0"
    x-transition:enter-end="transform translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="transform translate-y-0 opacity-100"
    x-transition:leave-end="transform translate-y-full opacity-0"
    x-cloak
    class="fixed bottom-0 inset-x-0 z-50 p-4 sm:p-6 pointer-events-none"
    role="dialog"
    aria-labelledby="pwa-install-title"
>
    <div class="max-w-lg mx-auto pointer-events-auto">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
            {{-- Header with app icon --}}
            <div class="flex items-start gap-4 p-4 sm:p-5">
                {{-- App Icon --}}
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1457.15 1140.01" fill="currentColor">
                            <path d="M1274.97 1042.15c64.4 27.18 125.1 58.36 182.18 96.48Q728.36 851.08 0 1140.01c2.44-1.86 4.86-3.94 7.5-5.69 52.31-34.5 108.05-62.58 165.48-87.32 9.14-4 11.37-8.71 11.35-17.86q-.45-168.81-.27-337.63v-16.42l58-21.17v366.77l49.87-16.39V637.59c2.74-1.31 4.44-2.30 6.26-3 27.45-10 55-19.82 82.36-30a26.64 26.64 0 0 1 19.69.07c35.67 13.16 71.45 26 107.22 39 23.71 8.57 47.47 17 71.19 25.58 2.24.81 4.38 1.89 8.06 3.5v272.17c4.74 0 7.9.22 11 0 12.3-1 24.58-2.2 37.57-3.38.31-4.22.77-7.68.77-11.15q0-138.78-.1-277.55c0-12.45 0-12.56-12-17.1-11.58-4.4-23.25-8.56-35.33-13V53.27c3.41-1.47 6.94-3.15 10.57-4.55 41.1-15.77 82.27-31.35 123.26-47.39 6.38-2.49 11.46-1.12 17.16 1.22q78.3 32.18 156.7 64.1 58.38 23.78 116.85 47.33c4 1.64 8 3.41 12.42 5.27v187.6l-295.47 121v509.93c15.47 1.15 30.86.43 47.55 1.08v-477.8l94.4-39.47v523.13l48.9 5.18V402.81c13-5.4 25.18-10.43 37.32-15.43 20-8.24 40-16.25 59.9-24.8 6.33-2.73 11.79-2.85 18.22-.16q99.67 41.73 199.54 83c9.24 3.84 18.52 7.57 27.77 11.41 11.31 4.69 11.31 4.72 11.31 16.32q0 91.32-.08 182.63v374.86Z"/>
                            <path d="M394.28 549.45v-430l134.16-46.76a20 20 0 0 1 2.28 1.31c.28.24.64.59.64.9q.12 257.64.15 515.3c0 2.74-.24 5.49-.37 8.22-8.81-.29-121.96-40.83-136.86-48.97"/>
                        </svg>
                    </div>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <h3 id="pwa-install-title" class="text-base sm:text-lg font-bold text-gray-900">
                        Install HomeBaze
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 leading-snug">
                        Add to your home screen for quick access to properties and faster browsing.
                    </p>
                </div>

                {{-- Close button --}}
                <button
                    @click="dismiss()"
                    class="flex-shrink-0 p-1.5 -mr-1 -mt-1 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                    aria-label="Dismiss"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 px-4 pb-4 sm:px-5 sm:pb-5">
                <button
                    @click="dismiss()"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors"
                >
                    Not Now
                </button>
                <button
                    @click="install()"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 transition-all"
                >
                    Install App
                </button>
            </div>

            {{-- iOS Instructions (shown only on iOS) --}}
            <template x-if="isIOS">
                <div class="px-4 pb-4 sm:px-5 sm:pb-5 -mt-2">
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm text-amber-800">
                                <p class="font-medium">To install on iOS:</p>
                                <p class="mt-1">
                                    Tap the
                                    <svg class="inline w-4 h-4 mx-0.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3 3h-2v6h-2V5H9l3-3zm-7 9v10h14V11h-4v2h2v6H7v-6h2v-2H5z"/>
                                    </svg>
                                    Share button, then "Add to Home Screen"
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function pwaInstallPrompt() {
        return {
            showPrompt: false,
            deferredPrompt: null,
            isIOS: false,

            init() {
                // Check if already installed
                if (window.matchMedia('(display-mode: standalone)').matches) {
                    return;
                }

                // Check if dismissed recently (within 7 days)
                const dismissed = localStorage.getItem('pwa-prompt-dismissed');
                if (dismissed) {
                    const dismissedAt = parseInt(dismissed);
                    const sevenDays = 7 * 24 * 60 * 60 * 1000;
                    if (Date.now() - dismissedAt < sevenDays) {
                        return;
                    }
                }

                // Check for iOS
                this.isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

                // For iOS, show prompt after a delay
                if (this.isIOS) {
                    // Check if running in standalone mode on iOS
                    if (navigator.standalone) {
                        return;
                    }
                    setTimeout(() => {
                        this.showPrompt = true;
                    }, 3000);
                    return;
                }

                // For other browsers, listen for beforeinstallprompt
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    this.deferredPrompt = e;

                    // Show prompt after a short delay
                    setTimeout(() => {
                        this.showPrompt = true;
                    }, 3000);
                });

                // Listen for successful install
                window.addEventListener('appinstalled', () => {
                    this.showPrompt = false;
                    this.deferredPrompt = null;
                    localStorage.setItem('pwa-installed', 'true');
                });
            },

            async install() {
                if (this.isIOS) {
                    // iOS doesn't support programmatic install, just show instructions
                    return;
                }

                if (!this.deferredPrompt) {
                    return;
                }

                // Show the install prompt
                this.deferredPrompt.prompt();

                // Wait for the user's response
                const { outcome } = await this.deferredPrompt.userChoice;

                if (outcome === 'accepted') {
                    console.log('PWA installed');
                }

                this.deferredPrompt = null;
                this.showPrompt = false;
            },

            dismiss() {
                this.showPrompt = false;
                localStorage.setItem('pwa-prompt-dismissed', Date.now().toString());
            }
        };
    }
</script>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
