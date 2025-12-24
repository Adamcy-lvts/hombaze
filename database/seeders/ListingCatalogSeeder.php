<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ListingPackage;
use App\Models\ListingAddon;

class ListingCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $packages = config('monetization.listing_packages', []);
        foreach ($packages as $slug => $package) {
            ListingPackage::updateOrCreate(
                ['slug' => $slug],
                array_merge(['slug' => $slug], $package)
            );
        }

        $addons = config('monetization.listing_addons', []);
        foreach ($addons as $slug => $addon) {
            ListingAddon::updateOrCreate(
                ['slug' => $slug],
                array_merge(['slug' => $slug], $addon)
            );
        }
    }
}
