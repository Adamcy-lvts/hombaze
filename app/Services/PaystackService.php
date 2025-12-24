<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaystackService
{
    public function initializePayment(string $email, float $amount, string $reference, string $callbackUrl, array $metadata = []): array
    {
        return Http::withToken(config('services.paystack.secret_key'))
            ->post($this->buildUrl('/transaction/initialize'), [
                'email' => $email,
                'amount' => (int) round($amount * 100),
                'reference' => $reference,
                'callback_url' => $callbackUrl,
                'metadata' => $metadata,
            ])
            ->json();
    }

    public function verifyPayment(string $reference): array
    {
        return Http::withToken(config('services.paystack.secret_key'))
            ->get($this->buildUrl("/transaction/verify/{$reference}"))
            ->json();
    }

    private function buildUrl(string $path): string
    {
        $baseUrl = rtrim(config('services.paystack.payment_url'), '/');
        return $baseUrl . $path;
    }
}
