<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'employee_id',
        'designer_id',
        'date',
        'delivery_date',
        'number',
        'status',
        'step',
        'preview',
        'design_file',
        'arrived',
    ];

    protected $casts = [
        'date' => 'datetime',
        'delivery_date' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function  employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products', 'order_id', 'product_id')
            ->withPivot('qtd', 'in_stock', 'supplier', 'obs')
            ->withTimestamps();
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function movements()
    {
        return $this->hasMany(OrderMovement::class, 'order_id');
    }

    public function getPreviewAttribute($value)
    {
        return json_decode($value, true);
    }
}
