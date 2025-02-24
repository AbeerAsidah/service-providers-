<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ReviewResource;


class ServiceResource extends JsonResource
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
            'provider' => $this->whenLoaded('provider', function () {
                return [
                    'name' => $this->provider->name,
                    'phone_number' => $this->provider->phone_number,
                ];
            }),       
            'category' => new CategoryResource($this->whenLoaded('category')),
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'complete_time' => $this->complete_time,
            'complete_time_unit' => $this->complete_time_unit,
            'status' => $this->status,
            'image' => $this->image ? asset("storage/{$this->image}") : null,
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

        ];
    }
}
 