<?php

namespace App\Http\Controllers;

use App\Models\SmartSearch;
use App\Models\SmartSearchSubscription;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SmartSearchPaymentController extends Controller
{
    public function __construct(private readonly PaystackService $paystackService)
    {
    }

    /**
     * Display the SmartSearch pricing page.
     */
    public function showPricing(Request $request)
    {
        $user = $request->user();
        $tiers = SmartSearch::getTierOptions();

        // Get user's active purchases if logged in
        $activePurchases = $user
            ? SmartSearchSubscription::where('user_id', $user->id)
                ->active()
                ->get()
            : collect();

        // Check remaining searches across all active purchases
        $remainingSearches = $activePurchases->sum(fn($p) => $p->getRemainingSearches());

        return view('smartsearch.pricing', [
            'tiers' => $tiers,
            'activePurchases' => $activePurchases,
            'remainingSearches' => $remainingSearches,
        ]);
    }

    /**
     * Initialize purchase of a SmartSearch tier.
     */
    public function purchase(Request $request, string $tier)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to purchase SmartSearch.');
        }

        // Validate tier
        $tierConfig = SmartSearch::getTierBySlug($tier);
        if (!$tierConfig) {
            return back()->with('error', 'Invalid tier selected.');
        }

        $price = $tierConfig['price'];
        $reference = Str::uuid()->toString();

        // Create pending purchase record
        $purchase = SmartSearchSubscription::create([
            'user_id' => $user->id,
            'tier' => $tier,
            'searches_limit' => $tierConfig['searches'],
            'searches_used' => 0,
            'duration_days' => $tierConfig['duration_days'],
            'amount_paid' => $price,
            'payment_reference' => $reference,
            'payment_method' => 'paystack',
            'payment_status' => 'pending',
            'notification_channels' => $tierConfig['channels'],
        ]);

        // Initialize Paystack payment
        $response = $this->paystackService->initializePayment(
            $user->email,
            (float) $price,
            $reference,
            route('paystack.universal.callback'),
            [
                'purchase_id' => $purchase->id,
                'tier' => $tier,
                'user_id' => $user->id,
                'purpose' => 'smartsearch',
            ]
        );

        if (!($response['status'] ?? false)) {
            $purchase->markAsFailed($response['message'] ?? 'Payment initialization failed');
            return back()->with('error', $response['message'] ?? 'Unable to initialize payment. Please try again.');
        }

        return redirect()->away($response['data']['authorization_url']);
    }

    /**
     * Handle Paystack callback for SmartSearch payment.
     */
    public function handleCallback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('smartsearch.pricing')->with('error', 'Missing payment reference.');
        }

        $purchase = SmartSearchSubscription::where('payment_reference', $reference)->first();

        if (!$purchase) {
            return redirect()->route('smartsearch.pricing')->with('error', 'Purchase record not found.');
        }

        // Already processed
        if ($purchase->isPaid()) {
            return redirect()->route('customer.searches.index')
                ->with('success', 'Your SmartSearch is already active!');
        }

        // Verify payment with Paystack
        $response = $this->paystackService->verifyPayment($reference);

        if (!($response['status'] ?? false)) {
            return redirect()->route('smartsearch.pricing')
                ->with('error', 'Unable to verify payment. Please contact support if you were charged.');
        }

        if (($response['data']['status'] ?? '') !== 'success') {
            $purchase->markAsFailed('Payment verification failed: ' . ($response['data']['gateway_response'] ?? 'Unknown error'));
            return redirect()->route('smartsearch.pricing')
                ->with('error', 'Payment was not completed. Please try again.');
        }

        // Activate purchase
        $purchase->activate([
            'paystack_response' => $response['data'],
            'verified_at' => now()->toIso8601String(),
        ]);

        $tierName = SmartSearch::TIER_CONFIGS[$purchase->tier]['name'] ?? 'SmartSearch';
        $searchLimit = $purchase->hasUnlimitedSearches() ? 'unlimited' : $purchase->searches_limit;

        return redirect()->route('customer.searches.index')
            ->with('success', "Your {$tierName} plan is now active! You can create {$searchLimit} searches for the next {$purchase->duration_days} days.");
    }

    /**
     * Extend a search that has received no matches (free goodwill extension).
     */
    public function extendForNoMatch(Request $request, SmartSearch $search)
    {
        $user = $request->user();

        // Verify ownership
        if ($search->user_id !== $user->id) {
            return back()->with('error', 'You do not have permission to extend this search.');
        }

        // Check if eligible for no-match extension
        if ($search->hasReceivedMatches()) {
            return back()->with('error', 'This search has received matches and is not eligible for extension.');
        }

        // Check if already expired (must be expired to request extension)
        if (!$search->isExpired()) {
            return back()->with('error', 'Only expired searches can be extended.');
        }

        // Check if extension was already granted (prevent abuse)
        $metadata = $search->additional_filters ?? [];
        if (isset($metadata['no_match_extension_granted'])) {
            return back()->with('error', 'A no-match extension has already been granted for this search.');
        }

        // Grant 30-day extension
        $search->extendDuration(30);

        // Mark as extended
        $metadata['no_match_extension_granted'] = now()->toIso8601String();
        $search->update(['additional_filters' => $metadata]);

        return back()->with('success', 'Your search has been extended by 30 days. We\'ll keep looking for matches!');
    }
}
