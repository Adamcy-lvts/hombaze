<?php

namespace App\Http\Controllers;

use App\Models\ListingPackage;
use App\Models\ListingAddon;

class PricingController extends Controller
{
    public function __invoke()
    {
        return view('pricing.index', [
            'packages' => ListingPackage::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'addons' => ListingAddon::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
