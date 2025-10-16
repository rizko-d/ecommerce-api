<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'invoice_number', 'total_amount', 'status',
        'payment_method', 'payment_url', 'doku_session_id',
        'paid_at', 'expired_at', 'shipping_address'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateInvoiceNumber()
    {
        return 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    }
}
