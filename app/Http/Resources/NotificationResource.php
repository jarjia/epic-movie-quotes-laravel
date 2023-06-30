<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        foreach ($this->resource as $notification) {
            if (strpos($notification->from->thumbnail, 'assets') === 0) {
                $notification->from->thumbnail = asset($notification->from->thumbnail);
            } elseif (strpos($notification->from->thumbnail, 'images') === 0) {
                $notification->from->thumbnail = asset('storage/' . $notification->from->thumbnail);
            }
        }
        return $this->resource->toArray();
    }
}
