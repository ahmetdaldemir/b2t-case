<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

 
    protected $fillable = [
        'user_id',
        'status',
        'total_price',
    ];

 
    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
        ];
    }

 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
                    ->withPivot('quantity', 'unit_price')
                    ->withTimestamps();
    }
 
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

  
    public function calculateTotalPrice(): void
    {
        $total = $this->orderItems->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $this->update(['total_price' => $total]);
    }
}
