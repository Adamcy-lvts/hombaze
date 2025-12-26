<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ListingPackage;
use App\Models\ListingAddon;
use App\Services\ListingCreditService;
use App\Models\ListingCreditTransaction;

use App\Models\SmartSearch;

class PricingPage extends Component
{
    public function render()
    {
        $owner = ListingCreditService::resolveOwner(auth()->user());
        $account = $owner ? ListingCreditService::getAccount($owner) : null;
        $currentPackageSlug = $account
            ? ListingCreditTransaction::where('listing_credit_account_id', $account->id)
                ->whereNotNull('package')
                ->whereIn('reason', ['self_service_purchase', 'self_service_free'])
                ->latest()
                ->value('package')
            : null;

        return view('livewire.pricing-page', [
            'packages' => ListingPackage::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'addons' => ListingAddon::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'currentPackageSlug' => $currentPackageSlug,
            'tiers' => SmartSearch::getTierOptions(),
        ])->layout('layouts.guest-app', ['title' => 'Pricing - HomeBaze']);
    }
}
