<?php
namespace App\Http\Controllers\Api\Home;

use App\Constants\Constants;
use App\Http\Controllers\Controller;
use App\Http\Resources\SectionResource;
use App\Models\Product;
use App\Http\Resources\ServiceResource;
use Carbon\Carbon;
use App\Services\Category\CategoryService;
use App\Services\Service\ServService;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function __construct(protected ServService $servService, protected CategoryService $categoryService)
    {
    }

   
    public function index(Request $request)
    {
        try {
            $catigories = $this->categoryService->getAllCategories();
         
            $trendingServices = $this->servService->getTrendingServices();
            $trendingServicesResource = ServiceResource::collection($trendingServices);

            // $user = auth('sanctum')->user();
            // // $unreadNotificationsCount = $user->notifications()->whereNull('read_at')->count();
            // $unreadNotificationsCount = 0;
            // if ($user) {
            //     $unreadNotificationsCount = $user->notifications()->whereNull('read_at')->count();
            // }
            $data = [
              
                'categories' => $catigories,
                'trending_services' => $trendingServicesResource,

                // 'unread_notifications_count' => $unreadNotificationsCount,
            ];

            return success($data, 200, ['message' => __('messages.data_fetched_successfully')]);
        } catch (\Exception $e) {
            return error(__('messages.failed_to_fetch_data'), ['error' => $e->getMessage()], $e->getCode());
        }
    }


     /**
     * @OA\Get(
     *     path="/admin/home",
     *     tags={"Admin", "Admin - home"},
     *     summary="Retrieve all home data",
     *     security={{"BearerAuth": {}, "LocaleAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Order statistics fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="status_counts", type="object",
     *                 @OA\Property(property="waiting_payment", type="integer", example=320),
     *                 @OA\Property(property="pricing", type="integer", example=210),
     *                 @OA\Property(property="shipping", type="integer", example=100),
     *                 @OA\Property(property="completed", type="integer", example=50)
     *             ),
     *             @OA\Property(property="orders_per_day", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="date", type="string", example="2024-02-10"),
     *                     @OA\Property(property="count", type="integer", example=75)
     *                 )
     *             ),
     *             @OA\Property(property="latest_orders", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=3278),
     *                     @OA\Property(property="user_name", type="string", example="John Doe"),
     *                     @OA\Property(property="status", type="string", example="بانتظار الدفع"),
     *                     @OA\Property(property="created_at", type="string", example="2024-02-12 14:30:00")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error.",
     *         @OA\JsonContent(ref="#/components/schemas/error")
     *     )
     * )
     */
    public function indexForAdmin()
    {
        try {
            $statusCounts = [
                (strtolower(Constants::ORDER_STATUSES[0])) => Order::where('status', Constants::ORDER_STATUSES[0])->count(),
                (strtolower(Constants::ORDER_STATUSES[5])) => Order::where('status', Constants::ORDER_STATUSES[5])->count(),
                (strtolower(Constants::ORDER_STATUSES[2])) => Order::where('status', Constants::ORDER_STATUSES[2])->count(),
                (strtolower(Constants::ORDER_STATUSES[3])) => Order::where('status', Constants::ORDER_STATUSES[3])->count(),
                (strtolower(Constants::ORDER_STATUSES[6])) => Order::where('status', Constants::ORDER_STATUSES[6])->count(),
                (strtolower(Constants::ORDER_STATUSES[7])) => Order::where('status', Constants::ORDER_STATUSES[7])->count(),
            ];

            $startDate = Carbon::now()->subDays(29)->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            $ordersPerDayRaw = Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->pluck('count', 'date');

            $ordersPerDay = [];
            for ($i = 0; $i < 30; $i++) {
                $date = Carbon::now()->subDays(29 - $i)->toDateString();
                $ordersPerDay[] = [
                    'date' => $date,
                    'count' => $ordersPerDayRaw[$date] ?? 0,
                ];
            }


            $latestOrders = Order::with(['items' , 'user' , 'userLocation'])->orderBy('created_at', 'DESC')->limit(15)->get();
            $latestOrders = OrderResource::collection($latestOrders);

            return success([
                'status_counts' => $statusCounts,
                'orders_in_last_30_days' => $ordersPerDay,
                'latest_orders' => $latestOrders,
            ], __('messages.data_fetched_successfully'));
        } catch (\Exception $e) {
            return error(__('messages.failed_to_fetch_data'), ['error' => $e->getMessage()], $e->getCode());
        }
    }

}
