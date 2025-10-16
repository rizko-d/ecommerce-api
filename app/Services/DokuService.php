<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DokuService
{
    private $clientId;
    private $secretKey;
    private $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.doku.client_id');
        $this->secretKey = config('services.doku.secret_key');
        $this->baseUrl = config('services.doku.base_url');
    }

    /**
     * Generate signature untuk DOKU request
     */
    private function generateSignature($requestBody, $requestId, $timestamp)
    {
        $digest = base64_encode(hash('sha256', $requestBody, true));
        $requestTarget = '/checkout/v1/payment';
        $componentString = "Client-Id:" . $this->clientId . "\n" .
                          "Request-Id:" . $requestId . "\n" .
                          "Request-Timestamp:" . $timestamp . "\n" .
                          "Request-Target:" . $requestTarget . "\n" .
                          "Digest:" . $digest;
        $signature = base64_encode(hash_hmac('sha256', $componentString, $this->secretKey, true));
        
        return [
            'signature' => 'HMACSHA256=' . $signature,
            'digest' => $digest
        ];
    }

    /**
     * Validasi signature dari DOKU notification
     */
    public function validateSignature($requestBody, $clientId, $requestId, $timestamp, $signature)
    {
        $digest = base64_encode(hash('sha256', $requestBody, true));
        
        $componentString = "Client-Id:" . $clientId . "\n" .
                          "Request-Id:" . $requestId . "\n" .
                          "Request-Timestamp:" . $timestamp . "\n" .
                          "Digest:" . $digest;
        
        $expectedSignature = 'HMACSHA256=' . base64_encode(hash_hmac('sha256', $componentString, $this->secretKey, true));
        
        return $signature === $expectedSignature;
    }

    /**
     * Buat payment dengan DOKU
     */
    public function createPayment($order, $user)
    {
        $requestId = Str::uuid()->toString();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        
        $requestBody = [
            'order' => [
                'amount' => (int) $order->total_amount,
                'invoice_number' => $order->invoice_number,
                'callback_url' => config('services.doku.callback_url'),
                'auto_redirect' => true,
            ],
            'payment' => [
                'payment_due_date' => 60, // dalalm menit
            ],
            'customer' => [
                'id' => (string) $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '628123456789',
                'address' => $user->address ?? 'Jakarta',
            ]
        ];

        $requestBodyJson = json_encode($requestBody);
        $signatureData = $this->generateSignature($requestBodyJson, $requestId, $timestamp);

        $response = Http::withHeaders([
            'Client-Id' => $this->clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => $signatureData['signature'],
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/checkout/v1/payment', $requestBody);

        return $response->json();
    }
}
