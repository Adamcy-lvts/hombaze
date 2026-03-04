<?php

namespace App\Livewire\Landing;

use Livewire\Component;
use App\Models\Property;

class FeaturedProperties extends Component
{
    public $properties;

    public function mount()
    {
        $this->properties = Property::published()
            ->featured()
            ->with(['propertyType', 'city.state', 'agent', 'agency', 'owner'])
            ->take(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.landing.featured-properties');
    }
}
