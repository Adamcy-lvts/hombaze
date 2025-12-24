<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ListingPackage;
use App\Models\ListingAddon;

class PricingPage extends Component
{
    public function render()
    {
        return view('livewire.pricing-page', [
            'packages' => ListingPackage::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'addons' => ListingAddon::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ])->layout('layouts.guest-app', ['title' => 'Pricing - HomeBaze']);
    }
}
