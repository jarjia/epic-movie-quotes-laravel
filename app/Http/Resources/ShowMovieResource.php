<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowMovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        foreach ($this->resource->quotes as $quote) {
            $quote->thumbnail = asset('storage/' . $quote->thumbnail);
        }

        $this->resource->thumbnail = asset('storage/' . $this->resource->thumbnail);

        return $this->resource->toArray();
    }
}
