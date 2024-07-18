<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'date' => $this->date,
            'delivery_date' => $this->delivery_date,
            'customer' => CustomerResource::make($this->customer),
            'order_products' => OrderProductResource::collection($this->orderProducts),
        ];
    }
}
