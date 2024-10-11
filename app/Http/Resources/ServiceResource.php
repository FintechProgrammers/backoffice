<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'price' => '$' . number_format($this->price, 2, '.', ','),
            'duration' => convertDaysToUnit($this->duration, $this->duration_unit) . ' ' . $this->duration_unit,
            'description' => $this->description,
            'icon' => $this->image,
            'product_image' => $this->product_image_url,
            'banner' => $this->banner_url,
        ];
    }
}
