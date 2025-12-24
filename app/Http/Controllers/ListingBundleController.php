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
            $this->applyCredits($purchase->owner, $product, $type, 'self_service_free');
            return redirect()->route('dashboard')->with('message', 'Listing credits activated successfully.');
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
            $this->applyCredits($purchase->owner, $product, $productType, 'self_service_purchase');
        }

        return redirect()->route('dashboard')->with('message', 'Listing credits activated successfully.');
    }

    private function resolveOwner(Request $request)
    {
        $user = $request->user();

        if ($request->boolean('for_agency')) {
            $agency = Agency::where('owner_id', $user->id)->first();
            if ($agency) {
                return $agency;
            }
        }

        return $user;
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
