<?php

namespace App\Filament\Tenant\Widgets;

use Filament\Widgets\Widget;
use App\Models\Lease;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaseProgressWidget extends Widget
{
    protected string $view = 'filament.tenant.widgets.lease-progress-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function getData(): array
    {
        $user = Auth::user();
        $tenant = $user?->tenant;

        if (!$tenant || !$tenant->landlord_id) {
            return [
                'leases' => collect(),
            ];
        }

        $now = now();

        $leases = Lease::where('tenant_id', $tenant->id)
            ->where('landlord_id', $tenant->landlord_id)
            ->whereIn('status', ['active', 'expired', 'terminated', 'renewed'])
            ->orderByRaw("case when status = 'active' then 0 else 1 end")
            ->orderBy('end_date', 'desc')
            ->with(['property'])
            ->get()
            ->map(function (Lease $lease) use ($now) {
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

                $daysRemaining = $end ? $now->diffInDays($end, false) : 0;

                return [
                    'id' => $lease->id,
                    'property_title' => $lease->property->title ?? 'Unknown Property',
                    'start_date' => $start ? $start->format('M d, Y') : 'N/A',
                    'end_date' => $end ? $end->format('M d, Y') : 'N/A',
                    'progress' => round($progress, 1),
                    'days_remaining' => (int) round($daysRemaining),
                    'is_expiring_soon' => $lease->isExpiringSoon(),
                    'status' => $lease->status,
                ];
            });

        return [
            'leases' => $leases,
        ];
    }
}
