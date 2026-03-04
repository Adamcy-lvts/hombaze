@php
    use Filament\Facades\Filament;
    use App\Services\ListingCreditService;
    use App\Models\ListingCreditTransaction;
    use App\Models\ListingPackage;
    use App\Models\ListingAddon;

    $owner = ListingCreditService::resolveOwner(auth()->user(), Filament::getTenant());
    $account = $owner ? ListingCreditService::getAccount($owner) : null;
    $listingBalance = $owner ? ListingCreditService::getListingBalance($owner) : 0;
    $featuredBalance = $owner ? ListingCreditService::getFeaturedBalance($owner) : 0;

    $packageSlug = $account
        ? ListingCreditTransaction::where('listing_credit_account_id', $account->id)
            ->whereNotNull('package')
            ->latest()
            ->value('package')
        : null;

    $packageName = null;
    if ($packageSlug) {
        $packageName = ListingPackage::where('slug', $packageSlug)->value('name')
            ?? ListingAddon::where('slug', $packageSlug)->value('name');
    }
@endphp

<x-filament-widgets::widget class="fi-filament-info-widget">
    <x-filament::section>
        <div class="fi-filament-info-widget-main">
            <div class="fi-filament-info-widget-logo">Credits</div>
            <p class="fi-filament-info-widget-version">
                {{ $packageName ? $packageName . ' Package' : 'No package yet' }}
            </p>
        </div>

        <div class="fi-filament-info-widget-links">
            <span class="text-xs text-gray-500 dark:text-gray-400">
                Listing {{ $listingBalance }}
            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400">
                Featured {{ $featuredBalance }}
            </span>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
