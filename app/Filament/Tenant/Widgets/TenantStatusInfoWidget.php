<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\TenantInvitation;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class TenantStatusInfoWidget extends Widget
{
    protected string $view = 'filament.tenant.widgets.tenant-status-info-widget';
    protected static ?int $sort = -2;
    protected int | string | array $columnSpan = 1;
    
    public $user;
    public $tenant;
    public $currentLease;

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->tenant = $this->user->tenant;
        
        // Get the current active lease for this tenant
        $this->currentLease = $this->tenant?->leases()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }
    
    public function getInvitationProperty()
    {
        // Get the accepted invitation for this user to check if it had a property
        return TenantInvitation::where('tenant_user_id', $this->user->id)
            ->where('status', 'accepted')
            ->with('property')
            ->first()?->property;
    }
}