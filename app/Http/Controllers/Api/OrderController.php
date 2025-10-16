<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\DokuService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected $dokuService;

    public function __construct(DokuService $dokuService)
    {
        $this->dokuService = $dokuService;
    }

    /**
     * Checkout - Buat order baru
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = $request->user();
            $totalAmount = 0;
            $orderItems = [];

            // Validasi produk dan hitung total
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => "Product with ID {$item['product_id']} not found"
                    ], 404);
                }

                if (!$product->is_active) {
                    return response()->json([
                        'success' => false,
                        'message' => "Product {$product->name} is not available"
                    ], 400);
                }

                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product {$product->name}"
                    ], 400);
                }

                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            // Buat order
            $order = Order::create([
                'user_id' => $user->id,
                'invoice_number' => Order::generateInvoiceNumber(),
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'expired_at' => now()->addHour(),
            ]);

            // Buat order items & kurangi stock
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Kurangi stock
                $item['product']->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order' => $order->load('items.product'),
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Payment - Generate DOKU payment URL
     */
    public function payment($orderId, Request $request)
    {
        $user = $request->user();
        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Order already processed'
            ], 400);
        }

        if ($order->expired_at < now()) {
            $order->update(['status' => 'expired']);
            return response()->json([
                'success' => false,
                'message' => 'Order has expired'
            ], 400);
        }

        try {
            // Panggil DOKU API
            $dokuResponse = $this->dokuService->createPayment($order, $user);

            if (isset($dokuResponse['response']['payment']['url'])) {
                // Update order dengan info payment
                $order->update([
                    'payment_url' => $dokuResponse['response']['payment']['url'],
                    'doku_session_id' => $dokuResponse['response']['order']['session_id'] ?? null,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment URL generated successfully',
                    'data' => [
                        'order_id' => $order->id,
                        'invoice_number' => $order->invoice_number,
                        'total_amount' => $order->total_amount,
                        'payment_url' => $dokuResponse['response']['payment']['url'],
                        'expired_at' => $order->expired_at,
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate payment URL',
                    'error' => $dokuResponse
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Riwayat Pemesanan
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 10);

        $orders = Order::where('user_id', $user->id)
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Order history retrieved successfully',
            'data' => $orders
        ], 200);
    }
}
