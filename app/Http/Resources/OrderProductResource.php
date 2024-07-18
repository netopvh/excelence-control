<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OrderProductResource extends JsonResource
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
            'supplier' => $this->supplier,
            'qtd' => $this->qtd,
            'design_file' => $this->design_file ? Storage::disk('s3')->url($this->design_file) : null,
            'preview' => $this->getPreview($this->preview),
            'noimage' => asset('media/photos/noimage.jpg'),
            'product' => new ProductResource($this->product)
        ];
    }

    private function getPreview($preview): array
    {

        if (!is_null($preview) && count($preview) > 0) {
            foreach ($preview as $key => $value) {
                $preview[$key] = Storage::disk('s3')->url($value);
            }
            return $preview;
        }
        return [];
    }
}
