<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="OfferResource",
 *     type="object",
 *     title="Offer",
 *     required={"name", "description", "discount", "image"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="discount", type="integer"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="image", type="string", example="exampleImageUrl"),
 * )
 */
class OfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $array = [
            'id' => $this->id ,
            'name' => $this->name ,
            'discount' => $this->discount ,
            'description' => $this->description,
            'image' => $this->image ,
        ];
        if($this->deleted_at)
            $array['deleted_at'] = $this->deleted_at ;
        return $array;
    }
}
