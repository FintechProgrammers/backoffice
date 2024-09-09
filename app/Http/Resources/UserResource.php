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
        $firstParent = !empty($this->sponsor) ? $this->sponsor : null; // Direct parent of the current user
        $secondParent = !empty($firstParent) ? $firstParent->sponsor : null; // Parent of the first parent
        $thirdParent = !empty($secondParent) ? $secondParent->sponsor : null; // Parent of the second parent

        return [
            'id' => $this->uuid,
            'user_id' => $this->id,
            'name' => $this->full_name,
            'email' => $this->email,
            'username' => $this->username,
            'profile_picture' => $this->profile_picture,
            'role' => $this->is_ambassador ? 'Ambassador' : 'Customer',
            'subscribed' => $this->id === 6 ? true : false,
            'parents' => ['level1' => new SponsorResource($firstParent), 'level2' => new  SponsorResource($secondParent), 'level3' => new SponsorResource($thirdParent)],
            'push_token' => $this->push_token,
            'created_at' => $this->created_at,
        ];
    }
}
