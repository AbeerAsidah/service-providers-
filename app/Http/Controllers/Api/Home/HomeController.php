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


   

}
