<?php

namespace App\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use App\Services\ListingCreditService;
use App\Models\ListingCreditTransaction;
use App\Models\ListingPackage;
use App\Models\ListingAddon;

class CreditStatusWidget extends Widget
{
    protected string $view = 'filament.widgets.credit-status-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $owner = ListingCreditService::resolveOwner(auth()->user(), Filament::getTenant());

        if (!$owner) {
            return [
                'packageName' => null,
                'listingBalance' => 0,
                'featuredBalance' => 0,
                'featuredExpiresAt' => null,
            ];
        }

        $account = ListingCreditService::getAccount($owner);
        $listingBalance = ListingCreditService::getListingBalance($owner);
        $featuredBalance = ListingCreditService::getFeaturedBalance($owner);

        $packageSlug = $account
            ? ListingCreditTransaction::where('listing_credit_account_id', $account->id)
                ->whereNotNull('package')
                ->whereIn('reason', ['self_service_purchase', 'self_service_free', 'package_purchase', 'addon_purchase'])
                ->latest()
                ->value('package')
            : null;

        $packageName = null;
        if ($packageSlug) {
            $packageName = ListingPackage::where('slug', $packageSlug)->value('name')
                ?? ListingAddon::where('slug', $packageSlug)->value('name');
        }

        return [
            'packageName' => $packageName,
            'listingBalance' => $listingBalance,
            'featuredBalance' => $featuredBalance,
            'featuredExpiresAt' => $account?->featured_expires_at,
        ];
    }
}
