<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Agent;
use App\Models\State;
use App\Models\PropertyType;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class AgentsPage extends Component
{
    use WithPagination;

    // Search and filter properties
    #[Url(as: 'search')]
    public $searchQuery = '';

    #[Url(as: 'location')]
    public $selectedLocation = '';

    #[Url(as: 'experience')]
    public $experienceFilter = '';

    #[Url(as: 'specialization')]
    public $specializationFilter = '';

    #[Url(as: 'sort')]
    public $sortBy = 'name';

    public $showFilters = false;
    public $selectedAgentTypes = [];
    public $selectedExperience = [];
    public $selectedRating = null;
    public $verifiedOnly = false;

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

    public function updatedExperienceFilter()
    {
        $this->resetPage();
    }

    public function updatedSpecializationFilter()
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
        $this->experienceFilter = '';
        $this->specializationFilter = '';
        $this->resetPage();
    }

    public function clearAllFilters()
    {
        $this->searchQuery = '';
        $this->selectedLocation = '';
        $this->experienceFilter = '';
        $this->specializationFilter = '';
        $this->selectedAgentTypes = [];
        $this->selectedExperience = [];
        $this->selectedRating = null;
        $this->verifiedOnly = false;
        $this->resetPage();
    }

    public function toggleFilter($type, $value)
    {
        if ($type === 'rating') {
            $this->selectedRating = $this->selectedRating == $value ? null : $value;
        }
        $this->resetPage();
    }

    public function getAgentsProperty()
    {
        $query = User::select('users.*')
            ->with(['agentProfile.agency', 'agentProfile.reviews'])
            ->whereHas('agentProfile', function ($q) {
                $q->where('is_available', true);
            })
            ->where(function ($q) {
                // Include independent agents (no agency)
                $q->whereDoesntHave('agentProfile.agency')
                  // Or include agents with active agencies
                  ->orWhereHas('agentProfile.agency', function ($agencyQuery) {
                      $agencyQuery->where('is_active', true);
                  });
            });

        // Apply search
        if (!empty($this->searchQuery)) {
            $query->where(function ($q) {
                $q->where('name', 'LIKE', "%{$this->searchQuery}%")
                  ->orWhereHas('agentProfile', function ($agentQuery) {
                      $agentQuery->where('bio', 'LIKE', "%{$this->searchQuery}%")
                                 ->orWhere('specializations', 'LIKE', "%{$this->searchQuery}%");
                  });
            });
        }

        // Apply location filter
        if (!empty($this->selectedLocation)) {
            $query->where(function ($q) {
                // Filter by agency location for agency agents
                $q->whereHas('agentProfile.agency', function ($agencyQuery) {
                    $agencyQuery->where('state_id', $this->selectedLocation)
                               ->orWhere('city_id', $this->selectedLocation);
                })
                // Or filter by agent's personal location for independent agents
                ->orWhereHas('agentProfile', function ($agentQuery) {
                    $agentQuery->where('state_id', $this->selectedLocation)
                              ->orWhere('city_id', $this->selectedLocation);
                });
            });
        }

        // Apply experience filter
        if (!empty($this->experienceFilter)) {
            switch ($this->experienceFilter) {
                case 'junior':
                    $query->whereHas('agentProfile', function ($q) {
                        $q->where('years_experience', '<', 3);
                    });
                    break;
                case 'mid':
                    $query->whereHas('agentProfile', function ($q) {
                        $q->whereBetween('years_experience', [3, 7]);
                    });
                    break;
                case 'senior':
                    $query->whereHas('agentProfile', function ($q) {
                        $q->where('years_experience', '>=', 8);
                    });
                    break;
            }
        }

        // Apply specialization filter
        if (!empty($this->specializationFilter)) {
            $query->whereHas('agentProfile', function ($q) {
                $q->where('specializations', 'LIKE', "%{$this->specializationFilter}%");
            });
        }

        // Apply new filter types - agent type
        if (!empty($this->selectedAgentTypes)) {
            $query->where(function ($q) {
                if (in_array('independent', $this->selectedAgentTypes)) {
                    $q->whereDoesntHave('agentProfile.agency');
                }
                if (in_array('agency', $this->selectedAgentTypes)) {
                    $q->orWhereHas('agentProfile.agency');
                }
            });
        }

        // Apply experience level filters
        if (!empty($this->selectedExperience)) {
            $query->where(function ($q) {
                foreach ($this->selectedExperience as $experience) {
                    switch ($experience) {
                        case '0-2':
                            $q->orWhereHas('agentProfile', function ($agentQuery) {
                                $agentQuery->whereBetween('years_experience', [0, 2]);
                            });
                            break;
                        case '3-5':
                            $q->orWhereHas('agentProfile', function ($agentQuery) {
                                $agentQuery->whereBetween('years_experience', [3, 5]);
                            });
                            break;
                        case '5+':
                            $q->orWhereHas('agentProfile', function ($agentQuery) {
                                $agentQuery->where('years_experience', '>', 5);
                            });
                            break;
                    }
                }
            });
        }

        // Apply rating filter
        if ($this->selectedRating) {
            $query->whereHas('agentProfile', function ($q) {
                $q->where('average_rating', '>=', $this->selectedRating);
            });
        }

        // Apply verified filter
        if ($this->verifiedOnly) {
            $query->whereHas('agentProfile', function ($q) {
                $q->where('is_verified', true);
            });
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'name':
                $query->orderBy('users.name');
                break;
            case 'rating':
                $query->orderByDesc(
                    Agent::select('average_rating')
                        ->whereColumn('agents.user_id', 'users.id')
                        ->limit(1)
                );
                break;
            case 'experience':
                $query->orderByDesc(
                    Agent::select('years_experience')
                        ->whereColumn('agents.user_id', 'users.id')
                        ->limit(1)
                );
                break;
            case 'listings':
                $query->orderByDesc(
                    Agent::select('properties_count')
                        ->whereColumn('agents.user_id', 'users.id')
                        ->limit(1)
                );
                break;
            case 'newest':
                $query->orderByDesc('users.created_at');
                break;
            default:
                $query->orderBy('users.name');
        }

        return $query->paginate(12);
    }

    public function getStatsProperty()
    {
        return [
            'total_agents' => User::whereHas('agentProfile', function ($q) {
                $q->where('is_available', true)->where('is_verified', true);
            })->count(),
            'verified_agents' => User::whereHas('agentProfile', function ($q) {
                $q->where('is_verified', true);
            })->count(),
            'avg_experience' => Agent::where('is_verified', true)->avg('years_experience'),
            'total_properties' => \App\Models\Property::whereHas('agent')->count(),
        ];
    }

    public function getLocationOptionsProperty()
    {
        return State::withCount(['agencies' => function ($q) {
            $q->where('is_active', true)
              ->whereHas('agents', function ($agentQuery) {
                  $agentQuery->where('is_available', true)
                            ->where('is_verified', true);
              });
        }])
        ->having('agencies_count', '>', 0)
        ->orderBy('name')
        ->get();
    }

    public function getFeaturedAgentsProperty()
    {
        return User::with(['agentProfile.agency', 'agentProfile.reviews'])
            ->whereHas('agentProfile', function ($q) {
                $q->where('is_available', true)
                  ->where('is_verified', true)
                  ->where('is_featured', true);
            })
            ->orderByDesc(
                Agent::select('rating')
                    ->whereColumn('agents.user_id', 'users.id')
                    ->limit(1)
            )
            ->limit(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.agents-page', [
            'agents' => $this->agents,
            'stats' => $this->stats,
            'locationOptions' => $this->locationOptions,
            'featuredAgents' => $this->featuredAgents,
        ])->layout('layouts.livewire-property', ['title' => 'Find Trusted Real Estate Agents']);
    }
}