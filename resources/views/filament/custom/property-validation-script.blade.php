{{-- Property Image Validation Script --}}
@once
    @vite('resources/js/property-image-validator.js')

    <style>
        .property-image-validation-errors {
            animation: slideIn 0.3s ease-out;
        }

        .property-image-validation-success {
            animation: slideIn 0.3s ease-out;
        }

        .property-image-dimension-info {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        /* Style adjustments for better integration with Filament */
        .property-image-validation-errors,
        .property-image-validation-success,
        .property-image-dimension-info {
            font-family: inherit;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .property-image-validation-errors {
            background-color: rgb(254 242 242);
            border-color: rgb(254 202 202);
            color: rgb(185 28 28);
        }

        .property-image-validation-success {
            background-color: rgb(240 253 244);
            border-color: rgb(187 247 208);
            color: rgb(21 128 61);
        }

        .property-image-dimension-info {
            background-color: rgb(239 246 255);
            border-color: rgb(191 219 254);
            color: rgb(29 78 216);
        }

        /* Dark mode support */
        .dark .property-image-validation-errors {
            background-color: rgb(69 10 10);
            border-color: rgb(127 29 29);
            color: rgb(248 113 113);
        }

        .dark .property-image-validation-success {
            background-color: rgb(20 83 45);
            border-color: rgb(34 197 94);
            color: rgb(134 239 172);
        }

        .dark .property-image-dimension-info {
            background-color: rgb(30 58 138);
            border-color: rgb(59 130 246);
            color: rgb(147 197 253);
        }
    </style>

    <script>
        // Initialize property image validation when Livewire component is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Re-initialize validation after Livewire updates
            Livewire.hook('component.initialized', function() {
                if (window.PropertyImageValidator) {
                    new window.PropertyImageValidator();
                }
            });

            // Re-initialize after Livewire DOM updates
            Livewire.hook('morph.updated', function() {
                if (window.PropertyImageValidator) {
                    new window.PropertyImageValidator();
                }
            });
        });

        // Additional configuration for property type limits
        window.propertyTypeLimits = {!! json_encode([
            'apartment' => getPropertyImageConfig('apartment')['gallery_max_files'],
            'house' => getPropertyImageConfig('house')['gallery_max_files'],
            'land' => getPropertyImageConfig('land')['gallery_max_files'],
            'commercial' => getPropertyImageConfig('commercial')['gallery_max_files'],
            'office-space' => getPropertyImageConfig('office-space')['gallery_max_files'],
            'warehouse' => getPropertyImageConfig('warehouse')['gallery_max_files'],
        ]) !!};

        window.optimalImageConfig = {!! json_encode(getOptimalImageResolution()) !!};
    </script>
@endonce