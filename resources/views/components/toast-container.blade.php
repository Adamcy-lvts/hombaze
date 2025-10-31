<!-- Toast Container for Session Messages -->
<div id="toast-container" class="fixed top-4 right-4 z-[9999] space-y-2">
    <!-- Success Messages -->
    @if(session('success'))
        <x-toast type="success" :message="session('success')" title="Success!" />
    @endif

    <!-- Error Messages -->
    @if(session('error'))
        <x-toast type="error" :message="session('error')" title="Error!" />
    @endif

    <!-- Warning Messages -->
    @if(session('warning'))
        <x-toast type="warning" :message="session('warning')" title="Warning!" />
    @endif

    <!-- Info Messages -->
    @if(session('info'))
        <x-toast type="info" :message="session('info')" title="Info" />
    @endif

    <!-- Validation Errors (if you want to show them as toasts) -->
    @if($errors->any() && session('show_errors_as_toast'))
        <x-toast type="error" :message="$errors->first()" title="Validation Error" />
    @endif
</div>

<!-- Livewire event listeners for dynamic toasts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Listen for Livewire events to show toasts
    if (window.Livewire) {
        window.addEventListener('toast', event => {
            showToast(event.detail.type || 'info', event.detail.message, event.detail.title);
        });

        window.addEventListener('preferences-saved', event => {
            showToast('success', 'Your preferences have been saved successfully!', 'Preferences Updated');
        });

        window.addEventListener('preferences-error', event => {
            showToast('error', event.detail.message || 'There was an error saving your preferences.', 'Error');
        });
    }
});

function showToast(type, message, title = '') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

    const typeConfig = {
        'success': {
            bg: 'bg-green-50 border-green-200',
            text: 'text-green-800',
            icon: 'text-green-400',
            iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
        },
        'error': {
            bg: 'bg-red-50 border-red-200',
            text: 'text-red-800',
            icon: 'text-red-400',
            iconPath: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
        },
        'warning': {
            bg: 'bg-yellow-50 border-yellow-200',
            text: 'text-yellow-800',
            icon: 'text-yellow-400',
            iconPath: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z'
        },
        'info': {
            bg: 'bg-blue-50 border-blue-200',
            text: 'text-blue-800',
            icon: 'text-blue-400',
            iconPath: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
        }
    };

    const config = typeConfig[type] || typeConfig['info'];

    const toastHTML = `
        <div id="${toastId}" class="max-w-sm w-full transform transition-all duration-300 ease-in-out translate-x-full opacity-0">
            <div class="rounded-lg border shadow-lg ${config.bg} p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 ${config.icon}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${config.iconPath}" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        ${title ? `<h3 class="text-sm font-medium ${config.text}">${title}</h3>` : ''}
                        <div class="text-sm ${config.text} ${title ? 'mt-1' : ''}">
                            ${message}
                        </div>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button
                            onclick="dismissToast('${toastId}')"
                            class="inline-flex rounded-md ${config.bg} ${config.text} hover:opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2"
                        >
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', toastHTML);

    // Animate in
    setTimeout(() => {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.classList.remove('translate-x-full', 'opacity-0');
        }
    }, 100);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        dismissToast(toastId);
    }, 5000);
}

function dismissToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
}
</script>