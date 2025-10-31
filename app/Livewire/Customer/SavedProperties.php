<?php

namespace App\Livewire\Customer;

use App\Models\SavedProperty;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class SavedProperties extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public $search = '';

    #[Url(as: 'type')]
    public $propertyType = '';

    #[Url(as: 'sort')]
    public $sortBy = 'saved_date';

    public function mount()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPropertyType()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function removeSavedProperty($savedPropertyId)
    {
        $savedProperty = SavedProperty::where('id', $savedPropertyId)
            ->where('user_id', auth()->id())
            ->first();

        if ($savedProperty) {
            $savedProperty->delete();
            $this->dispatch('property-removed', 'Property removed from saved list');
        }
    }

    public function getStats()
    {
        $userId = auth()->id();

        return [
            'total' => SavedProperty::where('user_id', $userId)->count(),
            'rent' => SavedProperty::where('user_id', $userId)
                ->whereHas('property', fn($q) => $q->where('listing_type', 'rent'))->count(),
            'sale' => SavedProperty::where('user_id', $userId)
                ->whereHas('property', fn($q) => $q->where('listing_type', 'sale'))->count(),
        ];
    }

    public function getSavedProperties()
    {
        $query = SavedProperty::where('user_id', auth()->id())
            ->with(['property.area', 'property.city', 'property.propertyType']);

        // Search filter
        if ($this->search) {
            $query->whereHas('property', function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhereHas('area', fn($area) => $area->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('city', fn($city) => $city->where('name', 'like', '%' . $this->search . '%'));
            });
        }

        // Property type filter
        if ($this->propertyType) {
            $query->whereHas('property', fn($q) => $q->where('listing_type', $this->propertyType));
        }

        // Sorting
        switch ($this->sortBy) {
            case 'price_low':
                $query->join('properties', 'saved_properties.property_id', '=', 'properties.id')
                      ->orderBy('properties.price', 'asc')
                      ->select('saved_properties.*');
                break;
            case 'price_high':
                $query->join('properties', 'saved_properties.property_id', '=', 'properties.id')
                      ->orderBy('properties.price', 'desc')
                      ->select('saved_properties.*');
                break;
            case 'title':
                $query->join('properties', 'saved_properties.property_id', '=', 'properties.id')
                      ->orderBy('properties.title', 'asc')
                      ->select('saved_properties.*');
                break;
            default: // saved_date
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate(9);
    }

    public function render()
    {
        return view('livewire.customer.saved-properties', [
            'savedProperties' => $this->getSavedProperties(),
            'stats' => $this->getStats(),
        ])->extends('layouts.property')->section('content');
    }
}
