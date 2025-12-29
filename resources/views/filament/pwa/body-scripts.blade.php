{{-- PWA Scripts for Filament Panels --}}

<!-- PWA Service Worker Registration -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then((registration) => {
                    console.log('[PWA] Service Worker registered with scope:', registration.scope);
                })
                .catch((error) => {
                    console.log('[PWA] Service Worker registration failed:', error);
                });
        });
    }
</script>

<!-- PWA Install Prompt -->
<x-pwa-install-prompt />
