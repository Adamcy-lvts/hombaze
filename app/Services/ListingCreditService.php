<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\ListingCreditAccount;
use App\Models\ListingCreditTransaction;
use Illuminate\Validation\ValidationException;
use App\Models\ListingPackage;
use App\Models\ListingAddon;

class ListingCreditService
{
    public static function resolveOwner(?Model $user, ?Model $tenant = null): ?Model
    {
        if ($tenant) {
            return $tenant;
        }

        if (!$user) {
            return null;
        }

        if (method_exists($user, 'user_type')) {
            if ($user->user_type === 'agent' && method_exists($user, 'firstAgency')) {
                $agency = $user->firstAgency();
                if ($agency) {
                    return $agency;
                }
            }

            if ($user->user_type === 'agency_owner' && method_exists($user, 'ownedAgencies')) {
                $agency = $user->ownedAgencies()->first();
                if ($agency) {
                    return $agency;
                }
            }
        }

        return $user;
    }

    public static function getAccount(Model $owner): ?ListingCreditAccount
    {
        return ListingCreditAccount::where('owner_type', $owner->getMorphClass())
            ->where('owner_id', $owner->getKey())
            ->first();
    }

    public static function getListingBalance(Model $owner): int
    {
        $account = static::getAccount($owner);
        if (!$account) {
            return 0;
        }

        return (int) $account->listing_balance;
    }

    public static function getFeaturedBalance(Model $owner): int
    {
        $account = static::getAccount($owner);
        if (!$account) {
            return 0;
        }

        if ($account->featured_expires_at && $account->featured_expires_at->isPast()) {
            return 0;
        }

        return (int) $account->featured_balance;
    }

    public static function assertHasListingCredits(Model $owner, int $required = null): void
    {
        $required = $required ?? static::listingCost();
        if (static::getListingBalance($owner) < $required) {
            throw ValidationException::withMessages([
                'listing_type' => 'Insufficient credits to publish this property.',
            ]);
        }
    }

    public static function assertHasFeaturedCredits(Model $owner, int $required = null): void
    {
        $required = $required ?? static::featuredCost();
        if (static::getFeaturedBalance($owner) < $required) {
            throw ValidationException::withMessages([
                'is_featured' => 'Insufficient credits to feature this property.',
            ]);
        }
    }

    public static function consumeListingCredits(Model $owner, Property $property, int $required = null): void
    {
        $required = $required ?? static::listingCost();

        DB::transaction(function () use ($owner, $property, $required) {
            $account = ListingCreditAccount::where('owner_type', $owner->getMorphClass())
                ->where('owner_id', $owner->getKey())
                ->lockForUpdate()
                ->first();

            if (!$account || $account->listing_balance < $required) {
                throw ValidationException::withMessages([
                    'listing_type' => 'Insufficient credits to publish this property.',
                ]);
            }

            $account->decrement('listing_balance', $required);

            ListingCreditTransaction::create([
                'listing_credit_account_id' => $account->id,
                'property_id' => $property->id,
                'credit_type' => 'listing',
                'credits' => -1 * $required,
                'reason' => 'listing_publish',
            ]);
        });

        $property->update([
            'listing_fee_status' => Property::LISTING_FEE_PAID,
            'listing_paid_at' => $property->listing_paid_at ?? now(),
        ]);
    }

    public static function consumeFeaturedCredits(Model $owner, Property $property, int $required = null): void
    {
        $required = $required ?? static::featuredCost();

        DB::transaction(function () use ($owner, $property, $required) {
            $account = ListingCreditAccount::where('owner_type', $owner->getMorphClass())
                ->where('owner_id', $owner->getKey())
                ->lockForUpdate()
                ->first();

            if (
                !$account ||
                ($account->featured_expires_at && $account->featured_expires_at->isPast()) ||
                $account->featured_balance < $required
            ) {
                throw ValidationException::withMessages([
                    'is_featured' => 'Insufficient credits to feature this property.',
                ]);
            }

            $account->decrement('featured_balance', $required);

            ListingCreditTransaction::create([
                'listing_credit_account_id' => $account->id,
                'property_id' => $property->id,
                'credit_type' => 'featured',
                'credits' => -1 * $required,
                'reason' => 'listing_featured',
            ]);
        });
    }

