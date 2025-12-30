@php
    $lease = $this->currentLease;
    $tenant = $this->tenant;
    $user = $this->user;
    
    // Check for property from invitation if no active lease
    $invitationProperty = $this->getInvitationProperty();
    
    // Determine status colors and labels
    if (!$lease) {
        $statusLabel = $invitationProperty ? 'Awaiting Lease' : 'No Lease';
        $statusColor = $invitationProperty ? 'info' : 'gray';
        $propertyName = $invitationProperty ? $invitationProperty->title : 'No Property Assigned';
    } else {
        $endDate = $lease->end_date;
        $daysUntilExpiry = now()->diffInDays($endDate, false);
        $propertyName = $lease->property->title ?? 'Property';
        
        if ($lease->status === 'active' && $endDate->isFuture()) {
            $statusLabel = $daysUntilExpiry <= 30 ? 'Expiring Soon' : 'Active';
            $statusColor = $daysUntilExpiry <= 30 ? 'warning' : 'success';
        } elseif ($lease->status === 'active' && $endDate->isPast()) {
            $statusLabel = 'Expired';
            $statusColor = 'danger';
        } else {
            $statusLabel = ucfirst($lease->status);
            $statusColor = match($lease->status) {
                'terminated' => 'danger',
                'renewed' => 'info',
                default => 'gray',
            };
        }
    }
@endphp

<x-filament-widgets::widget class="fi-tenant-status-info-widget">
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            <div class="flex-none">
                <div @class([
                    'flex h-10 w-10 items-center justify-center rounded-full transition-colors',
                    'bg-success-500/10 text-success-600 dark:text-success-400' => $statusColor === 'success',
                    'bg-warning-500/10 text-warning-600 dark:text-warning-400' => $statusColor === 'warning',
                    'bg-danger-500/10 text-danger-600 dark:text-danger-400' => $statusColor === 'danger',
                    'bg-info-500/10 text-info-600 dark:text-info-400' => $statusColor === 'info',
                    'bg-gray-500/10 text-gray-600 dark:text-gray-400' => $statusColor === 'gray',
                ])>
                    <x-heroicon-o-home-modern class="h-5 w-5" />
                </div>
            </div>

            <div class="flex-1 min-w-0">
                <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-tight">
                    Lease Status
                </h2>
                <p class="text-sm font-bold text-gray-950 dark:text-white truncate">
                    {{ $propertyName }}
                </p>
            </div>

            <div class="flex-none flex items-center gap-x-2">
                <x-filament::badge :color="$statusColor" size="sm">
                    {{ $statusLabel }}
                </x-filament::badge>
                
                @if($lease)
                    <a href="{{ \App\Filament\Tenant\Resources\LeaseResource::getUrl('view', ['record' => $lease->id]) }}" 
                       class="p-1 text-gray-400 hover:text-primary-600 transition-colors dark:hover:text-primary-400"
                       title="View Lease Details">
                        <x-heroicon-m-arrow-top-right-on-square class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>