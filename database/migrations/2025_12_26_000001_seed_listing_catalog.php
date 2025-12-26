<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $packages = config('monetization.listing_packages', []);
        if ($packages) {
            $rows = [];
            foreach ($packages as $slug => $package) {
                $rows[] = [
                    'slug' => $slug,
                    'name' => $package['name'] ?? $slug,
                    'price' => $package['price'] ?? 0,
                    'listing_credits' => $package['listing_credits'] ?? 0,
                    'featured_credits' => $package['featured_credits'] ?? 0,
                    'featured_expires_days' => $package['featured_expires_days'] ?? null,
                    'max_active_listing_credits' => $package['max_active_listing_credits'] ?? null,
                    'is_active' => $package['is_active'] ?? true,
                    'sort_order' => $package['sort_order'] ?? 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('listing_packages')->upsert(
                $rows,
                ['slug'],
                [
                    'name',
                    'price',
                    'listing_credits',
                    'featured_credits',
                    'featured_expires_days',
                    'max_active_listing_credits',
                    'is_active',
                    'sort_order',
                    'updated_at',
                ]
            );
        }

        $addons = config('monetization.listing_addons', []);
        if ($addons) {
            $rows = [];
            foreach ($addons as $slug => $addon) {
                $rows[] = [
                    'slug' => $slug,
                    'name' => $addon['name'] ?? $slug,
                    'price' => $addon['price'] ?? 0,
                    'listing_credits' => $addon['listing_credits'] ?? 0,
                    'featured_credits' => $addon['featured_credits'] ?? 0,
                    'featured_expires_days' => $addon['featured_expires_days'] ?? null,
                    'is_active' => $addon['is_active'] ?? true,
                    'sort_order' => $addon['sort_order'] ?? 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('listing_addons')->upsert(
                $rows,
                ['slug'],
                [
                    'name',
                    'price',
                    'listing_credits',
                    'featured_credits',
                    'featured_expires_days',
                    'is_active',
                    'sort_order',
                    'updated_at',
                ]
            );
        }
    }

    public function down(): void
    {
        $packages = array_keys(config('monetization.listing_packages', []));
        if ($packages) {
            DB::table('listing_packages')->whereIn('slug', $packages)->delete();
        }

        $addons = array_keys(config('monetization.listing_addons', []));
        if ($addons) {
            DB::table('listing_addons')->whereIn('slug', $addons)->delete();
        }
    }
};
