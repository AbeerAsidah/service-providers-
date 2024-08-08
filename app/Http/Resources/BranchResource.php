<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="BranchResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(type="string", example="image_url")
 *     ),
 *     @OA\Property(property="name", type="object",
 *         @OA\Property(property="en", type="string", example="exampleValueEN"),
 *         @OA\Property(property="ar", type="string", example="exampleValueAR")
 *     ),
 *     @OA\Property(property="description", type="object",
 *         @OA\Property(property="en", type="string", example="exampleValueEN"),
 *         @OA\Property(property="ar", type="string", example="exampleValueAR")
 *     ),
 *     @OA\Property(property="location", type="object",
 *         @OA\Property(property="en", type="string", example="exampleValueEN"),
 *         @OA\Property(property="ar", type="string", example="exampleValueAR")
 *     ),
 *     @OA\Property(property="email", type="string", example="exampleEmail@gmail.com"),
 *     @OA\Property(property="phone", type="string", example="+963629842364623"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 */
class BranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public static function collection($data)
    {
        /*
        This simply checks if the given data is and instance of Laravel's paginator classes
         and if it is,
        it just modifies the underlying collection and returns the same paginator instance
        */
        if (is_a($data, \Illuminate\Pagination\AbstractPaginator::class)) {
            $data->setCollection(
                $data->getCollection()->map(function ($listing) {
                    return new static($listing);
                })
            );
            return $data;
        }

        return parent::collection($data);
    }
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'email' => $this->email,
            'phone' => $this->phone,
            'locatione' => $this->location,
            'images' => $this->images,
        ];
    }
}
