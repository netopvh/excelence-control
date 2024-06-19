<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_date',
        'order_id',
        'responsable_id',
        'accepted_date',
        'movement_type',
        'description'
    ];
}
