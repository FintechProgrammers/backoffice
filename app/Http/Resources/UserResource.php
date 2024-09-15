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
        // Determine parents up to the third level
        $firstParent = $this->sponsor ?? null;
        $secondParent = $firstParent?->sponsor ?? null;
        $thirdParent = $secondParent?->sponsor ?? null;

        return [
            'id' => $this->uuid,
            'user_id' => $this->id,
            'name' => $this->full_name,
            'email' => $this->email,
            'username' => $this->username,
            'profile_picture' => $this->profile_picture,
            'role' => $this->is_ambassador ? 'Ambassador' : 'Customer',
            'subscribed' => $this->id === 6,
            'parents' => [
                'level1' => !empty($firstParent) ? new SponsorResource($firstParent) : null,
                'level2' => !empty($secondParent) ? new SponsorResource($secondParent) : null,
                'level3' => !empty($thirdParent) ? new SponsorResource($thirdParent) : null,
            ],
            'push_token' => $this->push_token,
            'created_at' => $this->created_at,
        ];
    }
}
