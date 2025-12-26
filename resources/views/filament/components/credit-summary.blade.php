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
    $totalCredits = $listingBalance + $featuredBalance;

    $packageSlug = $account
        ? ListingCreditTransaction::where('listing_credit_account_id', $account->id)
            ->whereNotNull('package')
            ->where('reason', 'like', '%purchase%')
            ->latest()
            ->value('package')
        : null;

    $packageName = null;
    if ($packageSlug) {
        $packageName = ListingPackage::where('slug', $packageSlug)->value('name')
            ?? ListingAddon::where('slug', $packageSlug)->value('name');
    }
@endphp

<div class="mr-4 hidden lg:flex items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 dark:border-gray-700 dark:bg-gray-900/80 dark:text-gray-100">
    <span class="text-gray-500 dark:text-gray-400">Credits:</span>
    <span>{{ $totalCredits }}</span>
</div>
