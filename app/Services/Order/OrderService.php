<?php

namespace App\Services\Order;

use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderDetailResource;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\Cart\CartService;
use App\Http\Requests\Api\Order\UpdateOrderRequest;
use App\Http\Requests\Api\Order\UpdateOrderDetailRequest;
use App\Http\Requests\Api\Order\PlaceOrderRequest;
use App\Constants\Constants;

class OrderService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    

    public function placeOrder(Request $request)
    {
        $validated = $request->all();
        $userId = Auth::id();
        $cartItems = collect($this->cartService->getCart($userId));

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => __('messages.cart_empty')], 400);
        }

        return DB::transaction(function () use ($cartItems, $userId, $validated) {
           
            $orders = [];

            foreach ($cartItems as $item) {
                if ($item->service) {
                    $subtotal = $item->quantity * $item->service->price;
               

                    $order = Order::create([
                        'user_id' => $userId,
                        'service_id' => $item->service_id,
                        'provider_id' => $item->service->service_provider_id ?? '1',
                        'price' => $item->service->price,
                        'complete_time_unit' => $item->service->complete_time_unit ?? 'minutes',
                        'complete_time' => $item->service->complete_time,
                        'quantity' => $item->quantity,
                        'total_price' => $subtotal,
                        'status' => Constants::ORDER_STATUSES[0],
                    ]);
                    $orders[] = new OrderResource($order->load('provider', 'service'));

                }
            }


            $this->cartService->clearCart($userId);


            return $orders;
        });
    }

    public function getOrder(int $orderId)
{
    $user = auth()->user();

    $query = Order::with([
        'service.provider',
        'service.category',
        'provider'
    ])->where('id', $orderId);

    if ($user->role == Constants::SERVICE_PROVIDER_ROLE) {
        $query->where('provider_id', $user->id);
    } elseif ($user->role == Constants::USER_ROLE) {
        $query->where('user_id', $user->id);
    }

    $order = $query->first();

    if (!$order) {
        return response()->json(['message' => __('messages.failed_to_fetch_order_details')], 403);
    }

    return new OrderResource($order);
}

    public function getAllOrders($trashOnly = false, $status = null, $search = null, $paginate = false, $limit = 10, $userId = null, $providerId = null)
    {
        $query = Order::with([
            'service.provider',
            'service.category',
            'provider'
        ]);
        if ($trashOnly) {
            $query->onlyTrashed();
        }
        if ($status) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where('id', 'like', "%$search%")
                ->orWhereHas('service', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                });
        }

        if ($userId) {
            // if ($role === Constants::SERVICE_PROVIDER_ROLE) {
            //     $query->whereHas('orderDetails.service', function ($query) use ($userId) {
            //         $query->where('service_provider_id', $userId);
            //     });
            // } else {
                $query->where('user_id', $userId);
            // }
        }

        if ($providerId) {
                $query->where('provider_id', $providerId);
        }

        return $paginate ? $query->paginate($limit) : OrderResource::collection($query->orderByDesc($trashOnly ? 'deleted_at' : 'created_at')->get());
    }



    public function deleteOrder(int $orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    

    public function updateStatus(UpdateOrderRequest $request, $id)
    {
        $validatedData = $request->validated();
    
        $order = Order::findOrFail($id);
        $user = auth()->user();
        if (auth()->user()->hasRole(Constants::SERVICE_PROVIDER_ROLE) && $order->provider_id !== auth()->id()) {
            return error(__('messages.unauthorized_action'), [], 403);
        }

        if ($user->hasRole(Constants::USER_ROLE) && $order->user_id !== $user->id) {
            return error(__('messages.unauthorized_action'), [], 403);
        }


    // 🔹 منطق تحديث الحالة باستخدام Constants::ORDER_STATUSES
    if ($validatedData['status'] === Constants::ORDER_STATUSES[2]) { // 'completed'
        if ($order->status === Constants::ORDER_STATUSES[1]) { // 'in_progress'
            $order->status = Constants::ORDER_STATUSES[4]; // 'waiting_for_complete'
        } elseif ($order->status === Constants::ORDER_STATUSES[4]) { // 'waiting_for_complete'
            $order->status = Constants::ORDER_STATUSES[2]; // 'completed'
        }
    } elseif ($validatedData['status'] === Constants::ORDER_STATUSES[3]) { // 'canceled'
        if ($order->status === Constants::ORDER_STATUSES[1]) { // 'in_progress'
            $order->status = Constants::ORDER_STATUSES[5]; // 'waiting_for_cancel'
        } elseif ($order->status === Constants::ORDER_STATUSES[5]) { // 'waiting_for_cancel'
            $order->status = Constants::ORDER_STATUSES[3]; // 'canceled'
        }
    } else {
        $order->status = $validatedData['status']; 
    }
        $order->save();
    

        // if (in_array($order->status, ['completed', 'canceled'])) {
        //     $order->orderDetails()->update(['status' => $order->status]);
        // }

        $orderUser = $order->user;
        // event(new OrderStatusUpdated($orderUser, $order, $order->status));
    
        return response()->json([
            'success' => true,
            'message' => __('messages.order_status_updated'),
            'order_status' => $order->status
        ], 200);
    }

    public function getUserOrders(int $userId)
    {
        return [
            "orders" => OrderResource::collection(
                Order::with([
                    'service.provider',
                    'service.category',
                    'provider'
                ])
                ->where('user_id', $userId)  
                ->orderBy('created_at', 'desc')  
                ->get() 
            )
        ];   
     }
 



    



   
}
