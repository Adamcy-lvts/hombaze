<div
    class="relative w-full"
    x-data="{
        selectedIndex: @entangle('selectedIndex'),
        showSuggestions: @entangle('showSuggestions'),
    }"
    @click.outside="$wire.hideSuggestions()"
    @keydown.escape.window="$wire.hideSuggestions()"
>
    @if($compact)
        {{-- Compact version for navbar/header --}}
        <div class="relative flex items-center">
            <div class="absolute left-3 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="query"
                wire:keydown.enter.prevent="handleEnter"
                wire:keydown.arrow-up.prevent="navigateUp"
                wire:keydown.arrow-down.prevent="navigateDown"
                @focus="$wire.showSuggestionsDropdown()"
                placeholder="{{ $placeholder }}"
                class="w-full pl-10 pr-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                autocomplete="off"
                @if($autoFocus) autofocus @endif
            >
        </div>
    @else
        {{-- Full/Hero version with glassmorphism --}}
        <div class="relative flex items-center bg-white/10 backdrop-blur-xl border border-white/20 rounded-full p-1.5 shadow-2xl transition-all duration-300 hover:bg-white/15 ring-1 ring-white/30">
            <div class="pl-4 text-emerald-400 hidden sm:block">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="query"
                wire:keydown.enter.prevent="handleEnter"
                wire:keydown.arrow-up.prevent="navigateUp"
                wire:keydown.arrow-down.prevent="navigateDown"
                @focus="$wire.showSuggestionsDropdown()"
                placeholder="{{ $placeholder }}"
                class="w-full bg-transparent border-none focus:ring-0 text-white placeholder-white/60 text-base sm:text-lg px-4 sm:px-4 py-2.5 sm:py-3 font-medium cursor-text"
                autocomplete="off"
                @if($autoFocus) autofocus @endif
            >
            <button
                wire:click="search"
                class="bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white rounded-full font-bold shadow-lg hover:shadow-emerald-500/20 transform hover:-translate-y-0.5 transition-all duration-200
                       p-2 sm:px-8 sm:py-3 shrink-0"
            >
                <span class="hidden sm:inline text-base">Search</span>
                <span class="sm:hidden block p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
            </button>
        </div>
    @endif

    {{-- Suggestions Dropdown --}}
    @if($showSuggestions && count($suggestions) > 0)
        <div
            class="absolute w-full mt-2 bg-white/95 backdrop-blur-xl border border-gray-200 rounded-xl shadow-2xl max-h-80 overflow-y-auto z-100 text-left"
            @if($compact) style="min-width: 320px;" @endif
        >
            @php $currentCategory = null; @endphp
            @foreach($suggestions as $index => $suggestion)
                {{-- Category Header --}}
                @if($suggestion['category'] !== $currentCategory)
                    @php $currentCategory = $suggestion['category']; @endphp
                    <div class="px-4 py-2 bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100 sticky top-0">
                        {{ $suggestion['category'] }}
                    </div>
                @endif

                <div
                    wire:click="selectSuggestion({{ $index }})"
                    class="flex items-center px-4 py-3 cursor-pointer transition-colors duration-200 border-b border-gray-100 last:border-b-0 group
                        {{ $selectedIndex === $index ? 'bg-emerald-50' : 'hover:bg-gray-50' }}"
                    @mouseenter="selectedIndex = {{ $index }}"
                >
                    <div class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200
                        @switch($suggestion['type'])
                            @case('recent')
                                bg-gray-100 text-gray-500
                                @break
                            @case('property')
                                bg-emerald-100 text-emerald-600
                                @break
                            @case('location')
                                bg-blue-100 text-blue-600
                                @break
                            @case('property_type')
                                bg-purple-100 text-purple-600
                                @break
                            @default
                                bg-gray-100 text-gray-500
                        @endswitch
                    ">
                        @switch($suggestion['icon'])
                            @case('clock')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                @break
                            @case('home')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                @break
                            @case('location-dot')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                @break
                            @case('building')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                @break
                            @default
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                        @endswitch
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm text-gray-900 truncate {{ $selectedIndex === $index ? 'text-emerald-700' : 'group-hover:text-emerald-700' }} transition-colors">
                            {{ $suggestion['text'] }}
                        </div>
                        @if($suggestion['subtitle'])
                            <div class="text-xs text-gray-500 truncate">{{ $suggestion['subtitle'] }}</div>
                        @endif
                    </div>
                    @if($suggestion['type'] === 'property')
                        <div class="ml-2 text-xs text-emerald-600 font-medium">View</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Loading indicator --}}
    <div wire:loading.delay wire:target="query" class="absolute right-4 top-1/2 -translate-y-1/2 @if(!$compact) mr-28 @endif">
        <svg class="animate-spin h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
</div>
