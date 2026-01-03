/**
 * Property Image Validation
 * Provides real-time client-side validation for property images
 */

class PropertyImageValidator {
    constructor() {
        this.config = {
            minWidth: 1024,
            minHeight: 683,
            recommendedWidth: 1440,
            recommendedHeight: 960,
            maxFileSizeMB: 5,
            allowedTypes: ['image/jpeg', 'image/png', 'image/webp'],
            aspectRatio: 3/2,
            aspectTolerance: 0.15
        };

        this.propertyTypeConfigs = {
            'apartment': { maxFiles: 10, type: 'apartment' },
            'house': { maxFiles: 10, type: 'house' },
            'land': { maxFiles: 4, type: 'land' },
            'commercial': { maxFiles: 5, type: 'commercial' },
            'office-space': { maxFiles: 8, type: 'office space' },
            'warehouse': { maxFiles: 6, type: 'warehouse' }
        };

        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.attachValidators());
        } else {
            this.attachValidators();
        }
    }

    attachValidators() {
        // Find all file upload inputs for property images
        this.attachToFileInputs();

        // Listen for property type changes
        this.listenForPropertyTypeChanges();
    }

    attachToFileInputs() {
        const fileInputs = document.querySelectorAll('input[type="file"][accept*="image"]');

        fileInputs.forEach(input => {
            if (input.closest('[data-field-wrapper="featured_image"]') ||
                input.closest('[data-field-wrapper="gallery_images"]')) {

                input.addEventListener('change', (e) => {
                    this.validateFiles(e.target.files, input);
                });
            }
        });
    }

    listenForPropertyTypeChanges() {
        // Listen for property type select changes to update validation messages
        const propertyTypeSelect = document.querySelector('select[name="property_type_id"]');
        if (propertyTypeSelect) {
            propertyTypeSelect.addEventListener('change', (e) => {
                this.updateValidationMessages();
            });
        }
    }

    async validateFiles(files, input) {
        const validFiles = [];
        const errors = [];

        const validations = Array.from(files).map((file, index) => {
            return Promise.resolve(this.validateSingleFile(file)).then((result) => ({
                result,
                file,
                index
            }));
        });

        const results = await Promise.all(validations);

        results.forEach(({ result, file, index }) => {
            if (result.valid) {
                validFiles.push({ file, meta: result });
            } else {
                errors.push(`File ${index + 1}: ${result.error}`);
            }
        });

        if (errors.length > 0) {
            this.showValidationErrors(errors, input);
            // Clear invalid files to prevent failed uploads from being submitted.
            input.value = '';
            return;
        }

        this.clearValidationErrors(input);
        this.showSuccessMessage(validFiles.map(({ file }) => file), input);

        const firstMeta = validFiles[0]?.meta;
        if (firstMeta?.width && firstMeta?.height) {
            this.showDimensionInfo(firstMeta.width, firstMeta.height, firstMeta.sizeMB, input);
        }
    }

    validateSingleFile(file) {
        // Check file type
        if (!this.config.allowedTypes.includes(file.type)) {
            return {
                valid: false,
                error: '📷 Please upload a valid image file (JPEG, PNG, or WebP)'
            };
        }

        // Check file size
        const fileSizeMB = file.size / (1024 * 1024);
        if (fileSizeMB > this.config.maxFileSizeMB) {
            return {
                valid: false,
                error: `📏 Image is too large (${fileSizeMB.toFixed(1)}MB). Maximum: ${this.config.maxFileSizeMB}MB`
            };
        }

        // For immediate feedback, validate dimensions when the image loads.
        return new Promise((resolve) => {
            const img = new Image();

            img.onload = () => {
                const validation = this.validateDimensions(img.width, img.height, file.name);
                resolve({
                    ...validation,
                    width: img.width,
                    height: img.height,
                    sizeMB: fileSizeMB
                });
            };

            img.onerror = () => {
                const errorValidation = {
                    valid: false,
                    error: '❌ Invalid image file - unable to read image dimensions'
                };
                resolve(errorValidation);
            };

            img.src = URL.createObjectURL(file);
        });
    }

    validateDimensions(width, height, filename) {
        // Check minimum dimensions
        if (width < this.config.minWidth || height < this.config.minHeight) {
            return {
                valid: false,
                error: `📐 Image resolution too low. Minimum: ${this.config.minWidth}×${this.config.minHeight}px. Your image: ${width}×${height}px`
            };
        }

        // Check aspect ratio (non-blocking, just informational)
        const actualRatio = width / height;
        const expectedRatio = this.config.aspectRatio;

        if (Math.abs(actualRatio - expectedRatio) > this.config.aspectTolerance) {
            const recommendedWidth = Math.round(height * expectedRatio);
            const recommendedHeight = Math.round(width / expectedRatio);

            // Log for analytics but don't block upload
            console.info(`Image aspect ratio notice: ${filename} (${width}×${height}) - optimal dimensions would be ${recommendedWidth}×${height} or ${width}×${recommendedHeight}`);
        }

        return { valid: true };
    }

    showValidationErrors(errors, input) {
        this.clearValidationErrors(input);

        const wrapper = input.closest('[data-field-wrapper]');
        if (!wrapper) return;

        // Create a more prominent error display
        const errorContainer = document.createElement('div');
        errorContainer.className = 'property-image-validation-errors mt-2 p-4 bg-red-50 border-2 border-red-300 rounded-lg shadow-xs';
        errorContainer.style.animation = 'shake 0.6s ease-in-out';

        errorContainer.innerHTML = `
            <div class="flex items-start">
                <div class="text-red-500 shrink-0 mr-3">
                    <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-semibold text-red-800 mb-2">❌ Upload Failed - Image Requirements Not Met</div>
                    <ul class="text-sm text-red-700 space-y-2">
                        ${errors.map(error => `<li class="flex items-start"><span class="text-red-400 mr-2">•</span><span>${error}</span></li>`).join('')}
                    </ul>
                    <div class="mt-3 p-2 bg-red-100 rounded-sm text-xs text-red-600">
                        <strong>💡 Quick Fix:</strong> Use images at least ${this.config.minWidth}×${this.config.minHeight}px, under ${this.config.maxFileSizeMB}MB, in JPEG/PNG/WebP format.
                    </div>
                </div>
            </div>
        `;

        wrapper.appendChild(errorContainer);

        // Auto-scroll to error if not visible
        setTimeout(() => {
            errorContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);

        // Play error sound (if available)
        if ('Audio' in window) {
            try {
                const errorSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmAaBDaN0e/adyEELYPR8Ni9oCUGI1mr5+OVSA0PVqzn77BdGAg+ltryxnkpBSl+zfPrfyUDEUSe0vPpbSMBADJ30s3PwDNOQwgWe8bq3IVHaB9kAUqGxhY=');
                errorSound.volume = 0.3;
                errorSound.play().catch(() => {});
            } catch (e) {}
        }
    }

    clearValidationErrors(input) {
        const wrapper = input.closest('[data-field-wrapper]');
        if (!wrapper) return;

        const existingErrors = wrapper.querySelectorAll('.property-image-validation-errors, .property-image-validation-success, .property-image-dimension-info');
        existingErrors.forEach(error => error.remove());
    }

    showSuccessMessage(files, input) {
        const wrapper = input.closest('[data-field-wrapper]');
        if (!wrapper) return;

        if (files.length === 0) return;

        const successContainer = document.createElement('div');
        successContainer.className = 'property-image-validation-success mt-2 p-2 bg-green-50 border border-green-200 rounded-md';
        successContainer.innerHTML = `
            <div class="flex items-center text-sm text-green-700">
                <svg class="h-4 w-4 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span>✅ ${files.length} image${files.length > 1 ? 's' : ''} validated successfully</span>
            </div>
        `;

        wrapper.appendChild(successContainer);

        // Auto-remove success message after 3 seconds
        setTimeout(() => {
            if (successContainer.parentNode) {
                successContainer.remove();
            }
        }, 3000);
    }

    showDimensionInfo(width, height, sizeMB, input) {
        const wrapper = input.closest('[data-field-wrapper]');
        if (!wrapper) return;

        const aspectRatio = (width / height).toFixed(2);
        const isOptimalRatio = Math.abs(width / height - this.config.aspectRatio) <= this.config.aspectTolerance;
        const isHighRes = width >= this.config.recommendedWidth && height >= this.config.recommendedHeight;

        const infoContainer = document.createElement('div');
        infoContainer.className = 'property-image-dimension-info mt-2 p-2 bg-blue-50 border border-blue-200 rounded-md';
        infoContainer.innerHTML = `
            <div class="text-xs text-blue-700">
                <div class="flex items-center justify-between">
                    <span>📏 ${width}×${height}px (${aspectRatio}:1) • ${sizeMB.toFixed(1)}MB</span>
                    <div class="flex space-x-1">
                        ${isHighRes ? '<span class="text-green-600">⭐ High Res</span>' : '<span class="text-orange-600">⚠️ Low Res</span>'}
                        ${isOptimalRatio ? '<span class="text-green-600">📐 Optimal Ratio</span>' : '<span class="text-orange-600">📐 Non-optimal Ratio</span>'}
                    </div>
                </div>
            </div>
        `;

        wrapper.appendChild(infoContainer);

        // Auto-remove dimension info after 5 seconds
        setTimeout(() => {
            if (infoContainer.parentNode) {
                infoContainer.remove();
            }
        }, 5000);
    }

    updateValidationMessages() {
        // Update helper text based on current property type
        const propertyTypeSelect = document.querySelector('select[name="property_type_id"]');
        if (!propertyTypeSelect) return;

        const selectedOption = propertyTypeSelect.options[propertyTypeSelect.selectedIndex];
        const propertyTypeId = selectedOption?.value;

        if (propertyTypeId) {
            // This would trigger a re-render of the form elements with new limits
            // The actual implementation depends on how Filament handles reactive updates
            console.log('Property type changed, validation limits may have updated');
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    new PropertyImageValidator();
});

// Also expose globally for manual initialization
window.PropertyImageValidator = PropertyImageValidator;
