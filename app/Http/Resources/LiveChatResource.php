<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LiveChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $sender = [
            'id' => $this->user->uuid,
            'name' => $this->user->username,
            'photo' => $this->user->profile_picture
        ];

        return [
            'id' => $this->uuid,
            'message' => $this->message,
            'sender' => $sender,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'formatedDate' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'formatedTime' => Carbon::parse($this->created_at)->format('g:i A')
        ];
    }
}
