<?php

namespace Modules\Payment\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class NowPaymentsClient
{
    public function isConfigured(): bool
    {
        return filled(config('nowpayments.api_key'));
    }

    /**
     * Create a hosted invoice. Returns the decoded NOWPayments response,
     * which includes `invoice_url` to redirect the customer to.
     *
     * @return array<string, mixed>
     */
    public function createInvoice(string $orderReference, float $amount, string $description): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('NOWPayments is not configured yet (missing NOWPAYMENTS_API_KEY).');
        }

        $response = Http::withHeaders([
            'x-api-key' => config('nowpayments.api_key'),
        ])->post(config('nowpayments.base_url').'/invoice', [
            'price_amount' => $amount,
            'price_currency' => config('nowpayments.price_currency'),
            'order_id' => $orderReference,
            'order_description' => $description,
            'ipn_callback_url' => config('nowpayments.ipn_callback_url'),
            'success_url' => config('nowpayments.success_url'),
            'cancel_url' => config('nowpayments.cancel_url'),
        ]);

        if ($response->failed()) {
            throw new RuntimeException('NOWPayments invoice creation failed: '.$response->body());
        }

        return $response->json();
    }

    /**
     * Verify the `x-nowpayments-sig` header on an IPN callback by recomputing
     * the HMAC-SHA512 over the payload with keys sorted alphabetically.
     *
     * @param  array<string, mixed>  $payload
     */
    public function verifyIpnSignature(array $payload, string $signature): bool
    {
        $secret = config('nowpayments.ipn_secret');
        if (blank($secret)) {
            return false;
        }

        ksort($payload);
        $sortedJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $expected = hash_hmac('sha512', $sortedJson, $secret);

        return hash_equals($expected, $signature);
    }
}
