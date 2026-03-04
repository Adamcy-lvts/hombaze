<?php

namespace App\Livewire\Listing;

use Livewire\Component;
use App\Models\PropertyOwner;
use Illuminate\Support\Facades\Auth;

class MyListings extends Component
{
    public function getListingsProperty()
    {
        $user = Auth::user();
        $propertyOwner = $user->propertyOwnerProfile;

        if (!$propertyOwner) {
            return collect();
        }

        return $propertyOwner->properties()
            ->with(['media', 'city', 'state'])
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.listing.my-listings')->layout('layouts.guest-app');
    }
}
