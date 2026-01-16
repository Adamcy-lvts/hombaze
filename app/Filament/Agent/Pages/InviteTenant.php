<?php

namespace App\Filament\Agent\Pages;

use Filament\Pages\Page;
use App\Models\Property;
use App\Models\TenantInvitation;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class InviteTenant extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-plus';

    protected string $view = 'filament.agent.pages.invite-tenant';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Invite Tenant';

    public $phone = '';
    public $property_id = null;
    
    public $generatedLink = null;
    public $invitation = null;

    public function generateLink()
    {
        $this->validate([
            'phone' => ['required', 'string', 'regex:/^(\+234|234|0)[789][01]\d{8}$/'],
            'property_id' => 'nullable|exists:properties,id',
        ]);

        // Get agent profile
        $user = Auth::user();
        $agent = $user->agentProfile;

        $invitation = TenantInvitation::create([
            'phone' => $this->phone,
            'property_id' => $this->property_id,
            'landlord_id' => Auth::id(), // Using landlord_id field but this is actually the agent
            'agent_id' => $agent?->id,
            'expires_at' => now()->addDays(7),
            'status' => 'pending',
        ]);

        $this->invitation = $invitation;
        $this->generatedLink = $invitation->getInvitationUrl();

        Notification::make()
            ->title('Invitation Link Generated!')
            ->success()
            ->send();
    }

    public function resetForm()
    {
        $this->phone = '';
        $this->property_id = null;
        $this->generatedLink = null;
        $this->invitation = null;
    }

    public function getPropertiesProperty()
    {
        $user = Auth::user();
        $agent = $user->agentProfile;
        
        if (!$agent) {
            return collect();
        }

        // Get IDs of properties that have a pending invitation
        $pendingPropertyIds = TenantInvitation::where('landlord_id', Auth::id())
            ->where('status', 'pending')
            ->whereNotNull('property_id')
            ->where('expires_at', '>', now())
            ->pluck('property_id')
            ->toArray();

        // Return properties belonging to agent, excluding those with pending invites
        return Property::where('agent_id', $agent->id)
            ->whereNotIn('id', $pendingPropertyIds)
            ->pluck('title', 'id');
    }

    public function getRecentInvitationsProperty()
    {
        return TenantInvitation::where('landlord_id', Auth::id())
            ->with('property')
            ->latest()
            ->take(10)
            ->get();
    }
}
