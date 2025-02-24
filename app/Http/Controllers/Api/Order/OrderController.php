<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Services\Order\OrderService;
use App\Http\Requests\Api\Order\UpdateOrderRequest;
use App\Http\Requests\Api\Order\UpdateOrderDetailRequest;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
//user
    public function placeOrder(Request $request)
    {
        try {
            $order = $this->orderService->placeOrder($request);
            return success(['order' => $order]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function myOrders(Request $request)
    {
        try {
            $orders = $this->orderService->getUserOrders(auth()->id());
            return success([$orders]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function orderItems(Order $order)
    {
        try {
            if ($order->user_id !== auth()->id()) {
                return error(__('messages.unauthorized_access'), [], 403);
            }
            return success([
                'order' => OrderResource::make($order->load([
                    'orderDetails.service.provider', 
                    'orderDetails.service.category',
                    'orderDetails.provider'
                ]))
            ]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    // Admin
    public function index(Request $request)
    {
        try {
            $orders = $this->orderService->getAllOrders(
                $request->status, 
                $request->search,
                $request->paginate,
                $request->limit,
                $request->user_id
            );
            return success(['orders' => $orders]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 500);
        }
    }

    public function getOrderItemsAsAdmin(int $orderId)
    {
        try {
            $items = $this->orderService->getOrderItems($orderId);
            return success(['items' => $items]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 500);
        }
    }

    public function updateStatus(UpdateOrderRequest $request, int $id)
    {
        try {
            return $this->orderService->updateStatus($request, $id);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 500);
        }
    }

    public function deleteOrder($orderId)
    {
        try {
            return $this->orderService->deleteOrder($orderId);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 400);
        }
    }

    public function getOrder(int $orderId)
    {
        try {
            $order = $this->orderService->getOrder($orderId);
            return success($order);
        } catch (\Throwable $th) {
            return error(__('messages.failed_to_fetch_order_details'), ['error' =>$th->getMessage()], $th->getCode());

        }
    }


    //provider
    public function getProviderOrderItems(Request $request)
    {
        try {
            $provider = auth()->id();
            $orders = $this->orderService->getProviderOrderItems(
                $provider,
                $request->status, 
                $request->search,
                $request->paginate,
                $request->limit,
                $request->user_id
            );
            return success(['orders' => $orders]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 500);
        }
    }

    public function getOrderItem(int $orderItemId)
    {
        try {
            $items = $this->orderService->getOrderItem($orderItemId);
            return success(['item' => $items]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 500);
        }
    }
    public function updateOrderDetailStatus(UpdateOrderDetailRequest $request, int $id)
    {
        try {
            return $this->orderService->updateOrderDetailStatus($request, $id);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 500);
        }
    }
    
}
