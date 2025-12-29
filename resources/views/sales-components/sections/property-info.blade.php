<div class="bg-white dark:bg-slate-900 p-10 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm transition-all duration-300 group relative overflow-hidden">
    {{-- Background Accent --}}
    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl pointer-events-none transition-colors group-hover:bg-blue-500/10"></div>

    <div class="flex items-center gap-5 mb-10 relative z-10">
        <div class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center border border-blue-500/20 group-hover:scale-110 transition-transform">
            <x-filament::icon icon="heroicon-o-home-modern" class="w-7 h-7 text-blue-600 dark:text-blue-400" />
        </div>
        <div>
            <h2 class="text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.3em] mb-1">Subject Property</h2>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Asset Identification & Location</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 relative z-10 box-border">
        {{-- Primary Info --}}
        <div class="md:col-span-12 grid grid-cols-1 md:grid-cols-3 gap-6 pb-6 border-b border-gray-100 dark:border-slate-800">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Property Title</p>
                <p class="text-base font-black text-gray-900 dark:text-white tracking-tight leading-snug">{{ $property?->title ?? 'N/A' }}</p>
            </div>
            
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Category</p>
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $property?->propertyType?->name ?? 'N/A' }}</p>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Location</p>
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-m-map-pin" class="w-4 h-4 text-blue-500" />
                    <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $property?->city?->name ?? 'N/A' }}, {{ $property?->state?->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Address & Value --}}
        <div class="md:col-span-7">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Physical Address</p>
            <p class="text-sm font-bold text-gray-700 dark:text-gray-400 leading-relaxed">{{ $property?->address ?? 'N/A' }}</p>
        </div>

        <div class="md:col-span-5 bg-blue-50/50 dark:bg-blue-900/10 p-5 rounded-xl border border-blue-100 dark:border-blue-900/30">
            <p class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em] mb-1">Market Valuation</p>
            <p class="text-2xl font-black text-blue-600 dark:text-blue-400 tracking-tight">{{ formatNaira($property?->price ?? 0) }}</p>
        </div>
    </div>
</div>
