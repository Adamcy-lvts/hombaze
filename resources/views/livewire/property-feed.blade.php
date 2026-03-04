<div class="h-screen w-full bg-black overflow-y-auto overflow-x-hidden snap-y snap-mandatory no-scrollbar relative" x-data="{
    handleScroll(e) {
        // Trigger loadMore if we're near the bottom
        const el = e.target;
        if (el.scrollHeight - el.scrollTop - el.clientHeight < 200) {
            $wire.loadMore();
        }
    },
    shareModalOpen: false,
    shareUrl: '',
    shareTitle: '',
    shareProperty(title, url) {
        if (navigator.share) {
            navigator.share({
                title: title,
                text: 'Explore this premium property: ' + title,
                url: url
            }).catch(() => {});
        } else {
            this.shareUrl = url;
            this.shareTitle = title;
            this.shareModalOpen = true;
        }
    },
    copyToClipboard() {
        navigator.clipboard.writeText(this.shareUrl).then(() => {
            this.shareModalOpen = false;
            if (typeof showToast === 'function') {
                showToast('success', 'Property link copied to clipboard!', 'Success');
            } else {
                alert('Link copied to clipboard!');
            }
        });
    }
}" @scroll="handleScroll">
    
    {{-- Header overlay --}}
    <div class="fixed top-0 inset-x-0 z-50 p-4 pt-20 sm:pt-24 lg:pt-28 bg-gradient-to-b from-black/80 via-black/40 to-transparent pointer-events-none flex justify-between items-start">
        <a href="{{ route('landing') }}" class="pointer-events-auto flex items-center gap-2 group">
            <div class="w-10 h-10 rounded-full bg-black/40 backdrop-blur-md flex items-center justify-center text-white border border-white/20 group-hover:bg-black/60 transition-colors">
                <x-heroicon-s-arrow-left class="w-5 h-5" />
            </div>
            <span class="font-bold text-white tracking-tight drop-shadow-md hidden sm:block">Back</span>
        </a>
        <div class="pointer-events-auto">
             <a href="{{ route('listing.create') }}" class="group flex items-center gap-2 bg-emerald-600/90 backdrop-blur-md hover:bg-emerald-600 text-white pl-3 pr-1 py-1 rounded-full transition-all shadow-lg border border-white/10">
                <span class="font-bold text-sm">List</span>
                <div class="bg-white/20 rounded-full p-1.5 transition-colors">
                    <x-heroicon-m-plus class="w-4 h-4" />
                </div>
            </a>
        </div>
    </div>

    @forelse($properties as $index => $property)
        <div class="h-screen w-full snap-start relative flex-shrink-0 bg-gray-900" 
             wire:key="property-{{ $property->id }}-{{ $index }}">
             
             {{-- Background Image --}}
             @if($property->getFirstMedia('featured'))
                 <img src="{{ $property->getFeaturedImageUrl('large') }}" 
                      alt="{{ $property->title }}" 
                      class="absolute inset-0 w-full h-full object-cover">
             @else
                 <div class="absolute inset-0 w-full h-full flex items-center justify-center bg-gray-800 text-gray-500">
                     <x-heroicon-o-home class="w-24 h-24 opacity-20" />
                 </div>
             @endif

             {{-- Overlays --}}
             <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent pointer-events-none"></div>

             {{-- Main Content Window (Bottom Left) --}}
             <div class="absolute bottom-24 sm:bottom-8 left-0 right-16 sm:right-24 p-4 sm:p-6 pointer-events-none">
                 {{-- Status Badge --}}
                 <div class="pointer-events-auto mb-3 flex flex-wrap gap-2">
                     <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-md border border-white/20">
                         {{ ucfirst($property->listing_type) }}
                     </span>
                     @if($property->status !== 'available')
                         <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-red-500/80 text-white backdrop-blur-md">
                             {{ ucfirst($property->status) }}
                         </span>
                     @endif
                 </div>

                 <h2 class="text-2xl sm:text-3xl font-bold text-white leading-tight mb-1 drop-shadow-lg line-clamp-2">
                     {{ $property->title }}
                 </h2>
                 
                 <p class="text-gray-300 text-sm sm:text-base flex items-center mt-1 drop-shadow-md mb-4">
                     <x-heroicon-s-map-pin class="w-4 h-4 sm:w-5 sm:h-5 mr-1 text-emerald-400 shrink-0" />
                     <span class="truncate">{{ $property->city->name ?? 'Unknown city' }}, {{ $property->state->name ?? 'Unknown state' }}</span>
                 </p>

                 <div class="pointer-events-auto bg-black/40 backdrop-blur-md rounded-2xl p-4 border border-white/10 sm:max-w-md">
                     <div class="flex items-end justify-between mb-3 border-b border-white/10 pb-3">
                         <div>
                             <p class="text-gray-400 text-xs uppercase tracking-wider font-bold mb-0.5">Price</p>
                             <p class="text-xl sm:text-2xl font-bold text-white">
                                 ₦{{ number_format($property->price) }}
                                 @if($property->listing_type === 'rent') <span class="text-sm font-medium text-gray-400">/yr</span> @endif
                             </p>
                         </div>
                     </div>
                     
                     <div class="flex items-center gap-3 text-xs sm:text-sm text-gray-300 font-medium overflow-x-auto no-scrollbar pb-1">
                         @if($property->bedrooms)
                             <span class="bg-white/10 px-3 py-1.5 rounded-xl border border-white/5 flex items-center gap-1.5 shrink-0"><x-heroicon-o-view-columns class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" /> {{ $property->bedrooms }} Beds</span>
                         @endif
                         @if($property->bathrooms)
                             <span class="bg-white/10 px-3 py-1.5 rounded-xl border border-white/5 flex items-center gap-1.5 shrink-0"><x-heroicon-o-inbox class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" /> {{ $property->bathrooms }} Baths</span>
                         @endif
                         @if($property->plotSize)
                             <span class="bg-white/10 px-3 py-1.5 rounded-xl border border-white/5 shrink-0">{{ $property->plotSize->display_text }}</span>
                         @elseif($property->custom_plot_size)
                             <span class="bg-white/10 px-3 py-1.5 rounded-xl border border-white/5 shrink-0">{{ $property->custom_plot_size }} {{ $property->custom_plot_unit }}</span>
                         @endif
                     </div>
                     
                     <div class="mt-4 pt-3 border-t border-white/10">
                        <a href="{{ route('property.show', $property) }}" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-medium shadow-lg transition-all flex items-center justify-center gap-2">
                             View Details
                        </a>
                     </div>
                 </div>
             </div>

             {{-- Interaction Sidebar (Bottom Right) --}}
             <div class="absolute bottom-28 sm:bottom-12 right-2 sm:right-6 flex flex-col items-center gap-6 z-10 pointer-events-none">
                 {{-- Uploader Profile --}}
                 <a href="{{ $property->agent ? route('agent.profile', $property->agent->user) : '#' }}" class="pointer-events-auto group relative mb-2">
                     <div class="w-12 h-12 rounded-full border-2 border-white overflow-hidden bg-gray-800 shadow-lg">
                         @if($property->agent && $property->agent->user->profile_photo_url)
                             <img src="{{ $property->agent->user->profile_photo_url }}" class="w-full h-full object-cover">
                         @else
                             <div class="w-full h-full flex items-center justify-center text-white font-bold text-lg bg-emerald-700">
                                 {{ substr($property->agent ? $property->agent->user->name : 'A', 0, 1) }}
                             </div>
                         @endif
                     </div>
                     <div class="absolute -bottom-2.5 left-1/2 -translate-x-1/2 w-5 h-5 bg-emerald-500 rounded-full border-2 border-black flex items-center justify-center text-white">
                         <x-heroicon-m-plus class="w-3 h-3" />
                     </div>
                 </a>

                 {{-- Save Action --}}
                 <button wire:click="toggleSaveProperty({{ $property->id }})" class="pointer-events-auto flex flex-col items-center gap-1 group">
                     @php
                         $isSaved = in_array($property->id, $savedPropertyIds);
                     @endphp
                     <div class="w-12 h-12 rounded-full backdrop-blur-md flex items-center justify-center transition-all transform hover:scale-110 active:scale-95 group-hover:bg-white/20 {{ $isSaved ? 'bg-red-500/80 text-white' : 'bg-black/40 text-white' }}">
                         @if($isSaved)
                             <x-heroicon-s-heart class="w-7 h-7" />
                         @else
                             <x-heroicon-o-heart class="w-7 h-7" />
                         @endif
                     </div>
                     <span class="text-xs font-semibold text-white drop-shadow-md">Save</span>
                 </button>

                 {{-- Share Action --}}
                 <button @click="shareProperty('{{ addslashes($property->title) }}', '{{ route('property.show', $property) }}')" class="pointer-events-auto flex flex-col items-center gap-1 group">
                     <div class="w-12 h-12 rounded-full bg-black/40 backdrop-blur-md flex items-center justify-center text-white transition-all transform hover:scale-110 active:scale-95 group-hover:bg-white/20">
                         <x-heroicon-o-share class="w-7 h-7" />
                     </div>
                     <span class="text-xs font-semibold text-white drop-shadow-md">Share</span>
                 </button>
                 
                 {{-- Contact Action --}}
                 <a href="{{ route('property.show', $property) }}#contact" class="pointer-events-auto flex flex-col items-center gap-1 group mt-2">
                     <div class="w-12 h-12 rounded-full bg-emerald-600 flex items-center justify-center text-white transition-all transform hover:scale-110 active:scale-95 shadow-lg shadow-emerald-500/30">
                         <x-heroicon-s-chat-bubble-oval-left-ellipsis class="w-6 h-6" />
                     </div>
                     <span class="text-xs font-semibold text-white drop-shadow-md">Chat</span>
                 </a>
             </div>
        </div>
    @empty
        <div class="h-screen w-full flex flex-col items-center justify-center bg-gray-900 text-white p-6">
            <x-heroicon-o-face-frown class="w-16 h-16 text-gray-600 mb-4" />
            <h2 class="text-2xl font-bold mb-2">No properties here</h2>
            <p class="text-gray-400 text-center max-w-sm mb-6">We couldn't find any available properties to show you right now.</p>
            <a href="{{ route('landing') }}" class="px-6 py-3 bg-white text-gray-900 font-bold rounded-xl hover:bg-gray-100 transition-colors">
                Back to Home
            </a>
        </div>
    @endforelse
    
    {{-- Loading state for infinite scroll --}}
    <div wire:loading wire:target="loadMore" class="w-full p-6 flex justify-center bg-gray-900 absolute bottom-0 z-20">
        <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    {{-- Fallback Share Modal --}}
    <div x-show="shareModalOpen" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center pointer-events-auto" x-cloak>
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="shareModalOpen = false" x-transition.opacity></div>
        
        <div class="bg-gray-900 w-full sm:w-[400px] rounded-t-3xl sm:rounded-3xl p-6 relative z-10 border border-white/10 shadow-2xl"
             x-show="shareModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-y-full sm:scale-95 sm:opacity-0"
             x-transition:enter-end="transform translate-y-0 sm:scale-100 sm:opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform translate-y-0 sm:scale-100 sm:opacity-100"
             x-transition:leave-end="transform translate-y-full sm:scale-95 sm:opacity-0"
             @click.stop>
             
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white">Share Property</h3>
                <button @click="shareModalOpen = false" class="p-2 bg-white/10 rounded-full hover:bg-white/20 text-white transition-colors">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>
            
            <div class="grid grid-cols-4 gap-4 mb-6">
                <!-- WhatsApp -->
                <a :href="`https://wa.me/?text=${encodeURIComponent('Check out this premium property: ' + shareTitle + ' ' + shareUrl)}`" 
                   target="_blank" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-full bg-[#25D366] flex items-center justify-center text-white transition-transform group-hover:scale-110 active:scale-95">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.94 3.659 1.437 5.63 1.438h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </div>
                    <span class="text-xs text-gray-300 font-medium">WhatsApp</span>
                </a>
                
                <!-- Twitter / X -->
                <a :href="`https://x.com/intent/tweet?text=${encodeURIComponent('Check out this premium property: ' + shareTitle)}&url=${encodeURIComponent(shareUrl)}`" 
                   target="_blank" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-full bg-black flex items-center justify-center border border-white/20 text-white transition-transform group-hover:scale-110 active:scale-95">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </div>
                    <span class="text-xs text-gray-300 font-medium">X</span>
                </a>

                <!-- Facebook -->
                <a :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`" 
                   target="_blank" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-full bg-[#1877F2] flex items-center justify-center text-white transition-transform group-hover:scale-110 active:scale-95">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                    </div>
                    <span class="text-xs text-gray-300 font-medium">Facebook</span>
                </a>

                <!-- Copy Link -->
                <button @click="copyToClipboard()" class="flex flex-col items-center gap-2 group">
                    <div class="w-14 h-14 rounded-full bg-gray-700 flex items-center justify-center text-white transition-transform group-hover:scale-110 active:scale-95">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    </div>
                    <span class="text-xs text-gray-300 font-medium">Copy Link</span>
                </button>
            </div>
            
            <div class="bg-gray-800 rounded-xl p-3 flex items-center gap-3">
                <div class="truncate flex-1 text-sm text-gray-400 font-medium" x-text="shareUrl"></div>
                <button @click="copyToClipboard()" class="px-4 py-1.5 bg-white/10 hover:bg-white/20 text-white text-sm font-bold rounded-lg transition-colors shrink-0">
                    Copy
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('property-saved', (event) => {
                if (typeof showToast === 'function') {
                    showToast('success', event.message || 'Property saved successfully!', 'Saved');
                }
            });

            Livewire.on('property-unsaved', (event) => {
                if (typeof showToast === 'function') {
                    showToast('info', event.message || 'Removed from saved properties', 'Removed');
                }
            });
        });
    </script>
@endpush
