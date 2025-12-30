<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">Lease Progress</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Track the duration and expiration of your active rental agreements</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="flex h-2 w-2 rounded-full bg-success-500"></span>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">Active Leases</span>
            </div>
        </div>

        @php
            $data = $this->getData();
            $leases = $data['leases'];
        @endphp

        @if($leases->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="rounded-full bg-gray-100 p-3 dark:bg-gray-800 mb-4">
                    <x-heroicon-o-document-text class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">No active leases found</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Start by creating a new lease agreement for your properties.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($leases as $lease)
                    <div class="relative group p-5 rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900/50 hover:shadow-xl hover:shadow-primary-500/5 transition-all duration-300">
                        <div class="flex justify-between items-start mb-4">
                            <div class="overflow-hidden">
                                <h4 class="text-base font-bold text-gray-900 dark:text-white truncate" title="{{ $lease['property_title'] }}">
                                    {{ $lease['property_title'] }}
                                </h4>
                                <div class="flex flex-col">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                        {{ $lease['tenant_name'] }}
                                    </p>
                                    <p class="text-xs text-primary-600 dark:text-primary-400 font-bold mt-0.5">
                                        {{ $lease['tenant_phone'] }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                                <span>Progress</span>
                                <span>{{ $lease['progress'] }}%</span>
                            </div>
                            
                            <div class="h-2.5 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $lease['is_expiring_soon'] ? 'bg-gradient-to-r from-danger-500 to-orange-500' : 'bg-gradient-to-r from-primary-500 to-success-500' }}"
                                     style="width: {{ $lease['progress'] }}%">
                                </div>
                            </div>

                            <div class="flex justify-between items-center pt-2">
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase font-bold tracking-tight">Started</span>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $lease['start_date'] }}</span>
                                </div>
                                <div class="h-6 w-px bg-gray-100 dark:bg-gray-800"></div>
                                <div class="flex flex-col text-right">
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase font-bold tracking-tight">Ends</span>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $lease['end_date'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Hover overlay effect -->
                        <div class="absolute inset-0 rounded-2xl bg-primary-500/0 group-hover:bg-primary-500/[0.02] transition-colors pointer-events-none"></div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
<style>
    @keyframes progress-shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    .progress-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
