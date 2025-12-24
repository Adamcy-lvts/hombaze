<?php

namespace App\Http\Controllers;

use App\Models\RentPayment;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaystackController extends Controller
{
    public function __construct(private readonly PaystackService $paystackService)
    {
    }

    public function initializeRentPayment(RentPayment $payment)
    {
        $this->authorizeRentPayment($payment);

        if ($payment->status === RentPayment::STATUS_PAID) {
            return back()->with('message', 'This rent payment is already marked as paid.');
        }

        $reference = $payment->payment_reference ?: Str::uuid()->toString();
        $payment->update([
            'payment_reference' => $reference,
            'payment_method' => RentPayment::METHOD_CARD,
            'status' => RentPayment::STATUS_PENDING,
        ]);

        $response = $this->paystackService->initializePayment(
            auth()->user()->email,
            (float) $payment->amount,
            $reference,
            route('paystack.callback'),
            [
                'payment_id' => $payment->id,
                'purpose' => 'rent',
            ]
        );

        if (!($response['status'] ?? false)) {
            return back()->with('error', $response['message'] ?? 'Unable to initialize payment.');
        }

        return redirect()->away($response['data']['authorization_url']);
    }

    public function handleCallback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('dashboard')->with('error', 'Missing payment reference.');
        }

        $response = $this->paystackService->verifyPayment($reference);

        if (!($response['status'] ?? false)) {
            return redirect()->route('dashboard')->with('error', 'Unable to verify payment.');
        }

        $payment = RentPayment::where('payment_reference', $reference)->first();

        if (!$payment) {
            return redirect()->route('dashboard')->with('error', 'Payment record not found.');
        }

        if (($response['data']['status'] ?? '') === 'success') {
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

    private function authorizeRentPayment(RentPayment $payment): void
    {
        $tenantUserId = $payment->tenant?->user_id;

        if (!$tenantUserId || $tenantUserId !== auth()->id()) {
            abort(403);
        }
    }
}
