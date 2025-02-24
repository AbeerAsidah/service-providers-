<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
        // $totalPrice = number_format($this->items->sum(function ($item) {
        //     return $item->total_price; 
        // }), 2, '.', ''); 
    
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id ?? null,
                'username' => $this->user->username ?? null,
            ],
            'status_name' =>  $this->status,
            // 'status' => __('orders.' . $this->status),
            // 'payment_id' => $this->payment_id,                
            'items' => OrderDetailResource::collection($this->orderDetails),
            'total_price' => $this->total_price, 
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
             

        ];
    }
}
