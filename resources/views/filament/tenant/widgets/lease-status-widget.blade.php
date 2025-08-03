@php
    $lease = $this->currentLease;
    $tenant = $this->tenant;
    $user = $this->user;
    
    // Determine lease status and configuration
    if (!$lease) {
        $status = 'no_lease';
        $statusConfig = [
            'text' => 'No Active Lease',
            'classes' => 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:border-gray-600 dark:bg-gray-700 dark:bg-opacity-25 dark:text-gray-400',
        ];
        $statusMessage = 'No active lease found. Contact your landlord.';
    } else {
        $now = now();
        $endDate = $lease->end_date;
        $daysUntilExpiry = $now->diffInDays($endDate, false);
        
        if ($lease->status === 'active' && $endDate->isFuture()) {
            if ($daysUntilExpiry <= 30) {
                $status = 'expiring_soon';
                $statusConfig = [
                    'text' => 'Expiring Soon',
                    'classes' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:border-yellow-600 dark:bg-yellow-700 dark:bg-opacity-25 dark:text-yellow-400',
                ];
                $statusMessage = "Lease expires in {$daysUntilExpiry} " . ($daysUntilExpiry === 1 ? 'day' : 'days') . " on {$endDate->format('F j, Y')}";
            } else {
                $status = 'active';
                $statusConfig = [
                    'text' => 'Active',
                    'classes' => 'bg-green-50 text-green-700 ring-green-600/20 dark:border-green-600 dark:bg-green-700 dark:bg-opacity-25 dark:text-green-400',
                ];
                $statusMessage = "Lease expires on {$endDate->format('F j, Y')}";
            }
        } elseif ($lease->status === 'active' && $endDate->isPast()) {
            $status = 'expired';
            $statusConfig = [
                'text' => 'Expired',
                'classes' => 'bg-red-50 text-red-700 ring-red-600/20 dark:border-red-600 dark:bg-red-700 dark:bg-opacity-25 dark:text-red-400',
            ];
            $statusMessage = "Lease expired on {$endDate->format('F j, Y')}. Contact your landlord for renewal.";
        } elseif ($lease->status === 'terminated') {
            $status = 'terminated';
            $statusConfig = [
                'text' => 'Terminated',
                'classes' => 'bg-red-50 text-red-700 ring-red-600/20 dark:border-red-600 dark:bg-red-700 dark:bg-opacity-25 dark:text-red-400',
            ];
            $statusMessage = $lease->move_out_date ? "Lease terminated. Move-out date: {$lease->move_out_date->format('F j, Y')}" : 'Lease has been terminated.';
        } elseif ($lease->status === 'renewed') {
            $status = 'renewed';
            $statusConfig = [
                'text' => 'Renewed',
                'classes' => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:border-blue-600 dark:bg-blue-700 dark:bg-opacity-25 dark:text-blue-400',
            ];
            $statusMessage = "Lease has been renewed until {$endDate->format('F j, Y')}";
        } else {
            $status = 'draft';
            $statusConfig = [
                'text' => 'Draft',
                'classes' => 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:border-gray-600 dark:bg-gray-700 dark:bg-opacity-25 dark:text-gray-400',
            ];
            $statusMessage = 'Lease is in draft status. Awaiting finalization.';
        }
    }

@endphp

<x-filament-widgets::widget class="fi-lease-status-widget">
    <x-filament::section class="bg-white dark:bg-gray-800 rounded-lg shadow">
        @if ($lease)
            {{-- Show lease details --}}
            <div class="flex flex-col gap-0.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300 leading-5">Lease Status:</span>
                        <span class="text-sm font-semibold text-gray-800 dark:text-white">
                            {{ $lease->property->title ?? 'Property' }}
                        </span>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="inline-flex items-center rounded-lg px-4 py-2 text-xs font-medium {{ $statusConfig['classes'] }} ring-1 ring-inset">
                            {{ $statusConfig['text'] }}
                        </span>
                    </div>
                </div>

                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $statusMessage }}
                </span>

            </div>
        @else
            {{-- Show tenant status when no lease --}}
            <div class="flex flex-col">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300 leading-5">Tenant Status</span>
                    <span class="inline-flex items-center rounded-lg px-4 py-2 text-xs font-medium ring-1 ring-inset {{ $statusConfig['classes'] }}">
                        {{ $statusConfig['text'] }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $statusMessage }}
                </p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>