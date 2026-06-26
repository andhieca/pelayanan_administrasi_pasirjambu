<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $apiUrl = 'https://api.fonnte.com/send';
    protected ?string $token;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
    }

    /**
     * Send a WhatsApp message via Fonnte API.
     *
     * @param string $phone  Recipient phone number
     * @param string $message  Message content
     * @return array  ['success' => bool, 'detail' => string]
     */
    public function sendMessage(string $phone, string $message): array
    {
        if (empty($this->token)) {
            Log::warning('Fonnte: API token belum dikonfigurasi.');
            return ['success' => false, 'detail' => 'API token belum dikonfigurasi'];
        }

        $phone = $this->formatPhone($phone);

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                'target' => $phone,
                'message' => $message,
            ]);

            $body = $response->json();

            Log::info('Fonnte: Response', [
                'phone' => $phone,
                'status' => $response->status(),
                'body' => $body,
            ]);

            if ($response->successful() && isset($body['status']) && $body['status'] === true) {
                return ['success' => true, 'detail' => $body['detail'] ?? 'Pesan terkirim'];
            }

            return [
                'success' => false,
                'detail' => $body['detail'] ?? $body['reason'] ?? 'Gagal mengirim pesan',
            ];
        } catch (\Exception $e) {
            Log::error('Fonnte: Exception', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'detail' => $e->getMessage()];
        }
    }

    /**
     * Format phone number to international format (62xxx).
     * Converts 08xxx → 628xxx, +62xxx → 62xxx.
     */
    protected function formatPhone(string $phone): string
    {
        // Remove spaces, dashes, and dots
        $phone = preg_replace('/[\s\-\.]/', '', $phone);

        // Remove leading +
        $phone = ltrim($phone, '+');

        // Convert 08xx to 628xx
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }
}
