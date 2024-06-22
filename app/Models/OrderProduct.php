<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'qtd',
        'order_id',
        'in_stock',
        'supplier',
        'obs'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
