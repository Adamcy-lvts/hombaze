<?php

namespace App\Livewire\Landing;

use Livewire\Component;
use App\Models\PropertyType;
use App\Models\PropertySubtype;
use App\Models\State;
use App\Models\City;
use App\Models\Area;

class PropertySearch extends Component
{
    public $state_id = '';
    public $city_id = '';
    public $area_id = '';
    public $property_type_id = '';
    public $property_subtype_id = '';
    public $keyword = '';
    public $min_price = '';
    public $max_price = '';
    public $bedrooms = '';
    public $listing_type = '';

    public function updatedStateId()
    {
        $this->city_id = '';
        $this->area_id = '';
    }

    public function updatedCityId()
    {
        $this->area_id = '';
    }

    public function updatedPropertyTypeId()
    {
        $this->property_subtype_id = '';
    }

    public function search()
    {
        $params = array_filter([
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,
            'property_type_id' => $this->property_type_id,
            'property_subtype_id' => $this->property_subtype_id,
            'keyword' => $this->keyword,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'bedrooms' => $this->bedrooms,
            'listing_type' => $this->listing_type,
        ]);

        return redirect()->route('properties.search', $params);
    }

    public function render()
    {
        $propertyTypes = PropertyType::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        
        $propertySubtypes = [];
        if ($this->property_type_id) {
            $propertySubtypes = PropertySubtype::where('property_type_id', $this->property_type_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        $states = State::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        
        $cities = [];
        if ($this->state_id) {
            $cities = City::where('state_id', $this->state_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        $areas = [];
        if ($this->city_id) {
            $areas = Area::where('city_id', $this->city_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return view('livewire.landing.property-search', compact(
            'propertyTypes', 
            'propertySubtypes', 
            'states', 
            'cities', 
            'areas'
        ));
    }
}
