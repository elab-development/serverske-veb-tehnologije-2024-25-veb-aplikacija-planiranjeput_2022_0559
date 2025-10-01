<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'user_id' => $this->user_id,
            'place_id' => $this->place_id,
            'rating' => (int) $this->rating,
            'title' => $this->title,
            'body' => $this->body,
            'created_at' => $this->created_at,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'role' => $this->user->role,
                ];
            }),
            'place' => $this->whenLoaded('place', function () {
                return [
                    'id' => $this->place->id,
                    'name' => $this->place->name,
                    'slug' => $this->place->slug,
                    'type' => $this->place->type,
                ];
            }),
        ];
    }
}