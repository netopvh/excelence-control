<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderProduct
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property string $qtd
 * @property string $in_stock
 * @property string|null $supplier
 * @property string|null $obs
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereInStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereObs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereQtd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereSupplier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereUpdatedAt($value)
 * @property string|null $was_bought
 * @property string|null $link
 * @property \Illuminate\Support\Carbon|null $arrival_date
 * @property string|null $arrived
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereArrivalDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereArrived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct whereWasBought($value)
 * @mixin \Eloquent
 */
class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'qtd',
        'order_id',
        'in_stock',
        'supplier',
        'link',
        'obs',
        'arrival_date',
        'purchase_date',
        'arrived',
        'was_bought',
        'status',
        'step',
        'production_date',
        'preview',
        'design_file',
        'delivered_date',
        'sector_id',
        'responsable_id'
    ];

    public $casts = [
        'arrival_date' => 'datetime',
        'purchase_date' => 'datetime',
        'delivered_date' => 'datetime',
        'production_date' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sector()
    {
        return $this->belongsTo(ProductionSector::class, 'sector_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function getPreviewAttribute($value)
    {
        return json_decode($value, true);
    }
}
