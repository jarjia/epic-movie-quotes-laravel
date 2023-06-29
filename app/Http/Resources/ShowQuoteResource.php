<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ShowQuoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $quote = $this->resource;

        $quote->thumbnail = asset('storage/' . $quote->thumbnail);
        $quote->user_id = $quote->movies->user_id;

        foreach ($quote->comments as $comment) {
            if (Str::startsWith($comment->user->thumbnail, 'assets')) {
                $comment->user->thumbnail = asset($comment->user->thumbnail);
            } else if (Str::startsWith($comment->user->thumbnail, 'images')) {
                $comment->user->thumbnail = asset('storage/' . $comment->user->thumbnail);
            }
        }

        return $quote->toArray();
    }
}
