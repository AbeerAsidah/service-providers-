<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ReviewResource;
use App\Services\Wallet\WalletService; 


class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $walletService = app(WalletService::class);

        $averageRating = $this->reviews->avg('rating') ?? 0;

        return [
            'id' => $this->id,
            'provider' => $this->whenLoaded('provider', function () use ($walletService) {
                return [
                    'id' => $this->provider->id,
                    'name' => $this->provider->username,
                    'phone_number' => $this->provider->phone_number,
                    'about' => $this->provider->about,
                    'wallet_balance' => $walletService->getBalance($this->provider->id), 

                ];
            }),       
            'category' => new CategoryResource($this->whenLoaded('category')),
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'complete_time' => $this->complete_time,
            // 'complete_time_unit' => $this->complete_time_unit,
            'status' => $this->status,
            'image' => $this->image ?? null,
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'average_rating' => $averageRating, 
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

        ];
    }
}
 