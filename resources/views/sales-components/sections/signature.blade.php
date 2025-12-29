@php
    $sellerName = $agreement->seller_name ?: $agreement->property?->owner?->name;
    $buyerName = $agreement->buyer_name ?: $agreement->buyer?->name;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-20">
    {{-- Seller Section --}}
    <div class="space-y-10 group">
        <div class="h-40 border-b-2 border-gray-100 dark:border-slate-800 relative flex items-end pb-6 transition-colors duration-500 group-hover:border-blue-500/30">
            <div class="absolute inset-0 bg-linear-to-t from-blue-50/20 to-transparent dark:from-blue-900/5 opacity-0 group-hover:opacity-100 transition-opacity rounded-t-2xl"></div>
            @if($agreement->status === 'signed' || $agreement->status === 'completed')
                <div class="absolute inset-x-0 top-0 flex items-center justify-center h-full pointer-events-none overflow-hidden">
                    <div class="border-4 border-blue-500/20 px-6 py-2 rounded-xl rotate-[-15deg] transform scale-150 opacity-40">
                        <span class="text-4xl font-black text-blue-500 tracking-[0.3em] uppercase whitespace-nowrap">EXECUTED</span>
                    </div>
                </div>
                <div class="absolute bottom-6 left-0 animate-in fade-in slide-in-from-left-4 duration-1000">
                    <span class="text-4xl font-normal text-blue-800/80 dark:text-blue-400/70 signature-font">{{ $sellerName }}</span>
                </div>
            @endif
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] absolute -bottom-8 left-0">Digital Signature of Seller</p>
        </div>
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center border border-blue-100 dark:border-blue-900/30 transition-transform duration-500 group-hover:scale-110">
                <x-filament::icon icon="heroicon-o-user" class="w-7 h-7 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <p class="text-lg font-black text-gray-900 dark:text-white tracking-tight">{{ $sellerName ?? 'Authorized Seller' }}</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Verified: {{ $agreement->signed_date?->format('F j, Y') ?? 'Pending' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Buyer Section --}}
    <div class="space-y-10 group">
        <div class="h-40 border-b-2 border-gray-100 dark:border-slate-800 relative flex items-end pb-6 transition-colors duration-500 group-hover:border-emerald-500/30">
            <div class="absolute inset-0 bg-linear-to-t from-emerald-50/20 to-transparent dark:from-emerald-900/5 opacity-0 group-hover:opacity-100 transition-opacity rounded-t-2xl"></div>
            @if($agreement->status === 'signed' || $agreement->status === 'completed')
                <div class="absolute inset-x-0 top-0 flex items-center justify-center h-full pointer-events-none overflow-hidden">
                    <div class="border-4 border-emerald-500/20 px-6 py-2 rounded-xl rotate-[-15deg] transform scale-150 opacity-40">
                        <span class="text-4xl font-black text-emerald-500 tracking-[0.3em] uppercase whitespace-nowrap">EXECUTED</span>
                    </div>
                </div>
                <div class="absolute bottom-6 left-0 animate-in fade-in slide-in-from-left-4 duration-1000 delay-300">
                    <span class="text-4xl font-normal text-emerald-800/80 dark:text-emerald-400/70 signature-font">{{ $buyerName }}</span>
                </div>
            @endif
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] absolute -bottom-8 left-0">Digital Signature of Buyer</p>
        </div>
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center border border-emerald-100 dark:border-emerald-900/30 transition-transform duration-500 group-hover:scale-110">
                <x-filament::icon icon="heroicon-o-user" class="w-7 h-7 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div>
                <p class="text-lg font-black text-gray-900 dark:text-white tracking-tight">{{ $buyerName ?? 'Authorized Buyer' }}</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Verified: {{ $agreement->signed_date?->format('F j, Y') ?? 'Pending' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Mrs+Saint+Delafield&display=swap');
    .signature-font {
        font-family: 'Mrs Saint Delafield', cursive;
    }
</style>
