<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Agency;
use App\Models\Property;
use App\Models\State;
use Livewire\Component;

class AboutPage extends Component
{
    public function getStatsProperty()
    {
        return [
            'total_users' => User::count(),
            'total_agencies' => Agency::where('is_active', true)->count(),
            'total_properties' => Property::where('is_active', true)->count(),
            'total_locations' => State::withCount(['properties' => function ($q) {
                $q->where('is_active', true);
            }])->having('properties_count', '>', 0)->count(),
            'verified_agencies' => Agency::where('is_verified', true)->count(),
            'total_agents' => User::whereHas('agentProfile', function ($q) {
                $q->where('is_active', true);
            })->count(),
        ];
    }

    public function getTeamMembersProperty()
    {
        // Return team members data (you can replace this with actual team data from database)
        return [
            [
                'name' => 'Adebayo Johnson',
                'position' => 'Chief Executive Officer',
                'bio' => 'With over 15 years in Nigerian real estate, Adebayo leads HomeBaze\'s vision to transform property transactions across Nigeria.',
                'image' => null,
                'linkedin' => '#',
                'twitter' => '#'
            ],
            [
                'name' => 'Kemi Adebola',
                'position' => 'Chief Technology Officer',
                'bio' => 'Tech innovator with expertise in PropTech solutions, leading our platform development and digital transformation initiatives.',
                'image' => null,
                'linkedin' => '#',
                'twitter' => '#'
            ],
            [
                'name' => 'Ibrahim Musa',
                'position' => 'Head of Operations',
                'bio' => 'Operations expert ensuring seamless property transactions and exceptional customer experiences across all Nigerian markets.',
                'image' => null,
                'linkedin' => '#',
                'twitter' => '#'
            ]
        ];
    }

    public function render()
    {
        return view('livewire.about-page', [
            'stats' => $this->stats,
            'teamMembers' => $this->teamMembers,
        ])->layout('layouts.livewire-property', ['title' => 'About HomeBaze - Premier Real Estate Platform']);
    }
}