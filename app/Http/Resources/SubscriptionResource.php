<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->service->uuid,
            'name' => $this->service->name,
            'is_expired' => $this->end_date->isPast(),
            'logo_url' => $this->service->image,
            'banner_url' => $this->service->banner_url,
            'product_url' => $this->service->product_image_url,
            'services' => ProductResource::collection($this->service->serviceProduct)
        ];
    }
}
