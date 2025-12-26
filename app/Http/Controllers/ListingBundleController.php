<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;
use App\Services\PaystackService;
use Illuminate\Support\Str;
use App\Models\ListingBundlePurchase;
use App\Services\ListingCreditService;
use App\Models\ListingPackage;
use App\Models\ListingAddon;
use Illuminate\Validation\ValidationException;
use Filament\Facades\Filament;

class ListingBundleController extends Controller
{
    public function __construct(private readonly PaystackService $paystackService)
    {
    }

    public function purchase(Request $request, string $type, string $slug)
    {
        $product = $this->resolveProduct($type, $slug);
        if (!$product) {
            return back()->with('error', 'Unknown listing package selected.');
        }

        $owner = $this->resolveOwner($request);
        if (!$owner) {
            return back()->with('error', 'Unable to determine purchase owner.');
        }

        $reference = Str::uuid()->toString();

        $purchase = ListingBundlePurchase::create([
            'owner_type' => $owner->getMorphClass(),
            'owner_id' => $owner->getKey(),
            'product_type' => $type,
            'product_id' => $product->id,
            'amount' => $product->price ?? 0,
            'currency' => 'NGN',
            'status' => 'pending',
            'paystack_reference' => $reference,
            'metadata' => [
                'listing_credits' => $product->listing_credits ?? 0,
                'featured_credits' => $product->featured_credits ?? 0,
                'product_slug' => $product->slug,
            ],
        ]);

        if ($purchase->amount <= 0) {
            $purchase->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
            try {
                $this->applyCredits($purchase->owner, $product, $type, 'self_service_free');
            } catch (ValidationException $exception) {
                return back()->with('error', collect($exception->errors())->flatten()->first() ?? 'Insufficient credits.');
            }
            return $this->redirectToOwnerDashboard($request, $purchase->owner)
                ->with('message', 'Credits activated successfully.');
        }

        $response = $this->paystackService->initializePayment(
            $request->user()->email,
            (float) $purchase->amount,
            $reference,
            route('listing-bundles.callback'),
            [
                'purchase_id' => $purchase->id,
            ]
        );

        if (!($response['status'] ?? false)) {
            return back()->with('error', $response['message'] ?? 'Unable to initialize payment.');
        }

        return redirect()->away($response['data']['authorization_url']);
    }

    public function callback(Request $request)
    {
        $reference = $request->query('reference');
        if (!$reference) {
            return redirect()->route('dashboard')->with('error', 'Missing payment reference.');
        }

        $purchase = ListingBundlePurchase::where('paystack_reference', $reference)->first();
        if (!$purchase) {
            return redirect()->route('dashboard')->with('error', 'Purchase record not found.');
        }

        if ($purchase->status === 'paid') {
            return redirect()->route('dashboard')->with('message', 'Bundle already activated.');
        }

        $response = $this->paystackService->verifyPayment($reference);
        if (!($response['status'] ?? false)) {
            return redirect()->route('dashboard')->with('error', 'Unable to verify payment.');
        }

        if (($response['data']['status'] ?? '') !== 'success') {
            $purchase->update(['status' => 'failed']);
            return redirect()->route('dashboard')->with('error', 'Payment was not completed.');
        }

        $purchase->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $productType = $purchase->product_type ?? ($purchase->bundle_key ? 'package' : null);
        $productId = $purchase->product_id ?? $purchase->bundle_key;
        $product = $productType ? $this->resolveProduct($productType, $productId) : null;
        if ($product) {
            try {
                $this->applyCredits($purchase->owner, $product, $productType, 'self_service_purchase');
            } catch (ValidationException $exception) {
                return $this->redirectToOwnerDashboard($request, $purchase->owner)
                    ->with('error', collect($exception->errors())->flatten()->first() ?? 'Insufficient credits.');
            }
        }

        return $this->redirectToOwnerDashboard($request, $purchase->owner)
            ->with('message', 'Credits activated successfully.');
    }

    private function resolveOwner(Request $request)
    {
        $user = $request->user();
        $tenant = Filament::getTenant();
        if ($tenant) {
            return $tenant;
        }

        if ($request->boolean('for_agency')) {
            $agency = Agency::where('owner_id', $user->id)->first();
            if ($agency) {
                return $agency;
            }
        }

        return ListingCreditService::resolveOwner($user);
    }

    private function redirectToOwnerDashboard(Request $request, $owner)
    {
        $user = $request->user();

        if ($owner instanceof Agency) {
            return redirect()->route('filament.agency.pages.agency-dashboard', ['tenant' => $owner]);
        }

        if (!$user) {
            return redirect()->route('pricing');
        }

        return match($user->user_type) {
            'super_admin', 'admin' => redirect()->route('filament.admin.pages.dashboard'),
            'agency_owner' => $this->redirectToAgencyDashboard($user),
            'agent' => $this->redirectAgentToDashboard($user),
            'property_owner' => redirect()->route('filament.landlord.pages.dashboard'),
            'tenant' => redirect()->route('filament.tenant.pages.dashboard'),
            'customer' => redirect('/dashboard'),
            default => redirect()->route('pricing'),
        };
    }

    private function redirectToAgencyDashboard($user)
    {
        $agency = $user->ownedAgencies()->first() ?? $user->agencies()->first();

        if ($agency) {
            return redirect()->route('filament.agency.pages.agency-dashboard', ['tenant' => $agency]);
        }

        return redirect()->route('filament.agent.pages.dashboard');
    }

    private function redirectAgentToDashboard($user)
    {
        $agency = $user->agencies()->first();

        if ($agency) {
            return redirect()->route('filament.agency.pages.agency-dashboard', ['tenant' => $agency]);
        }

        return redirect()->route('filament.agent.pages.dashboard');
    }

    private function resolveProduct(string $type, string|int $identifier)
    {
        if ($type === 'package') {
            return ListingPackage::where('slug', $identifier)->where('is_active', true)->first()
                ?? ListingPackage::find($identifier);
        }

        if ($type === 'addon') {
            return ListingAddon::where('slug', $identifier)->where('is_active', true)->first()
                ?? ListingAddon::find($identifier);
        }

        return null;
    }

    private function applyCredits($owner, $product, string $type, string $reason): void
    {
        if ($type === 'package') {
            ListingCreditService::grantPackage($owner, $product, $reason);
            return;
        }

        if ($type === 'addon') {
            ListingCreditService::grantAddon($owner, $product, $reason);
        }
    }
}
