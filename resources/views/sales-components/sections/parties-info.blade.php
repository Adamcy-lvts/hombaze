@php
    $propertyOwner = $agreement->property?->owner;
    $sellerName = $agreement->seller_name ?: $propertyOwner?->name;
    $sellerEmail = $agreement->seller_email ?: $propertyOwner?->email;
    $sellerPhone = $agreement->seller_phone ?: $propertyOwner?->phone;
    $sellerAddress = $agreement->seller_address ?: $propertyOwner?->address;

    $buyerUser = $agreement->buyer;
    $buyerName = $agreement->buyer_name ?: $buyerUser?->name;
    $buyerEmail = $agreement->buyer_email ?: $buyerUser?->email;
    $buyerPhone = $agreement->buyer_phone ?: $buyerUser?->phone;
    $buyerAddress = $agreement->buyer_address ?: $buyerUser?->address;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-12 mt-12">
    {{-- First Party Section --}}
    <div class="relative p-10 bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm transition-all duration-300 hover:shadow-md group">
        {{-- Floating Label --}}
        <div class="absolute -top-4 left-8 px-4 py-1.5 bg-blue-600 rounded-full shadow-lg shadow-blue-500/20 z-20">
            <h3 class="text-[10px] font-black text-white uppercase tracking-[0.2em]">First Party: Seller</h3>
        </div>
        
        <div class="space-y-8 relative z-10 pt-4">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Legal Name</p>
                <p class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $sellerName ?? 'N/A' }}</p>
            </div>
            
            <div class="grid grid-cols-1 gap-6">
                <div class="flex items-center gap-4 group/item">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center border border-blue-100 dark:border-blue-900/30 transition-transform group-hover/item:scale-110">
                        <x-filament::icon icon="heroicon-m-envelope" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Email Address</p>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $sellerEmail ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 group/item">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center border border-blue-100 dark:border-blue-900/30 transition-transform group-hover/item:scale-110">
                        <x-filament::icon icon="heroicon-m-phone" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Contact Phone</p>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $sellerPhone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            @if($sellerAddress)
                <div class="pt-6 border-t border-gray-100 dark:border-slate-800">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Registered Address</p>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 leading-relaxed">{{ $sellerAddress }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Second Party Section --}}
    <div class="relative p-10 bg-white dark:bg-slate-900 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm transition-all duration-300 hover:shadow-md group">
        {{-- Floating Label --}}
        <div class="absolute -top-4 left-8 px-4 py-1.5 bg-emerald-600 rounded-full shadow-lg shadow-emerald-500/20 z-20">
            <h3 class="text-[10px] font-black text-white uppercase tracking-[0.2em]">Second Party: Buyer</h3>
        </div>
        
        <div class="space-y-8 relative z-10 pt-4">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Legal Name</p>
                <p class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $buyerName ?? 'N/A' }}</p>
            </div>
            
            <div class="grid grid-cols-1 gap-6">
                <div class="flex items-center gap-4 group/item">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center border border-emerald-100 dark:border-emerald-900/30 transition-transform group-hover/item:scale-110">
                        <x-filament::icon icon="heroicon-m-envelope" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Email Address</p>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $buyerEmail ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 group/item">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center border border-emerald-100 dark:border-emerald-900/30 transition-transform group-hover/item:scale-110">
                        <x-filament::icon icon="heroicon-m-phone" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Contact Phone</p>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $buyerPhone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            @if($buyerAddress)
                <div class="pt-6 border-t border-gray-100 dark:border-slate-800">
                    <p class="text-[10px) font-bold text-gray-400 uppercase tracking-widest mb-2">Registered Address</p>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 leading-relaxed">{{ $buyerAddress }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
