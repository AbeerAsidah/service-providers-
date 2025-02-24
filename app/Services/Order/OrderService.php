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
            $order = Order::create([
                'user_id' => $userId,
                'total_price' => 0,
                'status' => Constants::ORDER_STATUSES[0],
            ]);
    
            $totalPrice = $cartItems->sum(function ($item) {
                return ($item->quantity * ($item->service->price ?? 0));
            });

            foreach ($cartItems as $item) {
                if ($item->service) {
                    $subtotal = $item->quantity * $item->service->price;
               

                    OrderDetail::create([
                        'order_id' => $order->id,
                        'service_id' => $item->service_id,
                        'provider_id' => $item->service->service_provider_id ?? '1',
                        'price' => $item->service->price,
                        'complete_time_unit' => $item->service->complete_time_unit,
                        'complete_time' => $item->service->complete_time,
                        'quantity' => $item->quantity,
                        'total_price' => $subtotal,
                        'status' => Constants::ORDER_STATUSES[0],
                    ]);
                }
            }

            $order->update(['total_price' => number_format($totalPrice, 2, '.', '')]);

            $this->cartService->clearCart($userId);


            return new OrderResource($order->load('orderDetails.provider', 'orderDetails.service'));
        });
    }

    public function getOrder(int $orderId)
    {
        $order = Order::with([
            'orderDetails.service.provider',
            'orderDetails.service.category',
            'orderDetails.provider'
        ])->findOrFail($orderId);

        return new OrderResource($order);
    }

    public function getAllOrders($status = null, $search = null, $paginate = false, $limit = 10, $userId = null)
    {
        $query = Order::with([
            'orderDetails.service.provider',
            'orderDetails.service.category',
            'orderDetails.provider'
        ]);

        if ($status) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where('id', 'like', "%$search%")
                ->orWhereHas('orderDetails.service', function ($query) use ($search) {
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

        return $paginate ? $query->paginate($limit) : OrderResource::collection($query->orderBy('created_at', 'desc')->get());
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
        $order->status = $validatedData['status'];
        $order->save();
    

        if (in_array($order->status, ['completed', 'canceled'])) {
            $order->orderDetails()->update(['status' => $order->status]);
        }

        $orderUser = $order->user;
        // event(new OrderStatusUpdated($orderUser, $order, $order->status));
    
        return response()->json([
            'success' => true,
            'message' => __('messages.order_status_updated'),
        ]);
    }

    public function getUserOrders(int $userId)
    {
        return [
            "orders" => OrderResource::collection(
                Order::with([
                    'orderDetails.service.provider',
                    'orderDetails.service.category',
                    'orderDetails.provider'
                ])
                ->where('user_id', $userId)  
                ->orderBy('created_at', 'desc')  
                ->get() 
            )
        ];   
     }
    public function getOrderItems(int $orderId)
    {
        return [
            "items" => OrderItemResource::collection(
                $orderItems = OrderItem::with([
                    'service.provider',
                    'service.category',
                    'provider'
                ])->where('order_id', $orderId)
                    ->orderBy('id', 'desc')
                    ->get()
            )
        ];
    }


//provider

    public function getProviderOrderItems($providerId, $status = null, $search = null, $paginate = false, $limit = 10,  $userId = null)
    {
        $query = OrderDetail::query();

        $query->where('provider_id', $providerId)
            ->with([
                'service.category',
            ]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($userId) {
          
            // } else {
                $query->where('user_id', $userId);
            // }
        }

        if (!empty($search)) {
            $query->whereHas('service', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            });
        }


        return $paginate ? $query->paginate($limit) : $query->orderBy('id', 'desc')->get();
    }




    public function getOrderItem(int $orderItemId)
    {
        if (auth()->user()->role == Constants::SERVICE_PROVIDER_ROLE) {
            $orderItem = OrderDetail::with([
                'service.category',
            ])
            ->where('id', $orderItemId)
            ->where('provider_id', auth()->user()->id) 
            ->first();
        } else {
            $orderItem = OrderDetail::with([
                'service.category',
            ])
            ->where('id', $orderItemId)
            ->first();
        }

        return $orderItem;
    }

    public function updateOrderDetailStatus(UpdateOrderDetailRequest $request, $id)
    {
        $validatedData = $request->validated();
        
        $orderDetail = OrderDetail::findOrFail($id);
        if (auth()->user()->hasRole(Constants::SERVICE_PROVIDER_ROLE) && $orderDetail->provider_id !== auth()->id()) {
            return error(__('messages.unauthorized_action'), [], 403);
        }
        $orderDetail->status = $validatedData['status'];
        $orderDetail->save();
    
        $orderUser = $orderDetail->order->user;
        // event(new OrderStatusUpdated($orderUser, $orderDetail, $orderDetail->status));
    
        return response()->json([
            'success' => true,
            'message' => __('messages.status_updated'),
        ]);
    }

}
