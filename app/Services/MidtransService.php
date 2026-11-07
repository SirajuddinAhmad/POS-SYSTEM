<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    /**
     * Dapatkan Server Key dari pengaturan atau env
     */
    public static function getServerKey(): string
    {
        return Setting::get('midtrans_server_key', env('MIDTRANS_SERVER_KEY'));
    }

    /**
     * Dapatkan Client Key dari pengaturan atau env
     */
    public static function getClientKey(): string
    {
        return Setting::get('midtrans_client_key', env('MIDTRANS_CLIENT_KEY'));
    }

    /**
     * Cek apakah mode produksi atau sandbox
     */
    public static function isProduction(): bool
    {
        return (bool) Setting::get('midtrans_is_production', 0);
    }

    /**
     * URL Javascript Snap Midtrans
     */
    public static function getSnapJsUrl(): string
    {
        return static::isProduction()
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /**
     * URL API Snap Transaksi
     */
    public static function getSnapApiUrl(): string
    {
        return static::isProduction()
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Integrasikan harga produk dan keranjang belanja ke Midtrans Snap Token
     */
    public static function createSnapTransaction(array $cartItems, float $discount = 0, string $customerName = 'Pelanggan Umum', ?string $orderId = null): array
    {
        if (empty($cartItems)) {
            throw new \Exception('Keranjang belanja kosong.');
        }

        if (!$orderId) {
            $orderId = 'MID-' . date('YmdHis') . '-' . rand(100, 999);
        }

        $itemDetails = [];
        $calculatedGrossAmount = 0;

        foreach ($cartItems as $item) {
            $productId = $item['id'] ?? null;
            $product = $productId ? Product::find($productId) : null;

            $sellingPrice = $product ? (int) round($product->selling_price) : (int) round($item['price'] ?? 0);
            $qty = (int) ($item['qty'] ?? 1);
            $subtotal = $sellingPrice * $qty;

            $calculatedGrossAmount += $subtotal;

            $itemDetails[] = [
                'id' => substr((string)($product ? $product->code ?? $product->id : $productId), 0, 50),
                'price' => $sellingPrice,
                'quantity' => $qty,
                'name' => substr($product ? $product->name : ($item['name'] ?? 'Item POS'), 0, 50),
            ];
        }

        // Tambahkan diskon sebagai item negatif jika ada diskon
        $discountAmount = (int) round($discount);
        if ($discountAmount > 0) {
            $itemDetails[] = [
                'id' => 'DISCOUNT',
                'price' => -$discountAmount,
                'quantity' => 1,
                'name' => 'Potongan Diskon Belanja',
            ];
            $calculatedGrossAmount -= $discountAmount;
        }

        // Midtrans gross_amount harus positif minimum 1
        $grossAmount = max(1, $calculatedGrossAmount);

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => substr($customerName ?: 'Pelanggan Umum', 0, 50),
            ],
        ];

        $serverKey = static::getServerKey();
        $apiUrl = static::getSnapApiUrl();

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(10)
                ->post($apiUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'status' => 'success',
                    'token' => $data['token'] ?? null,
                    'redirect_url' => $data['redirect_url'] ?? null,
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                    'item_details' => $itemDetails,
                    'is_simulated' => false,
                ];
            }

            Log::warning('Midtrans API Error: ' . $response->body());
            
            // Jika kredensial sandbox belum valid atau offline, generate fallback token simulasi
            $simulatedToken = 'SIM-' . md5($orderId . microtime());
            return [
                'status' => 'simulated',
                'message' => 'Menggunakan mode simulasi Midtrans (kredensial Sandbox belum terhubung ke akun live Midtrans)',
                'token' => $simulatedToken,
                'redirect_url' => '#',
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
                'item_details' => $itemDetails,
                'is_simulated' => true,
                'raw_error' => $response->json() ?? $response->body(),
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans Exception: ' . $e->getMessage());

            // Fallback graceful
            $simulatedToken = 'SIM-' . md5($orderId . microtime());
            return [
                'status' => 'simulated',
                'message' => 'Simulasi Midtrans aktif (Koneksi Midtrans: ' . $e->getMessage() . ')',
                'token' => $simulatedToken,
                'redirect_url' => '#',
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
                'item_details' => $itemDetails,
                'is_simulated' => true,
            ];
        }
    }

    /**
     * Verifikasi Signature Key untuk Webhook Notifikasi Midtrans
     */
    public static function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = static::getServerKey();
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        return hash_equals($expectedSignature, $signatureKey);
    }
}
