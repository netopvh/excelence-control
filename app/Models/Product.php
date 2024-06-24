<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products')
            ->withPivot(['qtd', 'in_stock', 'supplier', 'obs'])
            ->withTimestamps();
    }
}
