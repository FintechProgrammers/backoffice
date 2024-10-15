<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class AllSignalResource extends JsonResource
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
            'streamer' => new StreamerUserResource($this->streamer),
            'asset' => new AssetResource($this->asset),
            'category' => new CategoryResource($this->category),
            'order_type' => $this->order_type,
            'entry_price' => $this->entry_price,
            'stop_loss' => $this->stop_loss,
            'target_price' => $this->target_price,
            'percentage' => $this->percentage,
            'comment' => $this->comment,
            'caption' => Str::limit(strip_tags($this->comment), 30, '...'),
            // 'photo' => $this->photo,
            'chart_photo' => $this->chart_photo,
            'status' => $this->market_status,
            'is_updated' => $this->is_updated,
            'created_at' => $this->created_at,
            'formatedDate' => Carbon::parse($this->created_at)->format('M j, Y, g:i A'),
            'document' => $this->file_url
        ];
    }
}
