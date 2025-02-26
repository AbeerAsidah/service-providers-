<?php

namespace App\Http\Controllers\Api\Order;
use Illuminate\Support\Facades\Log;

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

   

    // Admin
    public function index(Request $request)
    {
        try {
            $trashOnly = $request->input('trashOnly',false);
            $paginate = $request->input('paginate', false);
            $limit = $request->input('limit', 10);
            $status = $request->input('status');
            $search = $request->input('search');
            $userId = $request->input('user_id');
            $providerId = $request->input('provider_id');
    
            Log::info('Order index request received', [
                'trashOnly' => $trashOnly,
                'status' => $status,
                'search' => $search,
                'paginate' => $paginate,
                'limit' => $limit,
                'user_id' => $userId,
                'provider_id' => $providerId,
                'request_ip' => $request->ip(),
                'request_user' => auth()->user()->id ?? 'guest'
            ]);
    
            $orders = $this->orderService->getAllOrders(
                $trashOnly,
                $status, 
                $search,
                $paginate,
                $limit,
                $userId,
                $providerId
            );
    
            return success(['orders' => $orders]);
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
    public function getProviderOrders(Request $request)
    {
        try {
            $provider = auth()->id();
            $orders = $this->orderService->getAllOrders(
                false ,
                $request->status, 
                $request->search,
                $request->paginate,
                $request->limit,
                null,
                $provider
            );
            return success(['orders' => $orders]);
        } catch (\Throwable $th) {
            return error($th->getMessage(), [$th->getMessage()], 500);
        }
    }

   
 
    
}
