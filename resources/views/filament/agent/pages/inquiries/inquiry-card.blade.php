<div class="flex flex-col gap-1 p-4 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 transition-all hover:shadow-md mb-3">
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-3">
            @if($getRecord()->property && $getRecord()->property->getFirstMediaUrl('featured'))
                <img src="{{ $getRecord()->property->getFirstMediaUrl('featured') }}" 
                     alt="{{ $getRecord()->property->title }}" 
                     class="w-12 h-12 rounded-lg object-cover bg-gray-100">
            @else
                <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                    <x-heroicon-o-building-office-2 class="w-6 h-6" />
                </div>
            @endif
            
            <div class="flex-1 min-w-0">
                <h4 class="font-semibold text-sm text-gray-900 dark:text-white line-clamp-1">
                    {{ $getRecord()->property->title ?? 'Unknown Property' }}
                </h4>
                <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                    <span class="font-medium text-gray-700 dark:text-gray-300 truncate">{{ $getRecord()->inquirer_name }}</span>
                    <span class="shrink-0">&bull;</span>
                    <span class="shrink-0">{{ $getRecord()->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        <x-filament::badge
            :color="match ($getRecord()->status) {
                'pending' => 'warning',
                'responded' => 'success',
                'viewing_scheduled' => 'info',
                'closed' => 'gray',
                default => 'gray',
            }"
        >
            {{ ucfirst($getRecord()->status) }}
        </x-filament::badge>
    </div>

    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
        {{ $getRecord()->message }}
    </div>
    
    <div class="mt-3 flex items-center justify-between border-t border-gray-100 dark:border-gray-800 pt-3">
        <div class="flex gap-4">
            <a href="tel:{{ $getRecord()->inquirer_phone }}" class="text-xs font-medium text-gray-500 hover:text-primary-600 flex items-center gap-1" @click.stop>
                <x-heroicon-m-phone class="w-3.5 h-3.5" />
                Call
            </a>
            <a href="mailto:{{ $getRecord()->inquirer_email }}" class="text-xs font-medium text-gray-500 hover:text-primary-600 flex items-center gap-1" @click.stop>
                <x-heroicon-m-envelope class="w-3.5 h-3.5" />
                Email
            </a>
        </div>
        
        <x-heroicon-m-chevron-right class="w-4 h-4 text-gray-400" />
    </div>
</div>
