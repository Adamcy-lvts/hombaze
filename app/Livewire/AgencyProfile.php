<?php

namespace App\Livewire;

use App\Models\Agency;
use Illuminate\View\View;
use Livewire\Component;

class AgencyProfile extends Component
{
    public Agency $agency;

    public function mount(Agency $agency): void
    {
        if (!$agency->is_active) {
            abort(404, 'Agency not found');
        }

        $this->agency = $agency->loadMissing(['state', 'city', 'area']);
    }

    public function getActiveAgentsProperty(): int
    {
        return $this->agency->agents()
            ->whereHas('user', fn ($query) => $query->where('is_active', true))
            ->count();
    }

    public function getActiveListingsProperty(): int
    {
        return $this->agency->properties()->published()->count();
    }

    public function getTopAgentsProperty()
    {
        return $this->agency->agents()
            ->with('user')
            ->withCount(['properties' => fn ($query) => $query->published()])
            ->orderByDesc('rating')
            ->limit(6)
            ->get();
    }

    public function getRecentPropertiesProperty()
    {
        return $this->agency->properties()
            ->published()
            ->with(['propertyType', 'city', 'state'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.agency-profile', [
            'activeAgents' => $this->activeAgents,
            'activeListings' => $this->activeListings,
            'topAgents' => $this->topAgents,
            'recentProperties' => $this->recentProperties,
        ])->layout('layouts.guest-app', ['title' => $this->agency->name . ' - Real Estate Agency']);
    }
}
