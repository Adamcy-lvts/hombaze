<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Agency;
use App\Models\State;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class AgenciesPage extends Component
{
    use WithPagination;

    // Search and filter properties
    #[Url(as: 'search')]
    public $searchQuery = '';
    
    #[Url(as: 'location')]
    public $selectedLocation = '';
    
    #[Url(as: 'size')]
    public $agencySizeFilter = '';
    
    #[Url(as: 'sort')]
    public $sortBy = 'rating';

    public $showFilters = false;

    public function mount()
    {
        // Initialize any required data
    }

    public function updatedSearchQuery()
    {
        $this->resetPage();
    }

    public function updatedSelectedLocation()
    {
        $this->resetPage();
    }

    public function updatedAgencySizeFilter()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->searchQuery = '';
        $this->selectedLocation = '';
        $this->agencySizeFilter = '';
        $this->resetPage();
    }

    public function getAgenciesProperty()
    {
        $query = Agency::with(['state', 'city', 'agents', 'properties'])
            ->where('is_active', true)
            ->where('is_verified', true);

        // Apply search
        if (!empty($this->searchQuery)) {
            $query->where(function ($q) {
                $q->where('name', 'LIKE', "%{$this->searchQuery}%")
                  ->orWhere('description', 'LIKE', "%{$this->searchQuery}%")
                  ->orWhere('address', 'LIKE', "%{$this->searchQuery}%");
            });
        }

        // Apply location filter
        if (!empty($this->selectedLocation)) {
            $query->where('state_id', $this->selectedLocation);
        }

        // Apply agency size filter
        if (!empty($this->agencySizeFilter)) {
            switch ($this->agencySizeFilter) {
                case 'small':
                    $query->withCount('agents')
                          ->having('agents_count', '<=', 5);
                    break;
                case 'medium':
                    $query->withCount('agents')
                          ->having('agents_count', '>', 5)
                          ->having('agents_count', '<=', 20);
                    break;
                case 'large':
                    $query->withCount('agents')
                          ->having('agents_count', '>', 20);
                    break;
            }
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'rating':
                $query->orderByDesc('rating');
                break;
            case 'properties':
                $query->withCount('properties')
                      ->orderByDesc('properties_count');
                break;
            case 'agents':
                $query->withCount('agents')
                      ->orderByDesc('agents_count');
                break;
            case 'experience':
                $query->orderByDesc('years_in_business');
                break;
            case 'newest':
                $query->orderByDesc('created_at');
                break;
            default:
                $query->orderByDesc('rating');
        }

        return $query->paginate(12);
    }

    public function getStatsProperty()
    {
        return [
            'total_agencies' => Agency::where('is_active', true)
                                     ->where('is_verified', true)
                                     ->count(),
            'verified_agencies' => Agency::where('is_verified', true)->count(),
            'avg_rating' => Agency::where('is_active', true)->avg('rating'),
            'total_agents' => User::whereHas('agentProfile.agency', function ($q) {
                $q->where('is_active', true);
            })->count(),
        ];
    }

    public function getLocationOptionsProperty()
    {
        return State::withCount(['agencies' => function ($q) {
            $q->where('is_active', true);
        }])
        ->having('agencies_count', '>', 0)
        ->orderBy('name')
        ->get();
    }

    public function getFeaturedAgenciesProperty()
    {
        return Agency::with(['state', 'city', 'agents', 'properties'])
            ->where('is_active', true)
            ->where('is_verified', true)
            ->where('is_featured', true)
            ->orderByDesc('rating')
            ->limit(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.agencies-page', [
            'agencies' => $this->agencies,
            'stats' => $this->stats,
            'locationOptions' => $this->locationOptions,
            'featuredAgencies' => $this->featuredAgencies,
        ])->layout('layouts.livewire-property', ['title' => 'Find Trusted Real Estate Agencies']);
    }
}