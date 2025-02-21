<?php

namespace App\Http\Controllers\Api\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Cart\StoreCartItemRequest;
use App\Http\Requests\Api\Cart\UpdateCartItemRequest;
use App\Services\Cart\CartService;
use Exception;

  
class CartItemController extends Controller
{

    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function addToCart(StoreCartItemRequest $request)
    {
        try {
            $this->cartService->addToCart(auth()->id(), $request->service_id, $request->quantity);
            return success(['message' => __('messages.item_added_to_cart')]);
        } catch (Exception $e) {
            return error($e->getMessage(), [], 400);
        }
    }

    public function updateCart(UpdateCartItemRequest $request)
    {
        try {
            $validated = $request->validated();
            $this->cartService->updateCartItem(auth()->id(), $validated['service_id'], $validated['quantity']);
            return success(['message' => __('messages.cart_updated')]);
        } catch (Exception $e) {
            return error($e->getMessage(), [], 400);
        }
    }

    public function viewCart()
    {
        try {
            $cart = $this->cartService->getCart(auth()->id());
            return success([
                'data' => [
                    'cart' => $cart,
                ],
                'message' => __('messages.cart_fetched'),
            ]);
        } catch (Exception $e) {
            return error($e->getMessage(), [], 400);
        }
    }

    public function removeFromCart(Request $request)
    {
        try {
            $request->validate([
                'service_id' => 'required|exists:services,id',
            ]);

            $this->cartService->removeFromCart(auth()->id(), $request->service_id);
            return success(['message' => __('messages.item_removed_from_cart')]);
        } catch (Exception $e) {
            return error($e->getMessage(), [], 400);
        }
    }

}

