<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public static function collection($data)
    {
        /* is_a() makes sure that you don't just match AbstractPaginator
         * instances but also match anything that extends that class.
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
    public function toArray($request)
    {
        // $serviceResource = $this->relationLoaded('service') && $this->service ? new ServiceResource($this->service) : __('messages.service_not_available');
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            // 'service_id' => $this->service_id,
            'quantity' => $this->quantity,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'complete_time_unit' => $this->complete_time_unit,
            'complete_time' => $this->complete_time,
            'service' => $this->whenLoaded('service', fn() => new ServiceResource($this->service), __('messages.service_not_available')),
            'provider' => $this->whenLoaded('provider', fn() => [
                'name' => $this->provider->name,
                'phone_number' => $this->provider->phone_number
            ], __('messages.provider_not_available')),       
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at, ];
        
    }
}
