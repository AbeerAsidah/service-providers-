<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="SectionResource",
 *     type="object",
 *     title="Section",
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="image", type="string", example="exampleImageUrl"),
 * )
 */
class SectionResource extends JsonResource
{

    
    /**
     * Transform the resource into an array.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Pagination\AbstractPaginator
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
    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $array = [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        if($this->parent_id)
            $array['parent_id'] = $this->parent_id;
        return $array;
    }
}
