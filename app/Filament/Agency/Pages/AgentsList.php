<?php

namespace App\Filament\Agency\Pages;

use Filament\Pages\Page;
use App\Models\Agent;
use Filament\Facades\Filament;
use Livewire\WithPagination;

class AgentsList extends Page
{
    use WithPagination;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected string $view = 'filament.agency.pages.agents-list';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'agents-list';

    protected static ?string $title = 'Agents';

    public ?Agent $selectedAgent = null;
    public $search = '';

    public function viewAgent($id)
    {
        $agency = Filament::getTenant();
        
        $this->selectedAgent = Agent::where('agency_id', $agency?->id)
            ->with(['user', 'properties'])
            ->find($id);
    }

    public function closeAgentView()
    {
        $this->selectedAgent = null;
    }

    public function getAgentsProperty()
    {
        $agency = Filament::getTenant();
        
        if (!$agency) {
            return collect();
        }

        return Agent::query()
            ->where('agency_id', $agency->id)
            ->with(['user', 'properties'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
