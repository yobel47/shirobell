<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Thin HTTP wrapper around Midtrans Snap (create transaction) and the
 * Core API status endpoint. No SDK; the server key never leaves this class.
 */
class MidtransClient
{
    /**
     * @return array{token: string, redirect_url: string}
     */
    public function createTransaction(Order $order): array
    {
        $order->loadMissing('user', 'invitation');
        $user = $order->user;

        $response = $this->http($this->snapBase())->post('/snap/v1/transactions', [
            'transaction_details' => [
                'order_id' => $order->uuid,
                'gross_amount' => $order->total_amount,
            ],
            'item_details' => [[
                'id' => match ($order->type) {
                    'renewal' => 'RENEWAL', 'event_day' => 'EVENTDAY', default => 'PACKAGE'
                },
                'price' => $order->total_amount,
                'quantity' => 1,
                'name' => Str::limit($order->type === 'event_day' ? 'Add-on Hari-H undangan' : ($order->type === 'renewal' ? 'Perpanjangan' : 'Paket').' undangan '.$order->duration_days.' hari', 50, ''),
            ]],
            'customer_details' => array_filter([
                'first_name' => Str::limit($user->name, 100, ''),
                'email' => $user->email,
                'phone' => $user->phone,
            ]),
            'callbacks' => [
                'finish' => route('app.orders.show', $order),
            ],
            'expiry' => [
                'unit' => 'hour',
                'duration' => (int) config('services.midtrans.expiry_hours', 24),
            ],
        ]);

        if (! $response->successful() || ! $response->json('redirect_url')) {
            $messages = implode('; ', (array) $response->json('error_messages', []));
            throw new RuntimeException('Midtrans create transaction failed ('.$response->status().'): '.$messages);
        }

        return [
            'token' => (string) $response->json('token'),
            'redirect_url' => (string) $response->json('redirect_url'),
        ];
    }

    /**
     * Authoritative transaction status. Returns null when Midtrans does not
     * know the order yet (status_code 404, e.g. customer never opened the page).
     *
     * @return array<string, mixed>|null
     */
    public function status(string $orderId): ?array
    {
        $response = $this->http($this->apiBase())->get('/v2/'.rawurlencode($orderId).'/status');

        if (! $response->successful()) {
            throw new RuntimeException('Midtrans status request failed ('.$response->status().')');
        }

        $data = $response->json();
        if ((string) ($data['status_code'] ?? '') === '404') {
            return null;
        }

        return $data;
    }

    /**
     * Expire a pending transaction so its payment method (VA number, QRIS,
     * e-wallet link) can no longer be paid. Returns true when nothing can be
     * paid on this order id anymore: expired now, already expired, or never
     * started at the gateway (customer opened Snap but picked no method).
     * Returns false when Midtrans refuses, e.g. the payment already settled.
     */
    public function expire(string $orderId): bool
    {
        $response = $this->http($this->apiBase())->post('/v2/'.rawurlencode($orderId).'/expire');

        if ($response->serverError() || $response->status() === 0) {
            throw new RuntimeException('Midtrans expire request failed ('.$response->status().')');
        }

        $code = (string) ($response->json('status_code') ?? $response->status());
        if (in_array($code, ['200', '407', '404'], true)) {
            return true;
        }

        // 412: the transaction can no longer be modified. Already expired is fine.
        return $response->json('transaction_status') === 'expire';
    }

    public function verifySignature(array $payload): bool
    {
        $expected = hash('sha512',
            ($payload['order_id'] ?? '').($payload['status_code'] ?? '').($payload['gross_amount'] ?? '').$this->serverKey()
        );

        return is_string($payload['signature_key'] ?? null) && hash_equals($expected, $payload['signature_key']);
    }

    public function isConfigured(): bool
    {
        return filled(config('services.midtrans.server_key'));
    }

    private function http(string $baseUrl): PendingRequest
    {
        return Http::baseUrl($baseUrl)
            ->withBasicAuth($this->serverKey(), '')
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            ->retry(2, 300, throw: false);
    }

    private function serverKey(): string
    {
        $key = (string) config('services.midtrans.server_key');
        if ($key === '') {
            throw new RuntimeException('MIDTRANS_SERVER_KEY is not configured.');
        }

        return $key;
    }

    private function snapBase(): string
    {
        return config('services.midtrans.is_production') ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com';
    }

    private function apiBase(): string
    {
        return config('services.midtrans.is_production') ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
    }
}
