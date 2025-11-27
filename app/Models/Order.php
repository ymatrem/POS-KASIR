<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'total_amount',
        'total_quantity',
        'status',
        'payment_method',
        'completed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'total_quantity' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
