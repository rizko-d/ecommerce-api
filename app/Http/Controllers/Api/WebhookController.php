<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $dokuService;

    public function __construct(DokuService $dokuService)
    {
        $this->dokuService = $dokuService;
    }

    /**
     * Handle webhook dari DOKU
     * Webhook ini dipanggil ketika pembayaran berhasil
     */
    public function dokuNotification(Request $request)
    {
        Log::info('DOKU Webhook Received', [
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        // Ambil header signature untuk validasi
        $clientId = $request->header('Client-Id');
        $requestId = $request->header('Request-Id');
        $timestamp = $request->header('Request-Timestamp');
        $signature = $request->header('Signature');

        // Validasi signature
        $requestBody = $request->getContent();
        
        if (!$this->dokuService->validateSignature($requestBody, $clientId, $requestId, $timestamp, $signature)) {
            Log::warning('DOKU Webhook: Invalid signature');
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature'
            ], 401);
        }

        // Ambil data dari notification body
        $data = $request->all();
        $invoiceNumber = $data['order']['invoice_number'] ?? null;
        $transactionStatus = $data['transaction']['status'] ?? null;

        if (!$invoiceNumber) {
            Log::error('DOKU Webhook: Invoice number not found');
            return response()->json([
                'success' => false,
                'message' => 'Invoice number not found'
            ], 400);
        }

        // Cari order berdasarkan invoice number
        $order = Order::where('invoice_number', $invoiceNumber)->first();

        if (!$order) {
            Log::error('DOKU Webhook: Order not found', ['invoice' => $invoiceNumber]);
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Update status order berdasarkan status transaksi
        if ($transactionStatus === 'SUCCESS') {
            $order->update([
                'status' => 'paid',
                'payment_method' => $data['channel']['id'] ?? 'unknown',
                'paid_at' => now(),
            ]);

            Log::info('DOKU Webhook: Payment success', [
                'invoice' => $invoiceNumber,
                'order_id' => $order->id
            ]);
            
        } elseif ($transactionStatus === 'FAILED') {
            // Kembalikan stock jika payment gagal
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            $order->update(['status' => 'cancelled']);

            Log::info('DOKU Webhook: Payment failed', [
                'invoice' => $invoiceNumber,
                'order_id' => $order->id
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Notification received'
        ], 200);
    }
}
