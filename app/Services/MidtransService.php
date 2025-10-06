<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransService
{
    public function isEnabled(): bool
    {
        return (bool) Config::get('midtrans.server_key');
    }

    public function baseUrl(): string
    {
        return Config::get('midtrans.is_production')
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';
    }

    public function createSnapTransaction(Transaction $transaction): array
    {
        if (! $this->isEnabled()) {
            throw new RuntimeException('Konfigurasi Midtrans belum diatur.');
        }

        $user = $transaction->savingsAccount->user;
        $orderId = sprintf('DEP-%s-%s', now('Asia/Jakarta')->format('YmdHis'), $transaction->id);
        $grossAmount = (int) round($transaction->amount);

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'item_details' => [
                [
                    'id' => $transaction->id,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => 'Setoran Tabungan',
                ],
            ],
            'callbacks' => [
                'finish' => route('transactions.show', $transaction),
            ],
        ];

        $notifyUrl = Config::get('midtrans.notify_url') ?: route('midtrans.notifications');

        if ($notifyUrl) {
            $payload['notification'] = [
                'url' => $notifyUrl,
            ];
        }

        $response = Http::withBasicAuth(Config::get('midtrans.server_key'), '')
            ->acceptJson()
            ->post($this->baseUrl().'/snap/v1/transactions', $payload);

        if (! $response->successful()) {
            throw new RuntimeException('Gagal membuat transaksi Midtrans: '.$response->body());
        }

        return array_merge($response->json(), [
            'order_id' => $orderId,
        ]);
    }

    public function verifySignature(array $payload): bool
    {
        $serverKey = Config::get('midtrans.server_key');

        if (! $serverKey) {
            return false;
        }

        $expected = hash('sha512', Arr::get($payload, 'order_id').Arr::get($payload, 'status_code').Arr::get($payload, 'gross_amount').$serverKey);

        return hash_equals($expected, (string) Arr::get($payload, 'signature_key'));
    }
}
