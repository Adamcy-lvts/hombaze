<x-filament-panels::page>
    <div class="flex flex-col space-y-6 pb-24">
        {{-- Header & Search --}}
        <div class="flex flex-col gap-4">
             <div class="flex items-center gap-4 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <div class="relative flex-1">
                    <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                    <input type="text" 
                           wire:model.live.debounce.500ms="tableSearch" 
                           placeholder="Search leases..." 
                           class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-800 border-none rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-primary-500/20">
                </div>
                 {{-- Filter Trigger --}}
                <x-filament::icon-button
                    icon="heroicon-m-funnel"
                    color="gray"
                    class="shrink-0"
                    wire:click="$toggle('tableFilters')"
                />
            </div>
        </div>

        {{-- Mobile Cards Loop --}}
        <div class="space-y-4">
            @php
                $records = $this->table->getRecords();
            @endphp

            @forelse($records as $record)
                <div class="flex flex-col bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden transition-all active:scale-[0.99]">
                    <div class="p-4">
                        {{-- Header: Property & Status --}}
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white line-clamp-1">
                                    {{ $record->property->title ?? 'Unknown Property' }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-1">
                                    <x-heroicon-o-user class="w-3 h-3" />
                                    {{ $record->tenant->name ?? 'Unknown Tenant' }}
                                </p>
                            </div>
                            <x-filament::badge 
                                :color="match ($record->status) {
                                    'draft' => 'gray',
                                    'active' => 'success',
                                    'expired' => 'danger',
                                    'terminated' => 'warning',
                                    'renewed' => 'info',
                                    default => 'gray',
                                }"
                                size="sm"
                            >
                                {{ ucfirst($record->status) }}
                            </x-filament::badge>
                        </div>

                        {{-- Financials --}}
                        <div class="flex items-center justify-between py-3 border-t border-b border-gray-50 dark:border-gray-800 my-2">
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Rent</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400 text-base">
                                    ₦{{ number_format($record->yearly_rent) }}
                                </span>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Frequency</span>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ ucfirst($record->payment_frequency) }}
                                </span>
                            </div>
                        </div>

                        {{-- Dates --}}
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mt-2 bg-gray-50 dark:bg-gray-800/50 p-2 rounded-lg">
                            <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" />
                            <div class="flex items-center gap-2 w-full justify-between">
                                <span>{{ $record->start_date?->format('M d, Y') }}</span>
                                <x-heroicon-m-arrow-right class="w-3 h-3 text-gray-300" />
                                <span>{{ $record->end_date?->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="grid grid-cols-2 border-t border-gray-100 dark:border-gray-800 divide-x divide-gray-100 dark:border-gray-800">
                        <a href="{{ route('filament.agent.resources.leases.view', $record) }}" 
                           class="flex items-center justify-center gap-2 py-3 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <x-heroicon-m-eye class="w-4 h-4 text-primary-500" />
                            View
                        </a>
                        <a href="{{ route('filament.agent.resources.leases.edit', $record) }}"
                           class="flex items-center justify-center gap-2 py-3 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <x-heroicon-m-pencil-square class="w-4 h-4 text-gray-400" />
                            Edit
                        </a>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 text-gray-400">
                        <x-heroicon-o-document-text class="w-8 h-8" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">No Leases Found</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-xs mx-auto">
                        You haven't created any lease agreements yet.
                    </p>
                    <a href="{{ route('filament.agent.resources.leases.create') }}" class="mt-4 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium">Create Lease</a>
                </div>
            @endforelse

             <div class="pt-4 pb-8">
               <x-filament::pagination :paginator="$records" />
            </div>
        </div>
    </div>
</x-filament-panels::page>
