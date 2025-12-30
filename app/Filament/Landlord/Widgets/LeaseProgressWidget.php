<?php

namespace App\Filament\Landlord\Widgets;

use Filament\Widgets\Widget;
use App\Models\Lease;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaseProgressWidget extends Widget
{
    protected string $view = 'filament.landlord.widgets.lease-progress-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function getData(): array
    {
        $landlordId = Auth::id();
        $now = now();

        $leases = Lease::where('landlord_id', $landlordId)
            ->where('status', 'active')
            ->with(['property', 'tenant'])
            ->orderBy('end_date', 'asc')
            ->get()
            ->map(function ($lease) use ($now) {
                $start = $lease->start_date ? Carbon::parse($lease->start_date) : null;
                $end = $lease->end_date ? Carbon::parse($lease->end_date) : null;
                
                if (!$start || !$end) {
                    $progress = 0;
                } else {
                    $totalDays = $start->diffInDays($end);
                    $elapsedDays = $start->diffInDays($now, false);
                    
                    if ($totalDays <= 0) {
                        $progress = 100;
                    } else {
                        $progress = min(100, max(0, ($elapsedDays / $totalDays) * 100));
                    }
                }

                return [
                    'id' => $lease->id,
                    'property_title' => $lease->property->title ?? 'Unknown Property',
                    'tenant_name' => $lease->tenant->name ?? 'Unknown Tenant',
                    'tenant_phone' => $lease->tenant->phone ?? 'N/A',
                    'start_date' => $start ? $start->format('M d, Y') : 'N/A',
                    'end_date' => $end ? $end->format('M d, Y') : 'N/A',
                    'progress' => round($progress, 1),
                    'days_remaining' => $end ? $now->diffInDays($end, false) : 0,
                    'is_expiring_soon' => $lease->isExpiringSoon(),
                ];
            });

        return [
            'leases' => $leases,
        ];
    }
}
