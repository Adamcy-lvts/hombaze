<!-- Enhanced Property Details Page -->
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100 relative overflow-hidden">
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg" 
             x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 5000)">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg"
             x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 5000)">
            {{ session('error') }}
        </div>
    @endif

    <!-- Subtle Background Elements -->
    <div class="absolute inset-0 opacity-30">
        <div class="floating-element absolute top-1/4 right-1/4 w-32 h-32 bg-gradient-to-br from-emerald-400/8 to-teal-500/6 rounded-full blur-3xl"></div>
        <div class="floating-element absolute bottom-1/3 left-1/4 w-40 h-40 bg-gradient-to-br from-blue-400/6 to-indigo-500/4 rounded-full blur-3xl"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-30 pt-20 lg:pt-24">
        <!-- Premium Breadcrumb Navigation -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 lg:mb-8">
            <nav class="flex items-center" aria-label="Breadcrumb">
                <div class="flex items-center space-x-3 bg-white/70 backdrop-blur-xl rounded-2xl px-5 py-4 shadow-xl border border-white/40">
                    <!-- Home -->
                    <a href="{{ route('landing') }}" class="group flex items-center text-gray-600 hover:text-emerald-600 transition-all duration-300">
                        <div class="p-2 rounded-xl bg-emerald-50 group-hover:bg-emerald-100 group-hover:scale-105 transition-all duration-300">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm lg:text-base font-medium">Home</span>
                    </a>
                    
                    <!-- Separator -->
                    <div class="flex items-center">
                        <div class="w-8 h-0.5 bg-gradient-to-r from-gray-300 to-gray-400 rounded-full"></div>
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-gray-400 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    
                    <!-- Properties -->
                    <a href="{{ route('properties.search') }}" class="group flex items-center text-gray-600 hover:text-emerald-600 transition-all duration-300">
                        <div class="p-2 rounded-xl bg-blue-50 group-hover:bg-blue-100 group-hover:scale-105 transition-all duration-300">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm lg:text-base font-medium">Properties</span>
                    </a>
                    
                    <!-- Separator -->
                    <div class="flex items-center">
                        <div class="w-8 h-0.5 bg-gradient-to-r from-gray-300 to-gray-400 rounded-full"></div>
                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-gray-400 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    
                    <!-- Location -->
                    <div class="flex items-center">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg">
                            <svg class="w-4 h-4 lg:w-5 lg:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-2">
                            <span class="text-sm lg:text-base font-bold text-gray-900">{{ $property->city->name }}</span>
                            <div class="text-xs text-gray-500">{{ $property->state->name }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Property Status Indicator -->
                <div class="ml-4 flex items-center space-x-2">
                    @if($property->is_featured)
                        <div class="px-3 py-1.5 bg-gradient-to-r from-yellow-400 to-amber-500 text-white text-xs font-bold rounded-full shadow-lg">
                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            Featured
                        </div>
                    @endif
                    @if($property->is_verified)
                        <div class="px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-bold rounded-full shadow-lg">
                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Verified
                        </div>
                    @endif
                </div>
            </nav>
        </div>

        <!-- Property Hero Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 lg:mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Enhanced Property Gallery with Lightbox -->
                @if ($property->getMedia('gallery')->count() > 0 || $property->getMedia('featured')->count() > 0)
                    <div class="lg:col-span-2 property-gallery">
                        <!-- Prepare gallery images as PHP variables -->
                        @php
                            // Get featured image URLs
                            $featuredMedia = $property->getFirstMedia('featured');
                            $featuredImagePreview = null;
                            $featuredImageOriginal = null;
                            if ($featuredMedia) {
                                $featuredImageOriginal = $featuredMedia->getUrl();

                                // Try to get preview conversion for gallery grid
                                try {
                                    $featuredImagePreview = $featuredMedia->getUrl('preview');
                                } catch (Exception $e) {
                                    $featuredImagePreview = $featuredImageOriginal;
                                }
                            }

                            // Prepare media library images with preview and original URLs
                            $mediaLibraryImages = $property->getMedia('gallery')
                                ->map(function ($media) use ($property) {
                                    $originalUrl = $media->getUrl();
                                    $previewUrl = $originalUrl;

                                    // Try to get preview conversion for gallery grid
                                    try {
                                        $previewUrl = $media->getUrl('preview');
                                    } catch (Exception $e) {
                                        // Use original if preview conversion doesn't exist
                                        $previewUrl = $originalUrl;
                                    }

                                    return [
                                        'src' => $previewUrl, // For gallery grid thumbnails
                                        'original' => $originalUrl, // For lightbox
                                        'caption' => $media->getCustomProperty('caption') ?? null,
                                        'alt' => $media->getCustomProperty('alt_text') ?? $media->getCustomProperty('caption') ?? $property->title,
                                    ];
                                })
                                ->toArray();

                            // Add featured image if it exists and is not already in gallery
                            if ($featuredImagePreview && $featuredImageOriginal) {
                                $featuredMedia = $property->getFirstMedia('featured');
                                $featuredImageData = [
                                    'src' => $featuredImagePreview, // For gallery grid thumbnails
                                    'original' => $featuredImageOriginal, // For lightbox
                                    'caption' => $featuredMedia?->getCustomProperty('caption') ?? 'Featured Image',
                                    'alt' => $featuredMedia?->getCustomProperty('alt_text') ?? $featuredMedia?->getCustomProperty('caption') ?? $property->title,
                                ];
                                
                                // Check if featured image is already in gallery (compare original URLs)
                                $existsInGallery = collect($mediaLibraryImages)->contains(function ($image) use ($featuredImageOriginal) {
                                    return $image['original'] === $featuredImageOriginal;
                                });

                                if (!$existsInGallery) {
                                    array_unshift($mediaLibraryImages, $featuredImageData);
                                }
                            }

                            // Fallback if no images
                            if (empty($mediaLibraryImages)) {
                                $fallbackUrl = 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
                                $mediaLibraryImages = [[
                                    'src' => $fallbackUrl,
                                    'original' => $fallbackUrl,
                                    'caption' => null,
                                    'alt' => $property->title,
                                ]];
                            }

                            $allImages = $mediaLibraryImages;
                        @endphp

                        <!-- Initialize Alpine with the properly encoded JSON data -->
                        <div x-data="{
                            showLightbox: false,
                            currentImageIndex: 0,
                            images: {{ Illuminate\Support\Js::from($allImages) }},
                            zoom: 1,
                            panX: 0,
                            panY: 0,
                            isDragging: false,
                            startX: 0,
                            startY: 0,
                            lastX: 0,
                            lastY: 0,
                            currentPreviewIndex: 0,
                            imageLoaded: true,

                            openLightbox(index) {
                                this.currentImageIndex = index;
                                this.showLightbox = true;
                                this.resetZoom();
                                document.body.style.overflow = 'hidden';
                                // Ensure proper focus for keyboard navigation
                                this.$nextTick(() => {
                                    this.updateImageTransform();
                                });
                            },

                            closeLightbox() {
                                this.showLightbox = false;
                                document.body.style.overflow = '';
                                this.resetZoom();
                            },

                            next() {
                                this.resetZoom();
                                this.currentImageIndex = (this.currentImageIndex + 1) % this.images.length;
                                this.imageLoaded = false;
                                this.$nextTick(() => {
                                    this.updateImageTransform();
                                });
                            },

                            prev() {
                                this.resetZoom();
                                this.currentImageIndex = (this.currentImageIndex - 1 + this.images.length) % this.images.length;
                                this.imageLoaded = false;
                                this.$nextTick(() => {
                                    this.updateImageTransform();
                                });
                            },

                            zoomIn() {
                                if (this.zoom < 3) {
                                    this.zoom += 0.5;
                                    this.updateImageTransform();
                                }
                            },

                            zoomOut() {
                                if (this.zoom > 1) {
                                    this.zoom -= 0.5;
                                    this.updateImageTransform();

                                    if (this.zoom === 1) {
                                        this.panX = 0;
                                        this.panY = 0;
                                    }
                                }
                            },

                            resetZoom() {
                                this.zoom = 1;
                                this.panX = 0;
                                this.panY = 0;
                                this.updateImageTransform();
                            },

                            startDrag(e) {
                                if (this.zoom > 1) {
                                    e.preventDefault();
                                    this.isDragging = true;
                                    this.startX = e.clientX || (e.touches ? e.touches[0].clientX : 0);
                                    this.startY = e.clientY || (e.touches ? e.touches[0].clientY : 0);
                                    this.lastX = this.panX;
                                    this.lastY = this.panY;
                                }
                            },

                            doDrag(e) {
                                if (!this.isDragging) return;
                                e.preventDefault();

                                const clientX = e.clientX || (e.touches ? e.touches[0].clientX : this.startX);
                                const clientY = e.clientY || (e.touches ? e.touches[0].clientY : this.startY);

                                const deltaX = clientX - this.startX;
                                const deltaY = clientY - this.startY;

                                const maxPan = 100 * (this.zoom - 1);

                                this.panX = Math.min(maxPan, Math.max(-maxPan, this.lastX + deltaX));
                                this.panY = Math.min(maxPan, Math.max(-maxPan, this.lastY + deltaY));

                                this.updateImageTransform();
                            },

                            endDrag() {
                                this.isDragging = false;
                            },

                            updateImageTransform() {
                                this.$nextTick(() => {
                                    const img = document.querySelector('.lightbox-image');
                                    if (img) {
                                        // Apply transform with proper scaling and translation
                                        img.style.transform = `scale(${this.zoom}) translate(${this.panX / this.zoom}px, ${this.panY / this.zoom}px)`;
                                    }
                                });
                            },

                            handleKeyDown(e) {
                                if (!this.showLightbox) return;

                                switch (e.key) {
                                    case 'Escape':
                                        e.preventDefault();
                                        this.closeLightbox();
                                        break;
                                    case 'ArrowLeft':
                                        e.preventDefault();
                                        this.prev();
                                        break;
                                    case 'ArrowRight':
                                        e.preventDefault();
                                        this.next();
                                        break;
                                    case '+':
                                    case '=':
                                        e.preventDefault();
                                        this.zoomIn();
                                        break;
                                    case '-':
                                        e.preventDefault();
                                        this.zoomOut();
                                        break;
                                    case '0':
                                        e.preventDefault();
                                        this.resetZoom();
                                        break;
                                }
                            }
                        }" @keydown.window="handleKeyDown($event)"
                        @click.away="showLightbox && closeLightbox()">
                            
                            <!-- Main Image Display -->
                            <div class="relative rounded-2xl lg:rounded-3xl overflow-hidden shadow-2xl mb-3 lg:mb-4 group cursor-pointer"
                                 @click="openLightbox(currentPreviewIndex)">
                                <img x-bind:src="images[currentPreviewIndex]?.original || images[currentPreviewIndex]?.src" 
                                     x-bind:alt="images[currentPreviewIndex]?.alt"
                                     class="w-full h-80 lg:h-[450px] xl:h-[500px] object-cover group-hover:scale-105 transition-transform duration-700">
                                
                                <!-- Image Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                
                                <!-- Main Image Navigation -->
                                <button @click.stop="currentPreviewIndex = (currentPreviewIndex - 1 + images.length) % images.length" 
                                        class="absolute left-3 lg:left-4 top-1/2 transform -translate-y-1/2 bg-white/20 backdrop-blur-xl rounded-full p-2 lg:p-3 hover:bg-white/30 transition-all duration-300 group-hover:opacity-100 opacity-0"
                                        x-show="images.length > 1">
                                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                
                                <button @click.stop="currentPreviewIndex = (currentPreviewIndex + 1) % images.length" 
                                        class="absolute right-3 lg:right-4 top-1/2 transform -translate-y-1/2 bg-white/20 backdrop-blur-xl rounded-full p-2 lg:p-3 hover:bg-white/30 transition-all duration-300 group-hover:opacity-100 opacity-0"
                                        x-show="images.length > 1">
                                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>

                                <!-- Click to expand indicator -->
                                <div class="absolute inset-0 bg-emerald-600/0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <div class="bg-white/80 backdrop-blur-sm rounded-full p-3 transform scale-75 group-hover:scale-100 transition-transform duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Status Badges -->
                                <div class="absolute top-3 lg:top-4 left-3 lg:left-4 flex flex-wrap gap-2">
                                    @if($property->is_featured)
                                        <span class="px-2 py-1 lg:px-3 lg:py-1.5 bg-gradient-to-r from-yellow-400 to-amber-500 text-white text-xs font-bold rounded-lg lg:rounded-xl shadow-lg">
                                            <svg class="w-2.5 h-2.5 lg:w-3 lg:h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            Featured
                                        </span>
                                    @endif
                                    @if($property->is_verified)
                                        <span class="px-2 py-1 lg:px-3 lg:py-1.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-bold rounded-lg lg:rounded-xl shadow-lg">
                                            <svg class="w-2.5 h-2.5 lg:w-3 lg:h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Verified
                                    @endif
                                </div>

                                <!-- Listing Type Badge -->
                                <div class="absolute top-3 lg:top-4 right-3 lg:right-4">
                                    <span class="px-3 py-1.5 lg:px-4 lg:py-2 bg-white/95 backdrop-blur-xl text-gray-700 text-xs lg:text-sm font-semibold rounded-lg lg:rounded-xl border border-gray-300/60 capitalize shadow-md">
                                        {{ $property->listing_type }}
                                    </span>
                                </div>
                            </div>

                            <!-- Thumbnail Gallery Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 lg:gap-4">
                                <template x-for="(image, index) in images" :key="index">
                                    <div class="group relative rounded-lg overflow-hidden shadow-md cursor-pointer transition-transform duration-300 hover:-translate-y-1 hover:shadow-lg h-24 lg:h-32"
                                         @click="currentPreviewIndex = index; openLightbox(index)"
                                         :class="{ 'ring-2 ring-emerald-500': currentPreviewIndex === index }">
                                        <img :src="image.src"
                                             :alt="image.alt"
                                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                                        <div x-show="image.caption"
                                             class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                                            <p x-text="image.caption" class="text-white text-sm"></p>
                                        </div>

                                        <!-- Click indicator -->
                                        <div class="absolute inset-0 bg-emerald-600/0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                            <div class="bg-white/80 backdrop-blur-sm rounded-full p-3 transform scale-75 group-hover:scale-100 transition-transform duration-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Lightbox Modal -->
                            <div x-show="showLightbox" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="fixed inset-0 z-50 bg-black/95 backdrop-blur-sm"
                                 style="display: none; width: 100vw; height: 100vh; top: 0; left: 0;"
                                 @click.self="closeLightbox()">
                                
                                <!-- Image container centered in viewport -->
                                <div class="h-full w-full flex items-center justify-center p-8">
                                    <div class="relative flex items-center justify-center w-full h-full max-w-7xl"
                                         @mousedown="startDrag($event)" 
                                         @mousemove="doDrag($event)"
                                         @mouseup="endDrag()" 
                                         @mouseleave="endDrag()" 
                                         @touchstart="startDrag($event)"
                                         @touchmove="doDrag($event)" 
                                         @touchend="endDrag()">
                                        
                                        <!-- Loading indicator -->
                                        <div class="absolute inset-0 flex items-center justify-center" x-show="!imageLoaded">
                                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
                                        </div>

                                        <!-- Current Image -->
                                        <img x-bind:src="images[currentImageIndex]?.original || images[currentImageIndex]?.src"
                                             x-bind:alt="images[currentImageIndex]?.alt"
                                             class="max-h-full max-w-full object-contain select-none lightbox-image transition-transform duration-200 mx-auto shadow-2xl"
                                             style="transform-origin: center; touch-action: none;"
                                             @dblclick="zoom === 1 ? zoomIn() : resetZoom()"
                                             @load="updateImageTransform(); imageLoaded = true"
                                             @loadstart="imageLoaded = false">
                                    </div>
                                </div>

                                <!-- Controls -->
                                <div class="fixed top-20 right-8 z-[60] flex items-center space-x-4">
                                    <!-- Zoom controls -->
                                    <div class="bg-black/50 backdrop-blur-sm rounded-lg flex items-center p-1 shadow-lg">
                                        <button @click="zoomOut()"
                                                class="text-white p-2 hover:bg-white/20 rounded-l-lg transition-colors"
                                                x-bind:disabled="zoom <= 1"
                                                x-bind:class="{ 'opacity-50 cursor-not-allowed': zoom <= 1 }">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                            </svg>
                                        </button>

                                        <div class="px-3 text-white text-sm font-medium" x-text="`${zoom.toFixed(1)}x`"></div>

                                        <button @click="zoomIn()"
                                                class="text-white p-2 hover:bg-white/20 rounded-r-lg transition-colors"
                                                x-bind:disabled="zoom >= 3"
                                                x-bind:class="{ 'opacity-50 cursor-not-allowed': zoom >= 3 }">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Close button -->
                                    <button @click="closeLightbox()"
                                            class="text-white bg-black/50 backdrop-blur-sm p-3 rounded-lg hover:bg-black/70 transition-colors shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        
                                    </button>
                                </div>

                                <!-- Navigation buttons -->
                                <button @click.stop="prev()"
                                        class="fixed left-8 top-1/2 transform -translate-y-1/2 bg-black/50 backdrop-blur-sm hover:bg-black/70 p-3 rounded-full text-white transition-all duration-300 z-[60] shadow-lg"
                                        x-show="images.length > 1"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 -translate-x-4"
                                        x-transition:enter-end="opacity-100 translate-x-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>

                                <button @click.stop="next()"
                                        class="fixed right-8 top-1/2 transform -translate-y-1/2 bg-black/50 backdrop-blur-sm hover:bg-black/70 p-3 rounded-full text-white transition-all duration-300 z-[60] shadow-lg"
                                        x-show="images.length > 1"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-x-4"
                                        x-transition:enter-end="opacity-100 translate-x-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>

                                <!-- Image Caption -->
                                <div x-show="images[currentImageIndex]?.caption"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-4"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-black/80 backdrop-blur-sm text-white text-sm rounded-lg px-4 py-3 z-[60] shadow-lg max-w-2xl text-center">
                                    <p x-text="images[currentImageIndex]?.caption" class="font-medium"></p>
                                </div>

                                <!-- Help text -->
                                <div class="fixed bottom-8 left-1/2 transform -translate-x-1/2 bg-black/60 backdrop-blur-sm text-white text-sm rounded-lg px-4 py-2 opacity-80 pointer-events-none z-[60] shadow-lg">
                                    <div class="flex items-center space-x-4 text-xs">
                                        <span>Zoom: <kbd class="font-mono bg-white/20 px-1 rounded">+/-</kbd></span>
                                        <span>Navigate: <kbd class="font-mono bg-white/20 px-1 rounded">←→</kbd></span>
                                        <span>Close: <kbd class="font-mono bg-white/20 px-1 rounded">ESC</kbd></span>
                                        <span>Double-click to zoom</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Fallback for properties without images -->
                    <div class="lg:col-span-2">
                        <div class="relative rounded-2xl lg:rounded-3xl overflow-hidden shadow-2xl mb-3 lg:mb-4 bg-gray-200 h-80 lg:h-[450px] xl:h-[500px] flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-gray-500">No images available for this property</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Property Info Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Price Card -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 p-4 lg:p-6 mb-4 lg:mb-6 sticky top-20 lg:top-24">
                        <!-- Price -->
                        <div class="mb-4 lg:mb-6">
                            <h2 class="text-2xl lg:text-3xl font-black bg-gradient-to-r from-emerald-500 to-teal-600 bg-clip-text text-transparent mb-2">
                                {{ $property->formatted_price }}
                                @if($property->price_period && $property->price_period !== 'total')
                                    <span class="text-base lg:text-lg font-normal text-gray-500">
                                        /{{ str_replace('per_', '', $property->price_period) }}
                                    </span>
                                @endif
                            </h2>
                            @if($property->service_charge > 0)
                                <p class="text-xs lg:text-sm text-gray-600">
                                    Service Charge: <span class="font-semibold">₦{{ number_format($property->service_charge) }}</span>
                                </p>
                            @endif
                            @if($property->agency_fee)
                                <p class="text-xs lg:text-sm text-gray-600">
                                    Agency Fee: <span class="font-semibold">₦{{ number_format($property->agency_fee) }}</span>
                                </p>
                            @endif
                        </div>

                        {{-- Address --}}
                        {{-- <div class="mb-4 lg:mb-6 p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="flex items-start text-gray-600">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5 mr-2 mt-0.5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 616 0z"></path>
                                </svg>
                                <span class="font-medium text-xs lg:text-sm leading-relaxed">
                                    {{ $property->address }}, {{ $property->area->name ?? '' }} {{ $property->city->name }}, {{ $property->city->state->name }}
                                </span>
                            </div>
                        </div> --}}

                        <!-- Quick Stats -->
                        <div class="grid grid-cols-3 gap-3 lg:gap-4 mb-4 lg:mb-6">
                            <div class="text-center p-3 lg:p-4 bg-gray-50 rounded-lg lg:rounded-xl border border-gray-200">
                                <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ $property->bedrooms }}</div>
                                <div class="text-xs text-gray-600 uppercase tracking-wide">Bedrooms</div>
                            </div>
                            <div class="text-center p-3 lg:p-4 bg-gray-50 rounded-lg lg:rounded-xl border border-gray-200">
                                <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ $property->toilets }}</div>
                                <div class="text-xs text-gray-600 uppercase tracking-wide">Toilets</div>
                            </div>
                            <div class="text-center p-3 lg:p-4 bg-gray-50 rounded-lg lg:rounded-xl border border-gray-200">
                                <div class="text-lg lg:text-2xl font-bold text-gray-900">{{ $property->parking_spaces ?? 0 }}</div>
                                <div class="text-xs text-gray-600 uppercase tracking-wide">Parking</div>
                            </div>
                        </div>


                        <!-- Contact Buttons -->
                        <div class="space-y-2 lg:space-y-3">
                            @if($this->getAgentPhoneNumber())
                                <a href="tel:{{ $this->getAgentPhoneNumber() }}" class="w-full bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-700 hover:via-emerald-600 hover:to-teal-600 text-white font-bold py-3 lg:py-4 px-4 lg:px-6 rounded-xl lg:rounded-2xl transition-all duration-500 transform hover:scale-105 shadow-lg hover:shadow-emerald-500/40 flex items-center justify-center text-sm lg:text-base">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    Call Agent
                                </a>
                            @else
                                <button class="w-full bg-gray-400 text-white font-bold py-3 lg:py-4 px-4 lg:px-6 rounded-xl lg:rounded-2xl flex items-center justify-center text-sm lg:text-base cursor-not-allowed opacity-60">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    No Phone Available
                                </button>
                            @endif
                            
                            <button wire:click="toggleContactForm" class="w-full bg-white/95 backdrop-blur-xl text-gray-700 font-semibold py-3 lg:py-4 px-4 lg:px-6 rounded-xl lg:rounded-2xl border-2 border-gray-300/60 hover:bg-white hover:border-gray-400/60 transition-all duration-500 transform hover:scale-105 shadow-lg flex items-center justify-center text-sm lg:text-base">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Send Message
                            </button>

                            <button wire:click="scheduleViewing" class="w-full bg-blue-50 text-blue-700 font-semibold py-3 lg:py-4 px-4 lg:px-6 rounded-xl lg:rounded-2xl border border-blue-200 hover:bg-blue-100 transition-all duration-300 flex items-center justify-center text-sm lg:text-base">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Schedule Viewing
                            </button>
                        </div>

                        <!-- Property Stats -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex justify-between items-center text-xs text-gray-500 space-x-4">
                                @if($property->view_count)
                                <div class="flex items-center space-x-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <span>{{ number_format($property->view_count) }}</span>
                                </div>
                                @endif
                                <div class="flex items-center space-x-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ $property->created_at->diffForHumans() }}</span>
                                </div>
                                @if($property->updated_at != $property->created_at)
                                <div class="flex items-center space-x-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span>{{ $property->updated_at->diffForHumans() }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Listed By -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Listed By</h3>

                            @if($property->agency && $property->agent)
                                <!-- Agency with Agent -->
                                <div class="space-y-4">
                                    <!-- Agency Info (smaller) -->
                                    <div class="flex items-center space-x-3 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-xs shadow-md">
                                            {{ substr($property->agency->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-medium text-gray-700 text-xs truncate">{{ $property->agency->name }}</h4>
                                            <p class="text-xs text-gray-500">Licensed Agency</p>
                                        </div>
                                    </div>

                                    <!-- Agent Info (prominent) -->
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full mx-auto mb-3 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                            {{ substr($property->agent->full_name ?? 'Agent', 0, 1) }}
                                        </div>
                                        <h4 class="font-bold text-gray-900 text-lg mb-1">{{ $property->agent->full_name ?? 'Licensed Agent' }}</h4>
                                        <p class="text-sm text-gray-600 mb-3">Senior Property Consultant</p>

                                        <!-- Agent Stats -->
                                        <div class="grid grid-cols-3 gap-2 mb-3">
                                            <div class="text-center p-2 bg-gray-50 rounded-lg">
                                                <div class="text-sm font-bold text-emerald-600">4.0</div>
                                                <div class="text-xs text-gray-600">Rating</div>
                                            </div>
                                            <div class="text-center p-2 bg-gray-50 rounded-lg">
                                                <div class="text-sm font-bold text-emerald-600">25+</div>
                                                <div class="text-xs text-gray-600">Listings</div>
                                            </div>
                                            <div class="text-center p-2 bg-gray-50 rounded-lg">
                                                <div class="text-sm font-bold text-emerald-600">3+</div>
                                                <div class="text-xs text-gray-600">Years</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($property->agent)
                                <!-- Independent Agent -->
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full mx-auto mb-3 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                        {{ substr($property->agent->full_name ?? 'Agent', 0, 1) }}
                                    </div>
                                    <h4 class="font-bold text-gray-900 text-lg mb-1">{{ $property->agent->full_name ?? 'Licensed Agent' }}</h4>
                                    <p class="text-sm text-gray-600 mb-4">Licensed Real Estate Professional</p>

                                    <!-- Agent Stats -->
                                    <div class="grid grid-cols-3 gap-2 mb-3">
                                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                                            <div class="text-sm font-bold text-emerald-600">4.7</div>
                                            <div class="text-xs text-gray-600">Rating</div>
                                        </div>
                                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                                            <div class="text-sm font-bold text-emerald-600">45+</div>
                                            <div class="text-xs text-gray-600">Listings</div>
                                        </div>
                                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                                            <div class="text-sm font-bold text-emerald-600">3+</div>
                                            <div class="text-xs text-gray-600">Years</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($property->owner)
                                <!-- Property Owner -->
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full mx-auto mb-3 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                        @if($property->owner->user)
                                            {{ substr($property->owner->user->name, 0, 1) }}
                                        @else
                                            {{ substr($property->owner->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <h4 class="font-bold text-gray-900 text-lg mb-1">
                                        @if($property->owner->user)
                                            {{ $property->owner->user->name }}
                                        @else
                                            {{ $property->owner->name }}
                                        @endif
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-4">Property Owner</p>
                                </div>
                            @else
                                <!-- Fallback for properties without any contact -->
                                @if($property->creator)
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-gray-500 to-gray-700 rounded-full mx-auto mb-3 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                        {{ substr($property->creator->name, 0, 1) }}
                                    </div>
                                    <h4 class="font-bold text-gray-900 text-lg mb-1">{{ $property->creator->name }}</h4>
                                    <p class="text-sm text-gray-600 mb-4">Property Lister</p>
                                </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Details Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 lg:mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6 lg:space-y-8">

                    <!-- Property Overview -->
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 p-6 lg:p-8">
                        <h2 class="text-xl lg:text-2xl font-bold text-gray-900 mb-4 lg:mb-6">Property Overview</h2>
                        
                        <!-- Title and Location -->
                        <div class="mb-4 lg:mb-6">
                            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-3">{{ $property->title }}</h1>
                        </div>

                        <!-- Property Description -->
                        @if($property->description)
                        <div class="mb-6 lg:mb-8">
                            <div class="prose prose-gray max-w-none">
                                <p class="text-sm lg:text-base text-gray-700 leading-relaxed">{{ $property->description }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Property Stats Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 lg:gap-4 mb-6">
                            <div class="text-center p-3 lg:p-4 bg-gray-50 rounded-lg lg:rounded-xl border border-gray-200">
                                <div class="text-base lg:text-xl font-bold text-gray-900">{{ $property->propertyType->name ?? 'N/A' }}</div>
                                <div class="text-xs lg:text-sm text-gray-600">Property Type</div>
                            </div>
                            @if($property->propertySubtype)
                            <div class="text-center p-3 lg:p-4 bg-gray-50 rounded-lg lg:rounded-xl border border-gray-200">
                                <div class="text-base lg:text-xl font-bold text-gray-900">{{ $property->propertySubtype->name }}</div>
                                <div class="text-xs lg:text-sm text-gray-600">Subtype</div>
                            </div>
                            @endif
                            @if($property->compound_type)
                            <div class="text-center p-3 lg:p-4 bg-emerald-50 rounded-lg lg:rounded-xl border border-emerald-200">
                                <div class="text-base lg:text-xl font-bold text-emerald-900">{{ $property->getCompoundTypeLabel() }}</div>
                                <div class="text-xs lg:text-sm text-emerald-600">Compound Type</div>
                            </div>
                            @endif
                            @if($property->parking_spaces)
                            <div class="text-center p-3 lg:p-4 bg-gray-50 rounded-lg lg:rounded-xl border border-gray-200">
                                <div class="text-base lg:text-xl font-bold text-gray-900">{{ $property->parking_spaces }}</div>
                                <div class="text-xs lg:text-sm text-gray-600">Parking</div>
                            </div>
                            @endif
                            @if($property->year_built)
                            <div class="text-center p-3 lg:p-4 bg-gray-50 rounded-lg lg:rounded-xl border border-gray-200">
                                <div class="text-base lg:text-xl font-bold text-gray-900">{{ $property->year_built }}</div>
                                <div class="text-xs lg:text-sm text-gray-600">Year Built</div>
                            </div>
                            @endif
                        </div>

                    </div>

                    <!-- Area Information - Only show if there's actual neighborhood data -->
                    @if($property->area && (
                        $property->area->education_facilities ||
                        $property->area->healthcare_facilities ||
                        $property->area->transport_facilities ||
                        $property->area->electricity_supply ||
                        $property->area->water_supply ||
                        $property->area->shopping_facilities ||
                        $property->area->security_rating
                    ))
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 p-6 lg:p-8">
                        <h2 class="text-xl lg:text-2xl font-bold text-gray-900 mb-4 lg:mb-6">Neighborhood Overview</h2>
                        
                        <!-- Area Categories Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                            <!-- Education Card - Only show if data exists -->
                            @if($property->area && $property->area->education_facilities)
                            <div class="bg-white rounded-xl border border-blue-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="p-4">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">Education</h3>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach($property->area->education_facilities as $facility)
                                            <div class="flex justify-between items-center p-2 bg-blue-50 rounded-lg">
                                                <span class="text-sm text-gray-700">{{ $facility['name'] }}</span>
                                                <span class="text-xs font-semibold text-blue-600">{{ $facility['distance'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Healthcare Card - Only show if data exists -->
                            @if($property->area && $property->area->healthcare_facilities)
                            <div class="bg-white rounded-xl border border-red-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="p-4">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">Healthcare</h3>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach($property->area->healthcare_facilities as $facility)
                                            <div class="flex justify-between items-center p-2 bg-red-50 rounded-lg">
                                                <span class="text-sm text-gray-700">{{ $facility['name'] }}</span>
                                                <span class="text-xs font-semibold text-red-600">{{ $facility['distance'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Transport Card - Only show if data exists -->
                            @if($property->area && $property->area->transport_facilities)
                            <div class="bg-white rounded-xl border border-green-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="p-4">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">Transport</h3>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach($property->area->transport_facilities as $facility)
                                            <div class="flex justify-between items-center p-2 bg-green-50 rounded-lg">
                                                <span class="text-sm text-gray-700">{{ $facility['name'] }}</span>
                                                <span class="text-xs font-semibold text-green-600">{{ $facility['distance'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Electricity Card - Only show if data exists -->
                            @if($property->area && $property->area->electricity_supply)
                            <div class="bg-white rounded-xl border border-yellow-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="p-4">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">Electricity</h3>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="p-2 bg-yellow-50 rounded-lg">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-sm font-medium text-gray-700">Quality</span>
                                                <span class="text-xs font-semibold text-yellow-600">{{ $property->area->electricity_supply['quality'] ?? 'Good' }}</span>
                                            </div>
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-sm text-gray-600">Availability</span>
                                                <span class="text-xs text-yellow-600">{{ $property->area->electricity_supply['availability'] ?? '18-20 hrs/day' }}</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-600">Backup</span>
                                                <span class="text-xs text-yellow-600">{{ $property->area->electricity_supply['backup'] ?? 'Available' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Water Card - Only show if data exists -->
                            @if($property->area && $property->area->water_supply)
                            <div class="bg-white rounded-xl border border-cyan-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="p-4">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">Water Supply</h3>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="p-2 bg-cyan-50 rounded-lg">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-sm font-medium text-gray-700">Quality</span>
                                                <span class="text-xs font-semibold text-cyan-600">{{ $property->area->water_supply['quality'] ?? 'Good' }}</span>
                                            </div>
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-sm text-gray-600">Availability</span>
                                                <span class="text-xs text-cyan-600">{{ $property->area->water_supply['availability'] ?? 'Daily' }}</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm text-gray-600">Source</span>
                                                <span class="text-xs text-cyan-600">{{ $property->area->water_supply['source'] ?? 'Municipal' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Shopping Card - Only show if data exists -->
                            @if($property->area && $property->area->shopping_facilities)
                            <div class="bg-white rounded-xl border border-purple-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="p-4">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">Shopping</h3>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach($property->area->shopping_facilities as $facility)
                                            <div class="flex justify-between items-center p-2 bg-purple-50 rounded-lg">
                                                <span class="text-sm text-gray-700">{{ $facility['name'] }}</span>
                                                <span class="text-xs font-semibold text-purple-600">{{ $facility['distance'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Security Card - Only show if data exists -->
                            @if($property->area && ($property->area->security_rating || $property->area->security_features))
                            <div class="bg-white rounded-xl border border-emerald-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="p-4">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900">Security</h3>
                                            @if($property->area->security_rating)
                                            <div class="text-right">
                                                <div class="text-xl font-bold text-emerald-600">{{ number_format($property->area->security_rating, 1) }}/10</div>
                                                <div class="text-xs text-gray-600">{{ $property->area->security_rating_display ?? 'Good' }}</div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        @if($property->area->security_stars)
                                        <div class="flex items-center space-x-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $property->area->security_stars ? 'text-emerald-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        @endif
                                        @if($property->area->security_features)
                                        <div class="p-2 bg-emerald-50 rounded-lg">
                                            <div class="text-xs text-gray-600">
                                                {{ implode(' • ', $property->area->security_features) }}
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif


                </div>

                <!-- Right Sidebar -->
                <div class="lg:col-span-1 space-y-4 lg:space-y-6">

                    <!-- Features & Amenities -->
                    @if($property->features && $property->features->count() > 0)
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 p-4 lg:p-6">
                        <h3 class="text-base lg:text-lg font-semibold text-gray-900 mb-3 lg:mb-4">Features & Amenities</h3>
                        <div class="space-y-2">
                            @foreach($property->features as $feature)
                                <div class="flex items-center space-x-2 py-1">
                                    <div class="flex-shrink-0">
                                        <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <span class="text-xs lg:text-sm text-gray-700 font-medium">{{ $feature->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- Personalized Recommendations -->
        @auth
        @if($recommendedProperties && $recommendedProperties->count() > 0)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 lg:pb-12">
            <div class="mb-6 lg:mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="p-2 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl lg:text-3xl font-bold text-gray-900">Recommended for You</h2>
                </div>
                <p class="text-gray-600 text-sm lg:text-base">Properties matched to your preferences and search history</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                @foreach($recommendedProperties as $recommendedProperty)
                    <a href="{{ route('property.show', $recommendedProperty->slug ?? $recommendedProperty->id) }}"
                       class="group bg-white/95 backdrop-blur-sm rounded-xl lg:rounded-2xl shadow-lg border border-emerald-200/60 overflow-hidden hover:bg-white hover:shadow-xl transition-all duration-500 hover:scale-105 relative">
                        <!-- Recommendation Score Badge -->
                        @if($recommendedProperty->recommendation_score && $recommendedProperty->recommendation_score > 0.7)
                        <div class="absolute top-3 right-3 z-10 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-xs font-semibold px-2 py-1 rounded-full shadow-lg">
                            {{ round($recommendedProperty->recommendation_score * 100) }}% Match
                        </div>
                        @endif

                        <div class="relative h-40 lg:h-48 overflow-hidden">
                            <img src="{{ $recommendedProperty->getFeaturedImageUrl('preview') }}"
                                 alt="{{ $recommendedProperty->title }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                 onerror="this.src='/images/property-placeholder.svg'">
                        </div>
                        <div class="p-3 lg:p-4">
                            <h4 class="font-semibold text-gray-900 mb-2 line-clamp-2 text-sm lg:text-base">{{ $recommendedProperty->title }}</h4>
                            <p class="text-xs lg:text-sm text-gray-600 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $recommendedProperty->area->name ?? '' }}, {{ $recommendedProperty->area->city->name ?? '' }}
                            </p>
                            <p class="text-base lg:text-lg font-bold text-emerald-600">{{ $recommendedProperty->formatted_price }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
        @endauth

        <!-- Related Properties -->
        @if($relatedProperties->count() > 0)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 lg:pb-12">
            <div class="mb-6 lg:mb-8">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">Similar Properties</h2>
                <p class="text-gray-600 text-sm lg:text-base">Other properties in {{ $property->city->name }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                @foreach($relatedProperties as $relatedProperty)
                    <a href="{{ route('property.show', $relatedProperty->slug ?? $relatedProperty->id) }}" 
                       class="group bg-white/95 backdrop-blur-sm rounded-xl lg:rounded-2xl shadow-lg border border-gray-300/60 overflow-hidden hover:bg-white hover:shadow-xl transition-all duration-500 hover:scale-105">
                        <div class="relative h-40 lg:h-48 overflow-hidden">
                            <img src="{{ $relatedProperty->getFeaturedImageUrl('preview') }}" 
                                 alt="{{ $relatedProperty->title }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                 onerror="this.src='/images/property-placeholder.svg'">
                        </div>
                        <div class="p-3 lg:p-4">
                            <h4 class="font-semibold text-gray-900 mb-2 line-clamp-2 text-sm lg:text-base">{{ $relatedProperty->title }}</h4>
                            <p class="text-xs lg:text-sm text-gray-600 mb-2">{{ $relatedProperty->city->name }}</p>
                            <p class="text-base lg:text-lg font-bold text-emerald-600">{{ $relatedProperty->formatted_price }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Contact Form Modal -->
    @if($showContactForm)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showContactForm') }" x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="toggleContactForm"></div>
                
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Contact Agent</h3>
                            <button wire:click="toggleContactForm" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <form wire:submit.prevent="submitInquiry" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input type="text" wire:model="inquiryName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                @error('inquiryName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" wire:model="inquiryEmail" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                @error('inquiryEmail') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="tel" wire:model="inquiryPhone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                @error('inquiryPhone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                                <textarea wire:model="inquiryMessage" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                                @error('inquiryMessage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="flex space-x-3 pt-4">
                                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                                    Send Inquiry
                                </button>
                                <button type="button" wire:click="toggleContactForm" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
// Add floating animation styles
document.addEventListener('DOMContentLoaded', function() {
    // Animate property cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0) scale(1)';
            }
        });
    }, observerOptions);

    // Initially hide cards and animate them in
    document.querySelectorAll('.bg-white\\/95').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px) scale(0.95)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
});
</script>

@push('styles')
<style>
    /* Floating elements animation */
    .floating-element {
        animation: float 6s ease-in-out infinite;
    }
    
    .floating-element:nth-child(2) {
        animation-delay: -2s;
    }
    
    .floating-element:nth-child(3) {
        animation-delay: -4s;
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0px) translateX(0px) rotate(0deg);
        }
        33% {
            transform: translateY(-20px) translateX(10px) rotate(2deg);
        }
        66% {
            transform: translateY(-10px) translateX(-5px) rotate(-1deg);
        }
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Lightbox specific styles */
    .lightbox-image {
        transition: transform 0.3s ease;
        cursor: move;
    }

    .lightbox-image:active {
        cursor: grabbing;
    }
</style>
@endpush