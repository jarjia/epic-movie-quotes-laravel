<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class AllQuoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->map(function ($quote) {
            $userThumbnail = '';
            if (Str::startsWith($quote->movies->user->thumbnail, 'assets')) {
                $userThumbnail = asset($quote->movies->user->thumbnail);
            } else {
                $userThumbnail = asset('storage/' . $quote->movies->user->thumbnail);
            }
            $quote['thumbnail'] = asset('storage/' . $quote->thumbnail);
            $quote->movies->thumbnail = Str::startsWith($quote->movies->thumbnail, 'http') ?
                $quote->movies->thumbnail : asset('storage/' . $quote->movies->thumbnail);
            $quote->movies->user->thumbnail = $userThumbnail;
            foreach ($quote->comments as $comment) {
                if (Str::startsWith($comment->user->thumbnail, 'assets')) {
                    $comment->user->thumbnail = asset($comment->user->thumbnail);
                } elseif (Str::startsWith($comment->user->thumbnail, 'images')) {
                    $comment->user->thumbnail = asset('storage/' . $comment->user->thumbnail);
                }
            }

            return $quote;
        })->toArray();
    }
}
