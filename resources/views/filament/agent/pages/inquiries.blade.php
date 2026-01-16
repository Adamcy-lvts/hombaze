<x-filament-panels::page>
    <div class="flex flex-col space-y-6 pb-24">
        {{-- Custom Search & Filter Header --}}
        <div class="flex items-center gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="relative flex-1">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                <input type="text" 
                       wire:model.live.debounce.500ms="tableSearch" 
                       placeholder="Search inquiries..." 
                       class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-800 border-none rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-primary-500/20">
            </div>
            {{-- Filter Trigger (Optional - using Filament's if accessible, or simple dropdown) --}}
            <x-filament::icon-button
                icon="heroicon-m-funnel"
                color="gray"
                class="shrink-0"
                wire:click="$toggle('tableFilters')"
            />
        </div>

        {{-- Mobile Friendly Cards Loop --}}
        <div class="space-y-4">
            @php
                $records = $this->table->getRecords();
            @endphp

            @forelse($records as $record)
                <div class="flex flex-col bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden transition-all active:scale-[0.99]">
                    {{-- Card Header --}}
                    <div class="p-4 flex gap-4 items-start">
                        {{-- Image --}}
                        <div class="shrink-0 relative">
                            @if($record->property && $record->property->getFirstMediaUrl('featured'))
                                <img src="{{ $record->property->getFirstMediaUrl('featured') }}" 
                                     alt="{{ $record->property->title }}" 
                                     class="w-16 h-16 rounded-xl object-cover bg-gray-100 dark:bg-gray-800">
                            @else
                                <div class="w-16 h-16 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                                    <x-heroicon-o-building-office-2 class="w-8 h-8" />
                                </div>
                            @endif
                            
                            {{-- Status Dot --}}
                            <div class="absolute -top-1 -right-1 w-4 h-4 rounded-full border-2 border-white dark:border-gray-900 flex items-center justify-center
                                {{ match ($record->status) {
                                    'pending' => 'bg-amber-500',
                                    'responded' => 'bg-emerald-500',
                                    'viewing_scheduled' => 'bg-blue-500',
                                    'closed' => 'bg-gray-500',
                                    default => 'bg-gray-300',
                                } }}">
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-bold text-base text-gray-900 dark:text-white truncate pr-2">
                                    {{ $record->property->title ?? 'Unknown Property' }}
                                </h3>
                                <span class="text-[10px] font-medium text-gray-400 shrink-0 mt-1">
                                    {{ $record->created_at->shortAbsoluteDiffForHumans() }}
                                </span>
                            </div>
                            
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300 truncate mt-0.5">
                                {{ $record->inquirer_name }}
                            </p>
                            
                            {{-- Status Pill --}}
                            <div class="mt-2 text-xs">
                                <span class="inline-flex items-center px-2 py-1 rounded-md font-medium
                                    {{ match ($record->status) {
                                        'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                                        'responded' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                        'viewing_scheduled' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                        'closed' => 'bg-gray-50 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400',
                                        default => 'bg-gray-50 text-gray-600',
                                    } }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Message Preview --}}
                    <div class="px-4 pb-4">
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-3">
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                "{{ $record->message }}"
                            </p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="grid grid-cols-2 border-t border-gray-100 dark:border-gray-800 divide-x divide-gray-100 dark:divide-gray-800">
                        <a href="tel:{{ $record->inquirer_phone }}" 
                           class="flex items-center justify-center gap-2 py-3.5 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors active:bg-gray-100 dark:active:bg-gray-800/80"
                           @click.stop>
                            <x-heroicon-m-phone class="w-4 h-4 text-emerald-500" />
                            Call
                        </a>
                        <a href="mailto:{{ $record->inquirer_email }}" 
                           class="flex items-center justify-center gap-2 py-3.5 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors active:bg-gray-100 dark:active:bg-gray-800/80"
                           @click.stop>
                            <x-heroicon-m-envelope class="w-4 h-4 text-blue-500" />
                            Email
                        </a>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 text-gray-400">
                        <x-heroicon-o-chat-bubble-bottom-center-text class="w-8 h-8" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">No Inquiries Found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-xs mx-auto">
                        Your search didn't match any inquiries. Try adjusting your filters.
                    </p>
                </div>
            @endforelse
            
            {{-- Pagination --}}
            <div class="pt-4 pb-8">
               <x-filament::pagination :paginator="$records" />
            </div>
        </div>
    </div>
</x-filament-panels::page>

