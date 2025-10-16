<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WebhookController;

// Public routes - dengan access key
Route::middleware('access.key')->group(function () {
    // Auth routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Product routes (publik)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
});

// Protected routes - butuh access key + sanctum authentication
Route::middleware(['access.key', 'auth:sanctum'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Orders
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::post('/orders/{orderId}/payment', [OrderController::class, 'payment']);
    Route::get('/orders/history', [OrderController::class, 'history']);
});

// Webhook route
Route::post('/webhook/doku', [WebhookController::class, 'dokuNotification']);