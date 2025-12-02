<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'image_url',
        'category_id',
        'sold_quantity',
        'stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sold_quantity' => 'integer',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Cek apakah stok cukup
    public function hasEnoughStock($quantity)
    {
        return $this->stock >= $quantity;
    }

    // Kurangi stok
    public function decreaseStock($quantity)
    {
        $this->stock -= $quantity;
        $this->save();
    }

    // Tambah stok
    public function increaseStock($quantity)
    {
        $this->stock += $quantity;
        $this->save();
    }
}
