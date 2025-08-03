@php
    $lease = $this->currentLease;
    $tenant = $this->tenant;
    $user = $this->user;
    
    // Check for property from invitation if no active lease
    $invitationProperty = $this->getInvitationProperty();
    
    // Determine lease status and configuration
    if (!$lease) {
        $status = 'no_lease';
        $statusConfig = [
            'text' => $invitationProperty ? 'Awaiting Lease' : 'No Active Lease',
            'classes' => $invitationProperty ? 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:border-blue-600 dark:bg-blue-700 dark:bg-opacity-25 dark:text-blue-400' : 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:border-gray-600 dark:bg-gray-700 dark:bg-opacity-25 dark:text-gray-400',
        ];
        $statusMessage = $invitationProperty ? 'Invited to property. Awaiting lease creation by landlord.' : 'No active lease found. Contact your landlord for lease assignment.';
        $propertyName = $invitationProperty ? $invitationProperty->title : 'No Property Assigned';
    } else {
        $now = now();
        $endDate = $lease->end_date;
        $daysUntilExpiry = $now->diffInDays($endDate, false);
        $propertyName = $lease->property->title ?? 'Property';
        
        if ($lease->status === 'active' && $endDate->isFuture()) {
            if ($daysUntilExpiry <= 30) {
                $status = 'expiring_soon';
                $statusConfig = [
                    'text' => 'Expiring Soon',
                    'classes' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:border-yellow-600 dark:bg-yellow-700 dark:bg-opacity-25 dark:text-yellow-400',
                ];
                $statusMessage = "Expires on {$endDate->format('F j, Y')} ({$daysUntilExpiry} " . ($daysUntilExpiry === 1 ? 'day' : 'days') . " remaining)";
            } else {
                $status = 'active';
                $statusConfig = [
                    'text' => 'Active',
                    'classes' => 'bg-green-50 text-green-700 ring-green-600/20 dark:border-green-600 dark:bg-green-700 dark:bg-opacity-25 dark:text-green-400',
                ];
                $statusMessage = "Expires on {$endDate->format('F j, Y')}";
            }
        } elseif ($lease->status === 'active' && $endDate->isPast()) {
            $status = 'expired';
            $statusConfig = [
                'text' => 'Expired',
                'classes' => 'bg-red-50 text-red-700 ring-red-600/20 dark:border-red-600 dark:bg-red-700 dark:bg-opacity-25 dark:text-red-400',
            ];
            $statusMessage = "Expired on {$endDate->format('F j, Y')}. Contact landlord for renewal.";
        } elseif ($lease->status === 'terminated') {
            $status = 'terminated';
            $statusConfig = [
                'text' => 'Terminated',
                'classes' => 'bg-red-50 text-red-700 ring-red-600/20 dark:border-red-600 dark:bg-red-700 dark:bg-opacity-25 dark:text-red-400',
            ];
            $statusMessage = $lease->move_out_date ? "Terminated. Move-out: {$lease->move_out_date->format('F j, Y')}" : 'Lease terminated.';
        } elseif ($lease->status === 'renewed') {
            $status = 'renewed';
            $statusConfig = [
                'text' => 'Renewed',
                'classes' => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:border-blue-600 dark:bg-blue-700 dark:bg-opacity-25 dark:text-blue-400',
            ];
            $statusMessage = "Renewed until {$endDate->format('F j, Y')}";
        } else {
            $status = 'draft';
            $statusConfig = [
                'text' => 'Draft',
                'classes' => 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:border-gray-600 dark:bg-gray-700 dark:bg-opacity-25 dark:text-gray-400',
            ];
            $statusMessage = 'Draft status. Awaiting finalization.';
        }
    }

    $isActiveLease = $status === 'active' || $status === 'expiring_soon';
@endphp

<x-filament-widgets::widget class="fi-filament-info-widget">
    <x-filament::section class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="flex flex-col gap-0.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300 leading-5">
                        {{ $isActiveLease ? 'Lease Status' : 'Tenant Status' }}:
                    </span>
                    <span class="text-sm font-semibold text-gray-800 dark:text-white">
                        {{ $propertyName }}
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
    </x-filament::section>
</x-filament-widgets::widget>