<?php

namespace App\Http\Controllers;

use App\Models\ListingAddon;
use App\Models\ListingBundlePurchase;
use App\Models\ListingPackage;
use App\Models\RentPayment;
use App\Models\SmartSearch;
use App\Models\SmartSearchSubscription;
use App\Services\ListingCreditService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaystackUniversalCallbackController extends Controller
{
    public function __construct(private readonly PaystackService $paystackService)
    {
    }

    public function __invoke(Request $request)
    {
        $reference = $request->query('reference');
        if (!$reference) {
            return redirect()->route('pricing')->with('error', 'Missing payment reference.');
        }

        $response = $this->paystackService->verifyPayment($reference);
        if (!($response['status'] ?? false)) {
            return redirect()->route('pricing')->with('error', 'Unable to verify payment.');
        }

        $data = $response['data'] ?? [];
        $metadata = $this->normalizeMetadata($data['metadata'] ?? []);
        $purpose = $metadata['purpose'] ?? null;

        if ($purpose === 'rent' || isset($metadata['payment_id'])) {
            return $this->handleRentPayment($reference, $data);
        }

        if ($purpose === 'listing_bundle' || isset($metadata['purchase_id'])) {
            $purchase = isset($metadata['purchase_id'])
                ? ListingBundlePurchase::find($metadata['purchase_id'])
                : null;
            $purchase = $purchase ?: ListingBundlePurchase::where('paystack_reference', $reference)->first();
            if ($purchase) {
                return $this->handleListingBundle($request, $purchase, $data);
            }
        }

        if ($purpose === 'smartsearch' || isset($metadata['purchase_id'])) {
            $subscription = isset($metadata['purchase_id'])
                ? SmartSearchSubscription::find($metadata['purchase_id'])
                : null;
            $subscription = $subscription ?: SmartSearchSubscription::where('payment_reference', $reference)->first();
            if ($subscription) {
                return $this->handleSmartSearch($subscription, $data);
            }
        }

        if ($payment = RentPayment::where('payment_reference', $reference)->first()) {
            return $this->handleRentPayment($reference, $data);
        }

        if ($purchase = ListingBundlePurchase::where('paystack_reference', $reference)->first()) {
            return $this->handleListingBundle($request, $purchase, $data);
        }

        if ($subscription = SmartSearchSubscription::where('payment_reference', $reference)->first()) {
            return $this->handleSmartSearch($subscription, $data);
        }

        return redirect()->route('pricing')->with('error', 'Payment record not found.');
    }

    private function handleRentPayment(string $reference, array $data)
    {
        $payment = RentPayment::where('payment_reference', $reference)->first();
        if (!$payment) {
            return redirect()->route('dashboard')->with('error', 'Payment record not found.');
        }

        if (($data['status'] ?? '') === 'success') {
            $payment->update([
                'status' => RentPayment::STATUS_PAID,
                'payment_date' => now(),
            ]);

            return redirect()->route('dashboard')->with('message', 'Payment received successfully.');
        }

        $payment->update([
            'status' => RentPayment::STATUS_PENDING,
        ]);

        return redirect()->route('dashboard')->with('error', 'Payment was not completed. Please try again.');
    }

    private function handleListingBundle(Request $request, ListingBundlePurchase $purchase, array $data)
    {
        if ($purchase->status === 'paid') {
            return $this->redirectToOwnerDashboard($request, $purchase->owner)
                ->with('message', 'Bundle already activated.');
        }

        if (($data['status'] ?? '') !== 'success') {
            $purchase->update(['status' => 'failed']);
            return $this->redirectToOwnerDashboard($request, $purchase->owner)
                ->with('error', 'Payment was not completed.');
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

    private function handleSmartSearch(SmartSearchSubscription $subscription, array $data)
    {
        if ($subscription->isPaid()) {
            return redirect()->route('customer.searches.index')
                ->with('success', 'Your SmartSearch is already active!');
        }

        if (($data['status'] ?? '') !== 'success') {
            $subscription->markAsFailed('Payment verification failed: ' . ($data['gateway_response'] ?? 'Unknown error'));
            return redirect()->route('smartsearch.pricing')
                ->with('error', 'Payment was not completed. Please try again.');
        }

        $subscription->activate([
            'paystack_response' => $data,
            'verified_at' => now()->toIso8601String(),
        ]);

        $tierName = SmartSearch::TIER_CONFIGS[$subscription->tier]['name'] ?? 'SmartSearch';
        $searchLimit = $subscription->hasUnlimitedSearches() ? 'unlimited' : $subscription->searches_limit;

        return redirect()->route('customer.searches.index')
            ->with('success', "Your {$tierName} plan is now active! You can create {$searchLimit} searches for the next {$subscription->duration_days} days.");
    }

    private function redirectToOwnerDashboard(Request $request, $owner)
    {
        $user = $request->user();

        if ($owner instanceof \App\Models\Agency) {
            return redirect()->route('filament.agency.pages.agency-dashboard', ['tenant' => $owner]);
        }

        if (!$user) {
            return redirect()->route('pricing');
        }

        return match ($user->user_type) {
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

    private function normalizeMetadata($metadata): array
    {
        if (is_array($metadata)) {
            return $metadata;
        }

        if (is_string($metadata)) {
            $decoded = json_decode($metadata, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }
}
