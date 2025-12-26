@props(['property', 'size' => 'md'])

@php
    $isLarge = $size === 'lg';
    $isSmall = $size === 'sm';
    
    $titleClass = $isLarge ? 'text-4xl md:text-6xl' : ($isSmall ? 'text-lg md:text-xl' : 'text-2xl md:text-4xl');
    $paddingClass = $isLarge ? 'p-10 md:p-16' : ($isSmall ? 'p-5 md:p-8' : 'p-8 md:p-12');
@endphp

<div x-data="{ 
        tiltX: 0, 
        tiltY: 0,
        handleMouseMove(e) {
            const el = $el.getBoundingClientRect();
            const x = e.clientX - el.left;
            const y = e.clientY - el.top;
            const centerX = el.width / 2;
            const centerY = el.height / 2;
            this.tiltX = (y - centerY) / 20;
            this.tiltY = (centerX - x) / 20;
        },
        resetTilt() {
            this.tiltX = 0;
            this.tiltY = 0;
        }
     }"
     @mousemove="handleMouseMove"
     @mouseleave="resetTilt"
     :style="`transform: perspective(1000px) rotateX(${tiltX}deg) rotateY(${tiltY}deg)`"
     class="group/bento relative w-full h-full overflow-hidden rounded-[2.5rem] md:rounded-[3.5rem] bg-slate-900 transition-all duration-500 ease-out shadow-2xl hover:shadow-emerald-500/20 active:scale-[0.98] cursor-pointer"
>
    <a href="{{ route('property.show', $property->slug ?? $property->id) }}" class="absolute inset-0 z-10"></a>

    <!-- Background Image with Parallax -->
    <div class="absolute inset-0 z-0">
        @if($property->getMedia('featured')->count() > 0)
            <img src="{{ $property->getFirstMedia('featured')->getUrl() }}" 
                 alt="{{ $property->title }}" 
                 class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover/bento:scale-110"
                 :style="`transform: scale(1.1) translate(${tiltY/2}px, ${-tiltX/2}px)`">
        @else
            <div class="w-full h-full bg-slate-800 flex items-center justify-center">
                <svg class="w-12 h-12 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        @endif
        
        <!-- Overlays -->
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent opacity-90 transition-opacity duration-700 group-hover/bento:opacity-100"></div>
        <div class="absolute inset-0 ring-1 ring-inset ring-white/10 rounded-[3.5rem]"></div>
    </div>

    <!-- Top Actions (Level with Header) -->
    <div class="absolute top-6 left-6 right-6 md:top-10 md:left-10 md:right-10 flex items-center justify-between z-20">
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-white/10 backdrop-blur-2xl border border-white/10 rounded-2xl text-[10px] md:text-xs font-black text-white uppercase tracking-[0.2em]">
                {{ $property->propertyType->name ?? 'Residential' }}
            </span>
            @if($property->is_verified)
                <div class="w-8 h-8 rounded-full bg-emerald-500/90 backdrop-blur-md flex items-center justify-center text-white border border-white/20">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                </div>
            @endif
        </div>
        
        <button wire:click.stop.prevent="toggleSaveProperty({{ $property->id }})" 
                class="relative z-30 w-12 h-12 md:w-14 md:h-14 flex items-center justify-center rounded-2xl bg-white/10 backdrop-blur-2xl border border-white/10 text-white hover:bg-rose-500 hover:border-rose-500 transition-all duration-300">
            @if($this->isPropertySaved($property->id))
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            @else
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            @endif
        </button>
    </div>

    <!-- Content -->
    <div class="relative z-20 w-full h-full flex flex-col justify-end {{ $paddingClass }}">
        <div class="flex items-center gap-3 mb-6">
            <span class="flex items-center gap-2 px-4 py-1.5 bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-xl shadow-emerald-500/20">
                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                {{ $property->listing_type === 'sale' ? 'For Sale' : 'For Rent' }}
            </span>
        </div>

        <h3 class="{{ $titleClass }} font-black text-white leading-[0.95] mb-6 tracking-tighter group-hover/bento:text-emerald-300 transition-colors drop-shadow-2xl">
            {{ $property->title }}
        </h3>

        <div class="flex flex-wrap items-center justify-between gap-6 pt-10 border-t border-white/10">
            <div class="flex flex-col">
                <span class="text-white/40 text-[10px] font-bold uppercase tracking-[0.2em] mb-1">Pricing</span>
                <div class="flex items-baseline gap-1">
                    <span class="{{ $isLarge ? 'text-4xl md:text-6xl' : 'text-2xl md:text-4xl' }} font-black text-white tracking-tight">₦{{ number_format($property->price) }}</span>
                    @if($property->listing_type === 'rent')
                        <span class="text-white/40 text-sm font-medium">/ year</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex flex-col items-center gap-1">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center group-hover/bento:bg-emerald-500/20 group-hover/bento:border-emerald-500/20 transition-all duration-500">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-white/50 uppercase">{{ $property->bedrooms ?? 0 }} Beds</span>
                </div>
            </div>
        </div>
    </div>
</div>
