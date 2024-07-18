<?php

namespace App\Services\Auth;

use Closure;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\AuthCode;
use App\Constants\Constants;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\Response;
use App\Constants\Notifications;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserRecourse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;
use App\Http\Requests\Api\LoginRequest;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Resources\NotificationRecourse;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Requests\Api\ChangePasswordRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\Api\SendVerificationCodeRequest;
use App\Http\Requests\Api\CheckVerificationCodeRequest;

class AuthService
{
    protected ?User $user;
    protected NotificationService $notificationService;
    protected UserService $userService;
    public function __construct(NotificationService $notificationService, UserService $userService)
    {
        $this->notificationService = $notificationService;
        $this->userService = $userService;
        $this->user = auth('sanctum')->user();
    }



    public function updateProfile(UpdateProfileRequest $request)
    {

        $data = $request->validated();
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($this->user->images()->count() - count($request->trash_images_ids ?? []) + count($request->images ?? []) > 4) {
            throw new Exception(__('messages.maximum_images_count'), 422);
        }
        /**this for multi images  */
        // if (is_array($request->trash_images_ids))
        //     $this->user->images()->whereIn('id', $request->trash_images_ids)->delete();
        // if (is_array($request->images))
        //     foreach ($request->images as $image) {
        //         $this->user->images()->create([
        //             'image' => $image->storePublicly('users/profile', 'public'),
        //         ]);
        //     }

        $this->userService->handleUserImage($this->user, $request);
        $this->user->update($data);

        //if the user has notification 
        $notifications = $this->notificationService->getAllNotifications();

        return success(
            UserRecourse::make(User::with('images')->where('id' , $this->user->id)->first()),
            200,
            [
                'notifications' => $notifications ?? null,
                //consider that the notifications comes from many types for example messages , new visit , new ad ..etc .
                'notifications_types_stats' => $this->notificationService?->getNotificationTypeStatistics(0) ?? [],
                'notifications_count' => $this->notificationService?->getAllNotifications(0, true) ?? [],
            ]
        );
    }
    public function getProfile(Request $request)
    {
        //if the user has notification 
        $notifications = $this->notificationService->getAllNotifications();
        //if wanted the updated the status of has_read to true then nust pass read=1 param .
        if ($request->read)
            $this->notificationService->readAllNotifications();
        return success(
            UserRecourse::make($this->user->load('images')),
            200,
            [
                'notifications' => $notifications,
                //consider that the notifications comes from many types for example messages , new visit , new ad ..etc .
                'notifications_types_stats' => $this->notificationService->getNotificationTypeStatistics(0),
                'notifications_count' => $this->notificationService->getAllNotifications(0, true),
            ]
        );
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('username', $request->username)
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', Constants::ADMIN_ROLE);
            })
            ->first();
        if (!$user)
            throw new Exception(__('messages.username_or_email_are_not_correct'), 422);
        if (!Hash::check($request->password, $user->password))
            throw new Exception(__('messages.wrong_password'), 422);

        $token = $user->createToken('auth')->plainTextToken;
        if ($request->fcm_token) {
            $this->userService->handelFcmToken($user, $request->fcm_token);
        }
        $user['token'] = $token;
        return ['user' => new UserRecourse($user)];
    }

    public function register(FormRequest $request): array
    {
        $user = $this->userService->createUser($request);
        //todo complet it 
        // $this->notificationService->pushAdminsNotifications(Notifications::NEW_REGISTRATION, $user);
        return ['user' => new UserRecourse($user)];
    }
    function logout()
    {
        $this->user->tokens()->where('id', $this->user->currentAccessToken()->id)->delete();
        return true;
    }
    public function changePassword(ChangePasswordRequest $request)
    {

        if (Hash::check($request->old_password, $this->user->password)) {
            $this->user->update(
                ['password' => Hash::make($request->password)]
            );
            return true;
        }
        throw new Exception(__('messages.wrong_old_password'), 422);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $passwordResetCode = AuthCode::where('email', $request->email)
            ->where('code', $request->verification_code)
            ->where('expired_at', '>', Carbon::now()->format('Y-m-d H:i:s'))->first();
        if (!$passwordResetCode) {
            throw new Exception(__('messages.invalid_verification_code'), 422);
        }
        $passwordResetCode->delete();
        $user = User::where('email', $request->email)->first();
        $user->update(
            ['password' => Hash::make($request->password)]
        );
        return true;
    }

    public function checkVerificationCode(CheckVerificationCodeRequest $request)
    {
        $queryCol = $this->user ? 'user_id' : 'email';
        $passwordResetCode = AuthCode::where($queryCol, $queryCol == 'email' ? $request->email : $this->user->id)
            ->where('code', $request->verification_code)
            ->where('expired_at', '>', Carbon::now()->format('Y-m-d H:i:s'))->first();
        // this part for activation after signup if needed . 
        if ($this->user) {
            if (!$passwordResetCode) {
                throw new Exception(__('messages.invalid_verification_code'), 422);
            }
            $passwordResetCode->delete();
            $this->user->update(['is_active' => 1, 'email_verified_at' => Carbon::now()]);
            $response = [
                'message' => __('messages.your_account_has_been_activated'),
            ];
            return $response;
        }
        $response = [
            'code' => $request->verification_code,
            'is_valid' => $passwordResetCode ? true : false,
        ];
        return $response;
    }


    public function sendVerificationCode(SendVerificationCodeRequest $request)
    {

        $authToVerfiy = [];
        // this part for activation after signup if needed . 
        if ($this->user) {
            if ($this->user->is_active) {
                throw new Exception(__('messages.you_have_already_activate_your_account'), 422);
            }
            $authToVerfiy['user_id'] = $this->user->id;
        } else {
            $authToVerfiy['email'] = $request->email;
        }
        $code = rand(1000, 9999);
        $details = [
            'title' => __('messages.your_verification_code_is'),
            'body' => $code,
        ];
        Mail::to($this->user ? $this->user->email : $request->email)->send(new \App\Mail\VerificationCode($details));
        AuthCode::create(array_merge([
            'code' => $code,
            'expired_at' => Carbon::now()->addMinutes(15)->format('Y-m-d H:i:s')
        ], $authToVerfiy));
        return true;
    }
}
