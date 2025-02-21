<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CartItemResource",
 *     type="object",
 *     title="CartItemResource",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 * )
 */

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ,
            'service_id' => $this->service_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'user' => [ 
                'id' => $this->user->id,
                'username' => $this->user->name,
            ],
            'service' => new ServiceResource($this->service), 
            'created_at' => $this->created_at ,
            'updated_at' => $this->updated_at ,
            'deleted_at' => $this->deleted_at ,
        ];
    }

  
}
