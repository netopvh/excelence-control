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
            'in_stock' => $this->in_stock,
            'was_bought' => $this->was_bought,
        ];
    }
}
