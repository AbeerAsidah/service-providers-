<?php

namespace App\Services\Notification;

use App\Models\User;
use App\Traits\HasFcmToken;
use App\Constants\Constants;
use App\Constants\Notifications;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\NotificationResource;

class NotificationService
{
    use HasFcmToken;
    protected ?User $user;

    public function __construct()
    {
        // @phpstan-ignore-next-line
        $this->user = auth('sanctum')->user();
    }

    public function getAllNotifications($hasRead = null, $countOnly = null , $is_broadcast = null)
    {
        $notifications = $this->user->notifications();
        
        if ($hasRead !== null) {
            $notifications->where('has_read', $hasRead);
        }
        
        if ($is_broadcast !== null) {
            $notifications->where('is_broadcast', $is_broadcast);
        }
        
        $notificationsCount = $notifications->count();

        if ($countOnly) {
            return $notificationsCount ;
        }
        $notifications = $notifications->orderByDesc('id')->paginate(config('app.pagination_limit'));
        return NotificationResource::collection($notifications);
    }
    
    public function getNotificationTypeStatistics($hasRead = null)
    {
        $stats = $this->user->notifications();
        if ($hasRead !== null) {
            $stats->where('has_read', $hasRead);
        }
        return $stats->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type');
    }


    public function readAllNotifications()
    {
        return $this->user->notifications()->where('has_read', 0)->update(['has_read' => 1]);
    }



    public function pushAdminsNotifications($notification, $admins = null)
    {
        // if (!$admins) {
        //     // @phpstan-ignore-next-line
        //     $admins = User::whereHas('role', function ($q) {
        //         $q->where('name', Constants::ADMIN_ROLE);
        //     })->get();
        // }

        // $admins->map(function ($admin) {
        //     $this->pushNotification(

        //     );
        // });
    }

    public function pushNotification(string $title, string $description, string $type, $state, User $user, string $modelType, int|string $modelId, bool $checkDuplicated = false, string|array $additional_data = [],bool $is_broadcast = false)
    {
        $data = [
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'state' => $state,
            'model_id' => $modelId,
            'model_type' => $modelType,
            'additional_data' => $additional_data,
            'is_broadcast' => $is_broadcast,
        ];

        if ($checkDuplicated) {
            $filteredData = array_diff_key($data, array_flip(['title', 'description']));
            $user->notifications()->firstOrCreate($filteredData, $data);
        } else {
            $notification = $user->notifications()->create($data);
        }
    }
}
