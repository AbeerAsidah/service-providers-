<?php

namespace App\Services\General\Notification;

use App\Models\User;
use App\Constants\Constants;
use App\Constants\Notifications;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\NotificationRecourse;

class NotificationService
{
    protected ?User $user;

    public function __construct()
    {
        // @phpstan-ignore-next-line
        $this->user = auth('sanctum')->user();
    }



    public function getAllNotifications($hasRead = null, $countOnly = null)
    {
        $notifications = $this->user->notifications();
        if ($hasRead !== null) {
            $notifications->where('has_read', $hasRead);
        }
        if ($countOnly) {
            return $notifications->count();
        }
        $notifications = $notifications->orderBy('id', 'DESC')->paginate(config('app.pagination_limit'));
        return NotificationRecourse::collection($notifications);
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
        return  $this->user->notifications()->update(['has_read' => 1]);
    }



    public function pushAdminsNotifications($notification, $admins = null)
    {
        switch ($notification['STATE']) {
            case Notifications::NEW_REGISTRATION['STATE']:
                $description = [
                    'ar' => __('notifications.new_registration_description', [], 'ar'),
                    'en' => __('notifications.new_registration_description', [], 'en'),
                ];
                $title = [
                    'ar' => __('notifications.new_registration_title', [], 'ar'),
                    'en' => __('notifications.new_registration_title', [], 'en'),
                ];
                break;
            default:
                return;
        }
        if (!$admins) {
            // @phpstan-ignore-next-line
            $admins = User::whereHas('role', function ($q) {
                $q->where('name', Constants::ADMIN_ROLE);
            })->get();
        }

        $admins->map(function ($admin) use ($title, $description) {
            $this->pushNotification(
                $title,
                $description,
                Notifications::NEW_REGISTRATION['TYPE'],
                Notifications::NEW_REGISTRATION['STATE'],
                $admin,
                class_basename($this->user),
                $this->user->id,
            );
        });
    }

    public function pushNotification($title, $description, $type, $state, $user, $modelType, $modelId, $checkDuplicated = false)
    {
        $data = [
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'state' => $state,
            'model_id' => $modelId,
            'model_type' => $modelType,
        ];

        if ($checkDuplicated) {
            $filteredData = array_diff_key($data, array_flip(['title', 'description']));
            $user->notifications()->firstOrCreate($filteredData, $data);
        } else
            $user->notifications()->create($data);
    }
}
