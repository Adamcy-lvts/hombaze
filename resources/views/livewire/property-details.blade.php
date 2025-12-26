<!-- Enhanced Property Details Page -->
<div class="min-h-screen bg-gray-50 font-sans text-gray-900 relative selection:bg-emerald-100 selection:text-emerald-900 animate-in fade-in duration-500">
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="fixed top-4 left-4 right-4 sm:left-auto sm:right-4 z-50 bg-emerald-600 text-white px-6 py-4 rounded-2xl shadow-xl flex items-center gap-3 animate-in slide-in-from-top-4 duration-300"
            x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 left-4 right-4 sm:left-auto sm:right-4 z-50 bg-red-500 text-white px-6 py-4 rounded-2xl shadow-xl flex items-center gap-3 animate-in slide-in-from-top-4 duration-300" x-data="{ show: true }"
            x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Content -->
    <!-- Main Content -->
    <div class="relative z-30 pt-4 lg:pt-8 pb-20">
        <!-- Premium Navigation Header -->
        <div class="sticky top-0 z-40 bg-gray-50/95 backdrop-blur-xl border-b border-gray-100 mb-8 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <nav aria-label="Breadcrumb">
                        <ol class="flex items-center gap-2 text-sm text-gray-500 overflow-x-auto whitespace-nowrap scrollbar-hide font-medium">
                            <li>
                                <a href="{{ route('landing') }}" wire:navigate class="flex items-center gap-2 hover:text-emerald-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                </a>
                            </li>
                            <li class="text-gray-300">/</li>
                            <li>
                                <a href="{{ route('properties.search') }}" wire:navigate class="hover:text-emerald-600 transition-colors">Properties</a>
                            </li>
                            <li class="text-gray-300">/</li>
                            <li>
                                <span class="text-gray-900 truncate max-w-[200px] block">
                                    {{ $property->title }}
                                </span>
                            </li>
                        </ol>
                    </nav>

                    <div class="flex items-center gap-3">
                         <button class="p-2 text-gray-400 hover:text-red-500 transition-colors rounded-full hover:bg-red-50">
                            <x-heroicon-o-heart class="w-5 h-5" />
                        </button>
                        <button class="p-2 text-gray-400 hover:text-blue-500 transition-colors rounded-full hover:bg-blue-50">
                            <x-heroicon-o-share class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Header -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                <div class="space-y-4 max-w-4xl">
                    <div class="flex flex-wrap items-center gap-3 animate-in fade-in slide-in-from-bottom-2 duration-500 delay-100">
                        @if ($property->is_featured)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold uppercase tracking-wider border border-amber-100">
                                <x-heroicon-s-star class="w-3.5 h-3.5" />
                                Featured
                            </span>
                        @endif
                        @if ($property->is_verified)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider border border-blue-100">
                                <x-heroicon-s-check-badge class="w-3.5 h-3.5" />
                                Verified
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-bold uppercase tracking-wider border border-gray-200">
                            {{ $property->listing_type }}
                        </span>
                    </div>
                    
                    <h1 class="text-3xl lg:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight animate-in fade-in slide-in-from-bottom-2 duration-500 delay-200">{{ $property->title }}</h1>
                    
                    <div class="flex items-center text-gray-500 text-lg animate-in fade-in slide-in-from-bottom-2 duration-500 delay-300">
                        <x-heroicon-o-map-pin class="w-5 h-5 mr-2 text-emerald-500" />
                        {{ ($property->area?->name ?? 'Unknown Area') . ', ' . ($property->city?->name ?? 'Unknown City') }}
                    </div>
                </div>

                <div class="text-left lg:text-right mt-2 animate-in fade-in slide-in-from-bottom-2 duration-500 delay-300">
                    <div class="text-3xl lg:text-5xl font-black text-emerald-600 tracking-tight">
                        {{ $property->formatted_price }}
                    </div>
                    @if ($property->price_period && $property->price_period !== 'total')
                        <p class="text-gray-500 font-medium mt-1">per {{ str_replace('per_', '', $property->price_period) }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Property Hero Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Enhanced Property Gallery -->
                <div class="lg:col-span-2">
                    @if ($property->getMedia('gallery')->count() > 0 || $property->getMedia('featured')->count() > 0)
                        @php
                            // Image processing logic (same as before but cleaner)
                            $images = [];
                            // Add featured image
                            if ($featured = $property->getFirstMedia('featured')) {
                                $images[] = [
                                    'src' => $featured->getUrl(),
                                    'preview' => $featured->hasGeneratedConversion('preview') ? $featured->getUrl('preview') : $featured->getUrl(),
                                    'caption' => $featured->getCustomProperty('caption'),
                                    'alt' => $featured->getCustomProperty('alt_text') ?? $property->title
                                ];
                            }
                            // Add gallery images
                            foreach($property->getMedia('gallery') as $media) {
                                // Avoid duplicates if featured image is also in gallery
                                if ($featured && $media->getUrl() === $featured->getUrl()) continue;
                                
                                $images[] = [
                                    'src' => $media->getUrl(),
                                    'preview' => $media->hasGeneratedConversion('preview') ? $media->getUrl('preview') : $media->getUrl(),
                                    'caption' => $media->getCustomProperty('caption'),
                                    'alt' => $media->getCustomProperty('alt_text') ?? $property->title
                                ];
                            }
                            
                            // Fallback
                            if (empty($images)) {
                                $images[] = [
                                    'src' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                                    'preview' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                                    'alt' => $property->title
                                ];
                            }
                        @endphp

                        <div x-data="{
                            active: 0,
                            images: {{ \Illuminate\Support\Js::from($images) }},
                            lightboxOpen: false,
                            next() { this.active = (this.active + 1) % this.images.length },
                            prev() { this.active = (this.active - 1 + this.images.length) % this.images.length }
                        }" class="space-y-4">
                            
                            <!-- Main Image -->
                            <div class="relative aspect-video rounded-3xl overflow-hidden shadow-2xl group cursor-pointer bg-gray-100" @click="lightboxOpen = true">
                                <template x-for="(image, index) in images" :key="index">
                                    <img :src="image.src" 
                                         x-show="active === index"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 scale-105"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="absolute inset-0 w-full h-full object-cover"
                                         :alt="image.alt">
                                </template>
                                
                                <!-- Navigation Overlays -->
                                <div class="absolute inset-0 flex items-center justify-between p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <button @click.stop="prev()" class="bg-white/20 backdrop-blur-md hover:bg-white/40 text-white p-3 rounded-full transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </button>
                                    <button @click.stop="next()" class="bg-white/20 backdrop-blur-md hover:bg-white/40 text-white p-3 rounded-full transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </div>

                                <!-- Expand Icon -->
                                <div class="absolute bottom-4 right-4 bg-black/50 backdrop-blur-md text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                    View Fullscreen
                                </div>
                            </div>

                            <!-- Thumbnails -->
                            <div class="grid grid-cols-4 sm:grid-cols-5 gap-3">
                                <template x-for="(image, index) in images" :key="index">
                                    <button @click="active = index" 
                                            class="relative aspect-square rounded-xl overflow-hidden ring-2 transition-all duration-200"
                                            :class="active === index ? 'ring-emerald-500 ring-offset-2' : 'ring-transparent opacity-70 hover:opacity-100'">
                                        <img :src="image.preview" class="w-full h-full object-cover">
                                    </button>
                                </template>
                            </div>

                            <!-- Lightbox Modal (Simplified for brevity, can be expanded) -->
                            <div x-show="lightboxOpen" 
                                 x-transition.opacity
                                 class="fixed inset-0 z-50 bg-black/95 backdrop-blur-sm flex items-center justify-center p-4"
                                 style="display: none;">
                                <button @click="lightboxOpen = false" class="absolute top-6 right-6 text-white/70 hover:text-white">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                <img :src="images[active].src" class="max-w-full max-h-[90vh] object-contain shadow-2xl rounded-lg">
                                
                                <button @click.stop="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white p-4">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <button @click.stop="next()" class="absolute right-4 top-1/2 -translate-y-1/2 text-white/70 hover:text-white p-4">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="aspect-video bg-gray-100 rounded-3xl flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="font-medium">No images available</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sticky Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-8 space-y-6">
                        <!-- Quick Stats Card -->
                        <div class="bg-white/80 backdrop-blur-xl rounded-2xl lg:rounded-3xl shadow-xl border border-white/50 p-6 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                            <div class="grid grid-cols-2 gap-4">
                                @if (in_array($property->propertyType->slug, ['apartment', 'house']))
                                    <div class="text-center p-3 bg-gray-50 rounded-lg lg:rounded-xl border border-gray-200">
                                        <span class="block text-2xl font-bold text-gray-900">{{ $property->bedrooms }}</span>
                                        <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Bedrooms</span>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 rounded-lg lg:rounded-xl border border-gray-200">
                                        <span class="block text-2xl font-bold text-gray-900">{{ $property->toilets }}</span>
                                        <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Bathrooms</span>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 rounded-lg lg:rounded-xl border border-gray-200 col-span-2">
                                        <span class="block text-2xl font-bold text-gray-900">{{ $property->size ?? 'N/A' }}</span>
                                        <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Square Meters</span>
                                    </div>
                                @else
                                    <div class="text-center p-3 bg-gray-50 rounded-lg lg:rounded-xl border border-gray-200 col-span-2">
                                        <span class="block text-2xl font-bold text-gray-900">{{ $property->size ?? 'N/A' }}</span>
                                        <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Size (SQM)</span>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-100 space-y-3">
                                @if ($this->getAgentPhoneNumber())
                                    <a href="tel:{{ $this->getAgentPhoneNumber() }}" class="group flex items-center justify-center gap-2 w-full bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-700 hover:via-emerald-600 hover:to-teal-600 text-white font-bold py-3 lg:py-4 px-4 lg:px-6 rounded-xl lg:rounded-2xl transition-all duration-500 transform hover:scale-[1.02] shadow-lg hover:shadow-emerald-500/40 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500 skew-y-12"></div>
                                        <svg class="w-5 h-5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        <span class="relative z-10">Call Agent</span>
                                    </a>
                                @endif
                                
                                <button wire:click="sendWhatsAppMessage" class="flex items-center justify-center gap-2 w-full bg-white/95 backdrop-blur-xl text-gray-700 font-semibold py-3 lg:py-4 px-4 lg:px-6 rounded-xl lg:rounded-2xl border-2 border-gray-300/60 hover:bg-white hover:border-gray-400/60 transition-all duration-500 transform hover:scale-105 shadow-lg">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-2.462-.96-4.779-2.705-6.526-1.746-1.746-4.065-2.707-6.526-2.709-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.092-.638zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51-.173-.008-.372-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z"/></svg>
                                    WhatsApp
                                </button>

                                <button wire:click="toggleContactForm" class="flex items-center justify-center gap-2 w-full bg-blue-50 text-blue-700 font-semibold py-3 lg:py-4 px-4 lg:px-6 rounded-xl lg:rounded-2xl border border-blue-200 hover:bg-blue-100 transition-all duration-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    Enquire Now
                                </button>
                            </div>
                        </div>

                        <!-- Agent Profile Card -->
                        <div class="bg-white/80 backdrop-blur-xl rounded-2xl lg:rounded-3xl shadow-xl border border-white/50 p-6 transition-all duration-300 hover:shadow-2xl">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Listed By</h3>
                            
                            @if($property->agent)
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="relative">
                                        @if($property->agent->profile_photo_url)
                                            <img src="{{ $property->agent->profile_photo_url }}" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-md">
                                        @else
                                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-xl font-bold border-2 border-white shadow-md">
                                                {{ substr($property->agent->name, 0, 1) }}
                                            </div>
                                        @endif
                                        @if($property->agent->is_verified)
                                            <div class="absolute -bottom-1 -right-1 bg-white rounded-full p-1 shadow-sm">
                                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-lg">{{ $property->agent->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $property->agent->agency->name ?? 'Independent Agent' }}</p>
                                    </div>
                                </div>
                                
                                @php
                                    $agentSlug = $property->agent?->slug;
                                @endphp
                                
                                @if($agentSlug)
                                    <a href="{{ route('agent.profile', $agentSlug) }}" wire:navigate class="block w-full text-center bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold py-2 px-4 rounded-xl transition-colors">
                                        View Profile & Listings
                                    </a>
                                @else
                                    <div class="block w-full text-center py-2 px-4 bg-gray-50 text-gray-400 font-medium rounded-lg text-sm">
                                        Agent profile unavailable
                                    </div>
                                @endif
                            @else
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">HomeBaze Listing</h4>
                                        <p class="text-sm text-gray-500">Verified Property</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Details Grid -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-10">
                    <!-- Description -->
                    <section>
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">About this property</h2>
                        <div class="prose prose-lg prose-gray max-w-none text-gray-600 leading-relaxed">
                            {{ $property->description }}
                        </div>
                    </section>

                    <!-- Amenities -->
                    @if ($property->features && $property->features->count() > 0)
                        <section>
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Features & Amenities</h2>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @foreach ($property->features as $feature)
                                    <div class="flex items-center gap-3 p-3 bg-white border border-gray-100 rounded-xl shadow-sm hover:border-emerald-200 transition-colors">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <span class="font-medium text-gray-700 text-sm">{{ $feature->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <!-- Neighborhood Overview -->
                    @if ($property->area && 
                        ($property->area->education_facilities || 
                         $property->area->healthcare_facilities || 
                         $property->area->transport_facilities || 
                         $property->area->shopping_facilities ||
                         $property->area->security_rating))
                        <section>
                            <h2 class="text-2xl font-bold text-gray-900 mb-6">Neighborhood Overview</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Education -->
                                @if ($property->area->education_facilities)
                                    <div class="bg-white/95 backdrop-blur-xs rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 p-5">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v5"></path></svg>
                                            </div>
                                            <h3 class="font-bold text-gray-900">Education</h3>
                                        </div>
                                        <div class="space-y-3">
                                            @foreach ($property->area->education_facilities as $facility)
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-gray-600">{{ $facility['name'] }}</span>
                                                    <span class="font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-lg text-xs">{{ $facility['distance'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Healthcare -->
                                @if ($property->area->healthcare_facilities)
                                    <div class="bg-white/95 backdrop-blur-xs rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 p-5">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                            </div>
                                            <h3 class="font-bold text-gray-900">Healthcare</h3>
                                        </div>
                                        <div class="space-y-3">
                                            @foreach ($property->area->healthcare_facilities as $facility)
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-gray-600">{{ $facility['name'] }}</span>
                                                    <span class="font-medium text-red-600 bg-red-50 px-2 py-1 rounded-lg text-xs">{{ $facility['distance'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Transport -->
                                @if ($property->area->transport_facilities)
                                    <div class="bg-white/95 backdrop-blur-xs rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 p-5">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                            </div>
                                            <h3 class="font-bold text-gray-900">Transport</h3>
                                        </div>
                                        <div class="space-y-3">
                                            @foreach ($property->area->transport_facilities as $facility)
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-gray-600">{{ $facility['name'] }}</span>
                                                    <span class="font-medium text-green-600 bg-green-50 px-2 py-1 rounded-lg text-xs">{{ $facility['distance'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Shopping -->
                                @if ($property->area->shopping_facilities)
                                    <div class="bg-white/95 backdrop-blur-xs rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 p-5">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                            </div>
                                            <h3 class="font-bold text-gray-900">Shopping</h3>
                                        </div>
                                        <div class="space-y-3">
                                            @foreach ($property->area->shopping_facilities as $facility)
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-gray-600">{{ $facility['name'] }}</span>
                                                    <span class="font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded-lg text-xs">{{ $facility['distance'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Security -->
                                @if ($property->area->security_rating)
                                    <div class="bg-white/95 backdrop-blur-xs rounded-2xl lg:rounded-3xl shadow-lg border border-gray-300/60 p-5">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="font-bold text-gray-900">Security Rating</h3>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-2xl font-bold text-emerald-600">{{ number_format($property->area->security_rating, 1) }}</span>
                                                <span class="text-sm text-gray-500">/10</span>
                                            </div>
                                        </div>
                                        @if ($property->area->security_features)
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($property->area->security_features as $feature)
                                                    <span class="text-xs font-medium text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-100">{{ $feature }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </section>
                    @endif
                </div>
            </div>
        </div>

        <!-- Similar Properties -->
        @if ($relatedProperties->count() > 0)
            <div class="bg-white py-16 border-t border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8">Similar Properties You Might Like</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($relatedProperties as $related)
                            <a href="{{ route('property.show', $related) }}" class="group block bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                                <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                                    <img src="{{ $related->getFirstMediaUrl('featured', 'preview') ?: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-60"></div>
                                    <div class="absolute bottom-3 left-3 text-white font-bold text-lg">
                                        {{ $related->formatted_price }}
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-900 mb-1 truncate group-hover:text-emerald-600 transition-colors">{{ $related->title }}</h3>
                                    <p class="text-sm text-gray-500 truncate mb-3">{{ $related->area->name ?? '' }}, {{ $related->city->name }}</p>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 font-medium">
                                        @if($related->bedrooms) <span>{{ $related->bedrooms }} Beds</span> @endif
                                        @if($related->toilets) <span>{{ $related->toilets }} Baths</span> @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Contact Form Modal -->
    @if ($showContactForm)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/75 transition-opacity backdrop-blur-sm" aria-hidden="true" wire:click="toggleContactForm"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mb-4" id="modal-title">Contact Agent</h3>
                                <form wire:submit.prevent="submitInquiry" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                        <input type="text" wire:model="inquiryName" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        @error('inquiryName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" wire:model="inquiryEmail" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        @error('inquiryEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <input type="tel" wire:model="inquiryPhone" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        @error('inquiryPhone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                                        <textarea wire:model="inquiryMessage" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                                        @error('inquiryMessage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-2">
                                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:w-auto sm:text-sm">
                                            Send Inquiry
                                        </button>
                                        <button type="button" wire:click="toggleContactForm" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:w-auto sm:text-sm">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('head')
    <style>
        /* Hide global navigation for property details view to keep it immersive */
        #navbar, #navbar + .h-20 { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
@endpush
