<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationResource extends JsonResource
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
            'name' => $this->name,
            'country' => $this->country,
            'region' => $this->region,
            'slug' => $this->slug,
            'description' => $this->when($this->description, $this->description),
            'places_count' => $this->whenCounted('places'),
            'places' => PlaceResource::collection($this->whenLoaded('places')),
        ];
    }
}