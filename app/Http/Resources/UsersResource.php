<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->map(function ($user) {
            $userImage = '';
            if (strpos($user->thumbnail, 'assets') === 0) {
                $userImage = asset($user->thumbnail);
            } else {
                $userImage = asset('storage/' . $user->thumbnail);
            }

            $user['thumbnail'] = $userImage;

            return $user;
        })->toArray();
    }
}
