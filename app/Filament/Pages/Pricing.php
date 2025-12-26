<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;
use BackedEnum;
use App\Models\ListingAddon;
use App\Models\ListingPackage;
use Filament\Facades\Filament;
use App\Services\ListingCreditService;
use App\Models\ListingCreditTransaction;

class Pricing extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    protected static string|UnitEnum|null $navigationGroup = 'Billing';
    protected static ?string $navigationLabel = 'Pricing';
    protected static ?string $title = 'Pricing';
    protected static ?string $slug = 'pricing';

    protected string $view = 'filament.pages.pricing';

    protected function getViewData(): array
    {
        $owner = ListingCreditService::resolveOwner(auth()->user(), Filament::getTenant());
        $account = $owner ? ListingCreditService::getAccount($owner) : null;
        $currentPackageSlug = $account
            ? ListingCreditTransaction::where('listing_credit_account_id', $account->id)
                ->whereNotNull('package')
                ->whereIn('reason', ['self_service_purchase', 'self_service_free'])
                ->latest()
                ->value('package')
            : null;

        return [
            'packages' => ListingPackage::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'addons' => ListingAddon::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'currentPackageSlug' => $currentPackageSlug,
        ];
    }
}
