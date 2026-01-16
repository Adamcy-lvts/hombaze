<div class="flex flex-col bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-4 mb-2">
    <div class="flex items-start justify-between gap-3 mb-2">
        <div class="min-w-0">
            <h3 class="font-bold text-gray-900 dark:text-white truncate">
                {{ $getRecord()->property->title ?? 'Unknown Property' }}
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                <x-heroicon-o-user class="w-3 h-3" />
                {{ $getRecord()->tenant->name ?? 'Unknown Tenant' }}
            </p>
        </div>
        
        <x-filament::badge 
            :color="match ($getRecord()->status) {
                'draft' => 'gray',
                'active' => 'success',
                'expired' => 'danger',
                'terminated' => 'warning',
                'renewed' => 'info',
                default => 'gray',
            }"
            size="sm"
        >
            {{ ucfirst($getRecord()->status) }}
        </x-filament::badge>
    </div>

    <div class="flex items-center justify-between py-3 border-t border-b border-gray-50 dark:border-gray-800 my-2">
        <div class="flex flex-col">
            <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Rent</span>
            <span class="font-bold text-emerald-600 dark:text-emerald-400">
                ₦{{ number_format($getRecord()->yearly_rent) }}
            </span>
        </div>
        <div class="flex flex-col items-end">
            <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Frequency</span>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ ucfirst($getRecord()->payment_frequency) }}
            </span>
        </div>
    </div>

    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mt-1">
        <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" />
        <span>
            {{ $getRecord()->start_date?->format('M d, Y') }} 
            <span class="mx-1">&rarr;</span> 
            {{ $getRecord()->end_date?->format('M d, Y') }}
        </span>
    </div>
</div>
