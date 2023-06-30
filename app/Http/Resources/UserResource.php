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
        $userImage = '';
        if (strpos($this->resource->thumbnail, 'assets') === 0) {
            $userImage = asset($this->resource->thumbnail);
        } else {
            $userImage = asset('storage/' . $this->resource->thumbnail);
        }

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'thumbnail' => $userImage,
            'google_id' => $this->resource->google_id,
            'remember_token' => $this->resource->remember_token
        ];
    }
}
