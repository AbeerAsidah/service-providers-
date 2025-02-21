<?php

namespace App\Services\Cart;

use App\Models\CartItem;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Http\Resources\CartItemResource;

class CartService
{
    public function addToCart(int $userId, int $serviceId, int $quantity): void
    {
        try {
            $service = Service::findOrFail($serviceId);

            $cartItem = CartItem::where('user_id', $userId)
                ->where('service_id', $serviceId)
                ->first();

            if ($cartItem) {
                $cartItem->update([
                    'quantity' => $cartItem->quantity + $quantity,
                ]);
            } else {
                CartItem::create([
                    'user_id' => $userId,
                    'service_id' => $serviceId,
                    'quantity' => $quantity,
                ]);
            }
        } catch (Exception $e) {
            Log::error('Error adding to cart: ' . $e->getMessage());
            throw new Exception('Failed to add item to cart.');
        }
    }

    public function updateCartItem(int $userId, int $serviceId, int $quantity): void
    {
        try {
            CartItem::where('user_id', $userId)
                ->where('service_id', $serviceId)
                ->update(['quantity' => $quantity]);
        } catch (Exception $e) {
            Log::error('Error updating cart item: ' . $e->getMessage());
            throw new Exception('Failed to update cart item.');
        }
    }

    public function getCart(int $userId)
    {
        try {
                return CartItemResource::collection(
                    CartItem::with(['service', 'service.provider', 'service.category'])
                        ->where('user_id', $userId)
                        ->get()
                );
        } catch (Exception $e) {
            Log::error('Error fetching cart: ' . $e->getMessage());
            throw new Exception('Failed to fetch cart.');
        }
    }

    public function removeFromCart(int $userId, int $serviceId): void
    {
        try {
            CartItem::where('user_id', $userId)
                ->where('service_id', $serviceId)
                ->delete();
        } catch (Exception $e) {
            Log::error('Error removing from cart: ' . $e->getMessage());
            throw new Exception('Failed to remove item from cart.');
        }
    }
}