    public static function grantPackage(Model $owner, ListingPackage $package, ?string $reason = null): void
    {
        DB::transaction(function () use ($owner, $package, $reason) {
            $account = ListingCreditAccount::firstOrCreate(
                [
                    'owner_type' => $owner->getMorphClass(),
                    'owner_id' => $owner->getKey(),
                ],
                [
                    'listing_balance' => 0,
                    'featured_balance' => 0,
                ]
            );

            $listingCredits = (int) ($package->listing_credits ?? 0);
            $featuredCredits = (int) ($package->featured_credits ?? 0);
            $featuredExpiresDays = (int) ($package->featured_expires_days ?? 0);
            $maxActiveListingCredits = $package->max_active_listing_credits;

            if ($maxActiveListingCredits && $account->listing_balance >= $maxActiveListingCredits) {
                throw ValidationException::withMessages([
                    'bundle' => 'This package is limited to a fixed number of active listing credits.',
                ]);
            }

            if ($listingCredits > 0) {
                $account->increment('listing_balance', $listingCredits);
                ListingCreditTransaction::create([
                    'listing_credit_account_id' => $account->id,
                    'credit_type' => 'listing',
                    'credits' => $listingCredits,
                    'package' => $package->slug,
                    'reason' => $reason ?? 'package_purchase',
                ]);
            }

            if ($featuredCredits > 0) {
                $account->increment('featured_balance', $featuredCredits);

                if ($featuredExpiresDays > 0) {
                    $baseTime = $account->featured_expires_at && $account->featured_expires_at->isFuture()
                        ? $account->featured_expires_at
                        : now();
                    $account->featured_expires_at = $baseTime->copy()->addDays($featuredExpiresDays);
                    $account->save();
                }

                ListingCreditTransaction::create([
                    'listing_credit_account_id' => $account->id,
                    'credit_type' => 'featured',
                    'credits' => $featuredCredits,
                    'package' => $package->slug,
                    'reason' => $reason ?? 'package_purchase',
                ]);
            }
        });
    }

    public static function grantAddon(Model $owner, ListingAddon $addon, ?string $reason = null): void
    {
        DB::transaction(function () use ($owner, $addon, $reason) {
            $account = ListingCreditAccount::firstOrCreate(
                [
                    'owner_type' => $owner->getMorphClass(),
                    'owner_id' => $owner->getKey(),
                ],
                [
                    'listing_balance' => 0,
                    'featured_balance' => 0,
                ]
            );

            $listingCredits = (int) ($addon->listing_credits ?? 0);
            $featuredCredits = (int) ($addon->featured_credits ?? 0);
            $featuredExpiresDays = (int) ($addon->featured_expires_days ?? 0);

            if ($listingCredits > 0) {
                $account->increment('listing_balance', $listingCredits);
                ListingCreditTransaction::create([
                    'listing_credit_account_id' => $account->id,
                    'credit_type' => 'listing',
                    'credits' => $listingCredits,
                    'package' => $addon->slug,
                    'reason' => $reason ?? 'addon_purchase',
                ]);
            }

            if ($featuredCredits > 0) {
                $account->increment('featured_balance', $featuredCredits);

                if ($featuredExpiresDays > 0) {
                    $baseTime = $account->featured_expires_at && $account->featured_expires_at->isFuture()
                        ? $account->featured_expires_at
                        : now();
                    $account->featured_expires_at = $baseTime->copy()->addDays($featuredExpiresDays);
                    $account->save();
                }

                ListingCreditTransaction::create([
                    'listing_credit_account_id' => $account->id,
                    'credit_type' => 'featured',
                    'credits' => $featuredCredits,
                    'package' => $addon->slug,
                    'reason' => $reason ?? 'addon_purchase',
                ]);
            }
        });
    }

    private static function listingCost(): int
    {
        return (int) (config('monetization.credit_costs.listing') ?? 1);
    }

    private static function featuredCost(): int
    {
        return (int) (config('monetization.credit_costs.featured') ?? 1);
    }
}
