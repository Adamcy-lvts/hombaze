<div x-data="{
    isDragOver: @entangle('isDragOver'),
    showMetadata: false,
    aspectRatioWarning: null
}"
     x-on:show-aspect-ratio-warning.window="aspectRatioWarning = $event.detail.message; setTimeout(() => aspectRatioWarning = null, 5000)"
     class="w-full">

    <!-- Aspect Ratio Warning -->
    <div x-show="aspectRatioWarning"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
        <p x-text="aspectRatioWarning" class="text-amber-800 text-sm"></p>
    </div>

    <!-- Upload Area -->
    <div class="space-y-4">
        @if ($previewUrl || $existingImageUrl)
            <!-- Image Preview -->
            <div class="relative group">
                <div class="relative overflow-hidden rounded-lg border-2 border-gray-200 bg-gray-50">
                    <img src="{{ $previewUrl ?: $existingImageUrl }}"
                         alt="Featured image preview"
                         class="w-full h-48 object-cover">

                    <!-- Overlay with actions -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <div class="flex space-x-2">
                            <button type="button"
                                    wire:click="removeImage"
                                    class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors text-sm">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Remove
                            </button>
                            <button type="button"
                                    x-on:click="showMetadata = !showMetadata"
                                    class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Image Metadata Form -->
                <div x-show="showMetadata"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform translate-y-2"
                     class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg space-y-4">

                    <div>
                        <label for="caption" class="block text-sm font-medium text-gray-700 mb-1">
                            Image Caption
                        </label>
                        <input type="text"
                               id="caption"
                               wire:model.live="caption"
                               placeholder="Describe this image (e.g., 'Beautiful living room with modern furniture')"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="altText" class="block text-sm font-medium text-gray-700 mb-1">
                            Alt Text (for accessibility)
                        </label>
                        <input type="text"
                               id="altText"
                               wire:model.live="altText"
                               placeholder="Brief description for screen readers"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Help visually impaired users understand this image</p>
                    </div>
                </div>
            </div>
        @else
            <!-- Upload Drop Zone -->
            <div class="relative">
                <label for="{{ $componentId }}" class="block cursor-pointer">
                    <div class="border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200 hover:border-blue-400 hover:bg-blue-50"
                         :class="isDragOver ? 'border-blue-400 bg-blue-50' : 'border-gray-300'"
                         x-on:dragover.prevent="$wire.handleDragOver()"
                         x-on:dragleave.prevent="$wire.handleDragLeave()"
                         x-on:drop.prevent="
                             $wire.handleDragLeave();
                             let files = $event.dataTransfer.files;
                             if (files.length > 0) {
                                 $wire.upload('featuredImage', files[0])
                             }
                         ">

                        <!-- Upload Icon -->
                        <div class="mx-auto w-16 h-16 text-gray-400 mb-4">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>

                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            Upload Featured Image
                        </h3>

                        <p class="text-gray-600 mb-4">
                            Drag and drop your image here, or click to browse
                        </p>

                        <!-- Upload Requirements -->
                        <div class="text-sm text-gray-500 space-y-1 mb-6">
                            <p>📐 Minimum: {{ getOptimalImageResolution()['min_width'] }}×{{ getOptimalImageResolution()['min_height'] }}px</p>
                            <p>🎯 Recommended: {{ getOptimalImageResolution()['recommended_width'] }}×{{ getOptimalImageResolution()['recommended_height'] }}px</p>
                            <p>📁 Max file size: {{ round(getOptimalImageResolution()['max_file_size'] / 1024 / 1024) }}MB</p>
                            <p>🎨 Formats: JPEG, PNG, WebP</p>
                        </div>

                        <!-- File Input Button -->
                        <div class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Choose Image
                        </div>
                    </div>
                </label>

                <input type="file"
                       id="{{ $componentId }}"
                       name="featuredImage"
                       wire:model="featuredImage"
                       accept="image/jpeg,image/jpg,image/png,image/webp"
                       class="hidden">

                <!-- Upload Progress -->
                <div wire:loading wire:target="featuredImage" class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center rounded-lg">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                        <p class="text-sm text-gray-600">Uploading and validating image...</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Validation Error -->
        @if ($validationError)
            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-800 text-sm">{{ $validationError }}</p>
            </div>
        @endif

        @error('featuredImage')
            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-800 text-sm">{{ $message }}</p>
            </div>
        @enderror

        <!-- Helper Text -->
        <div class="text-xs text-gray-500 space-y-1">
            <p>{{ getOptimalImageResolution()['quality_note'] }}</p>
            @if (!$required)
                <p class="text-blue-600">📝 This image is optional</p>
            @else
                <p class="text-orange-600">⚠️ This image is required</p>
            @endif
        </div>
    </div>
</div>