<div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl relative group">
    {{-- Header Banner --}}
    <div class="px-10 py-6 bg-linear-to-r from-slate-800 to-slate-900 border-b border-slate-800 flex items-center justify-between relative z-10">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20">
                <x-filament::icon icon="heroicon-o-banknotes" class="w-6 h-6 text-emerald-500" />
            </div>
            <div>
                <h2 class="text-sm font-black text-white uppercase tracking-[0.2em]">Financial Summary</h2>
                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">Payment Terms & Schedule</p>
            </div>
        </div>
        <div class="px-4 py-1.5 bg-emerald-500/10 rounded-full border border-emerald-500/20 shadow-lg shadow-emerald-500/5">
            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ $agreement->status ?? 'Draft' }}
            </p>
        </div>
    </div>
    
    <div class="p-12 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
            {{-- Total Value --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Gross Asset Value</p>
                </div>
                <p class="text-4xl font-black text-white leading-none tracking-tighter">{{ formatNaira($agreement->sale_price ?? 0) }}</p>
                <div class="pt-4 border-t border-slate-800">
                    <p class="text-[10px] font-bold text-slate-500 leading-relaxed italic">Agreed purchase price for the subject property</p>
                </div>
            </div>
            
            {{-- Deposit --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em]">Initial Deposit</p>
                    @php
                        $percentage = ($agreement->sale_price > 0) ? ($agreement->deposit_amount / $agreement->sale_price) * 100 : 0;
                    @endphp
                    <span class="text-[10px] font-black text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">{{ number_format($percentage, 0) }}%</span>
                </div>
                <p class="text-4xl font-black text-emerald-400 leading-none tracking-tighter">{{ formatNaira($agreement->deposit_amount ?? 0) }}</p>
                
                <div class="pt-4 border-t border-slate-800">
                    <div class="h-2 w-full bg-slate-800 rounded-full overflow-hidden mb-2">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest tracking-tighter">Earnest Money Verification</p>
                </div>
            </div>

            {{-- Balance --}}
            <div class="space-y-4">
                <p class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em]">Balance at Closing</p>
                @if(($agreement->balance_amount ?? 0) <= 0)
                    <p class="text-4xl font-black text-blue-400 leading-none tracking-tighter uppercase italic">NIL</p>
                @else
                    <p class="text-4xl font-black text-blue-400 leading-none tracking-tighter">{{ formatNaira($agreement->balance_amount ?? 0) }}</p>
                @endif
                
                <div class="pt-4 border-t border-slate-800">
                    <div class="flex items-center gap-2 text-slate-400">
                        <x-filament::icon icon="heroicon-m-calendar-days" class="w-4 h-4 text-blue-500" />
                        <p class="text-xs font-bold">{{ $agreement->closing_date?->format('M d, Y') ?? 'Closing Date' }}</p>
                    </div>
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mt-1">Final Settlement Deadline</p>
                </div>
            </div>
        </div>

        {{-- Footer Details --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mt-16 pt-12 border-t border-slate-800">
            <div class="flex items-center gap-5">
                <div class="w-12 h-12 rounded-2xl bg-slate-800 flex items-center justify-center shrink-0 border border-slate-700 shadow-inner group-hover:scale-110 transition-transform">
                    <x-filament::icon icon="heroicon-o-calendar" class="w-6 h-6 text-slate-400" />
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Effective Agreement Date</p>
                    <p class="text-base font-bold text-slate-200 tracking-tight">{{ $agreement->signed_date?->format('F j, Y') ?? 'Awaiting Signatures' }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-5">
                <div class="w-12 h-12 rounded-2xl bg-slate-800 flex items-center justify-center shrink-0 border border-slate-700 shadow-inner group-hover:scale-110 transition-transform">
                    <x-filament::icon icon="heroicon-o-clock" class="w-6 h-6 text-slate-400" />
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1">Closing & Possession</p>
                    <p class="text-base font-bold text-slate-200 tracking-tight">{{ $agreement->closing_date?->format('F j, Y') ?? 'To be determined' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Decorative Background Elements --}}
    <div class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl pointer-events-none"></div>
</div>
