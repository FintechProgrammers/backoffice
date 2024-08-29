<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'user_id' => $this->id,
            'name' => $this->full_name,
            'email' => $this->email,
            'username' => $this->username,
            'profile_picture' => $this->profile_picture,
            'role' => $this->is_ambassador ? 'Ambassador' : 'Customer',
            'push_token' => $this->push_token
        ];
    }
}
