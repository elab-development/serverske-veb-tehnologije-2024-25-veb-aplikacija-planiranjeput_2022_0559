<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array    
    {
        return [
            'id' => $this->id,
            'destination_id' => $this->destination_id,
            'name' => $this->name,
            'type' => $this->type,
            'slug' => $this->slug,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'price_level' => $this->price_level,
            'rating_avg' => (float) $this->rating_avg,
            'reviews_count' => (int) $this->reviews_count,
            'destination' => $this->whenLoaded('destination', function () {
                return [
                    'id' => $this->destination->id,
                    'name' => $this->destination->name,
                    'slug' => $this->destination->slug,
                    'country' => $this->destination->country,
                ];
            }),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}