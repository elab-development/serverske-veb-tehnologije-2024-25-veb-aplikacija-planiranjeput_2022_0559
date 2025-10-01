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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when($request->user()?->isAdmin(), $this->email),
            'role' => $this->role,
            'reviews_count' => $this->whenCounted('reviews'),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
        ];
    }
}