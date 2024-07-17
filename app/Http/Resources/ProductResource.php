<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'arrived' => $this->arrived,
            'arrival_date' => $this->arrival_date ? $this->arrival_date->format('d/m/Y') : null,
            'purchase_date' => $this->purchase_date ? $this->purchase_date->format('d/m/Y H:i') : null,
            'in_stock' => $this->in_stock,
            'was_bought' => $this->was_bought,
            'delivered_date' => $this->delivered_date ? $this->delivered_date->format('d/m/Y H:i') : null,
        ];
    }
}
