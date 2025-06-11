<?php

namespace App\Livewire\Landing;

use Livewire\Component;
use App\Models\Property;

class FeaturedProperties extends Component
{
    public $properties;

    public function mount()
    {
        $this->properties = Property::where('is_published', true)
            ->where('is_featured', true)
            ->with(['propertyType', 'city.state'])
            ->take(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.landing.featured-properties');
    }
}
