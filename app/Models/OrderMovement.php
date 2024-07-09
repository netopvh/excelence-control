<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrderMovement
 *
 * @property int $id
 * @property string|null $action_date
 * @property int $order_id
 * @property int|null $responsable_id
 * @property string|null $accepted_date
 * @property string $movement_type
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement whereAcceptedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement whereActionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement whereMovementType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement whereResponsableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement whereUpdatedAt($value)
 * @property string|null $action_type
 * @property int|null $action_user_id
 * @property string|null $origin
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement whereActionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement whereActionUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMovement whereOrigin($value)
 * @mixin \Eloquent
 */
class OrderMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_date',
        'action_type',
        'action_user_id',
        'order_id',
        'origin',
        'arrived',
        'movement_type',
        'description'
    ];
}
