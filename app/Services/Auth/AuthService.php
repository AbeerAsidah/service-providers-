<?php

namespace App\Services\Auth;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\AuthCode;
use App\Constants\Constants;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Services\User\UserService;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Services\Notification\NotificationService;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\UpdateProfileRequest;
use App\Http\Requests\Api\Auth\SignUpRequest;
use App\Http\Requests\Api\Auth\SignUpServiceProviderRequest;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\Auth\SendVerificationCodeRequest;
use App\Http\Requests\Api\Auth\CheckVerificationCodeRequest;

class AuthService
{
    protected ?User $user;
    protected NotificationService $notificationService;
    protected UserService $userService;

    public function __construct(NotificationService $notificationService, UserService $userService)
    {
        $this->notificationService = $notificationService;
        $this->userService = $userService;
        // @phpstan-ignore-next-line
        $this->user = auth('sanctum')->user();
    }

    public function getProfile(Request $request): JsonResponse
    {
        //if the user has notification
        // $notifications = $this->notificationService->getAllNotifications();
        //if wanted the updated the status of has_read to true then must pass read=1 param .
        // if ($request->read)
        //     $this->notificationService->readAllNotifications();
        return success(
            UserResource::make($this->user?->load('images')),
            200
            // [
            //     'notifications' => $notifications,
            //     //consider that the notifications comes from many types for example messages , new visit , new ad ..etc .
            //     'notifications_types_stats' => $this->notificationService->getNotificationTypeStatistics(0),
            //     'notifications_count' => $this->notificationService->getAllNotifications(0, true),
            // ]
        );
    }

    /**
     * @throws Exception
     */
    public function login(LoginRequest $request, ?bool $isAdmin = false): array
    {
        $fun = $isAdmin ? 'whereHas' : 'whereDoesntHave';
        $user = User::where('username', $request->username)
            ->$fun('roles', function ($q) {
                $q->where('name', Constants::ADMIN_ROLE);
            })
            ->first();
        if (!$user)
            throw new Exception(__('messages.username_is_not_correct'), 422);
        if (!Hash::check($request->password, $user->password))
            throw new Exception(__('messages.wrong_password'), 422);

        $token = $user->createToken('auth')->plainTextToken;
        if ($request->fcm_token) {
            $this->userService->handleFcmToken($user, $request->fcm_token);
        }
        $user['token'] = $token;
        return ['user' => new UserResource($user)];
    }

    function logout(): true
    {
        // @phpstan-ignore-next-line
        $this->user->tokens()->where('id', $this->user->currentAccessToken()->id)->delete();
        return true;
    }

    /**
     * @throws Exception
     */
    public function changePassword(ChangePasswordRequest $request): true
    {

        if (Hash::check($request->old_password, $this->user->password)) {
            $this->user->update(
                ['password' => Hash::make($request->password)]
            );
            return true;
        }
        throw new Exception(__('messages.wrong_old_password'), 422);
    }

    /**
     * @throws Exception
     */
    public function resetPassword(ResetPasswordRequest $request): true
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

    public function checkVerificationCode(CheckVerificationCodeRequest $request): array
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


    /**
     * @throws Exception
     * @return true
     */
    public function sendVerificationCode(SendVerificationCodeRequest $request): true
    {
        return DB::transaction(function () use ($request) {
            $authToVerify = [];
            // this part for activation after signup if needed .
            if ($this->user) {
                if ($this->user->is_active) {
                    throw new Exception(__('messages.you_have_already_activate_your_account'), 422);
                }
                $authToVerify['user_id'] = $this->user->id;
                AuthCode::where('user_id', $this->user->id)->delete();

            } else {
                $authToVerify['email'] = $request->email;
                AuthCode::where('email', $request->email)->delete();

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
            ], $authToVerify));
            return true;
        }) === true ;
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {

        $data = $request->validated();
        if ($request->has('password')) {
            if (!Hash::check($request->old_password, $this->user->password)) {
                throw new Exception(__('messages.wrong_old_password'), 422);
            }
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
        // $notifications = $this->notificationService->getAllNotifications();

        return success(
            UserRecourse::make(User::with('images')->where('id', $this->user->id)->first()),
            200
            // [
            //     'notifications' => $notifications ?? null,
            //     //consider that the notifications comes from many types for example messages , new visit , new ad ..etc .
            //     'notifications_types_stats' => $this->notificationService?->getNotificationTypeStatistics(0) ?? [],
            //     'notifications_count' => $this->notificationService?->getAllNotifications(0, true) ?? [],
            // ]
        );
    }

    

    public function register(SignUpRequest $request): array
    {
        $user = $this->userService->createUser($request);
        //todo complet it
        // $this->notificationService->pushAdminsNotifications(Notifications::NEW_REGISTRATION, $user);
        return ['user' => new UserResource($user)];
    }

    public function registerServiceProvider(SignUpServiceProviderRequest $request): array
    {
        $user = $this->userService->createServiceProvider($request);
        //todo complet it
        // $this->notificationService->pushAdminsNotifications(Notifications::NEW_REGISTRATION, $user);
        return ['user' => new UserResource($user)];
    }


    
    public function uploadIdentityImage(Request $request)
    {
        $request->validate([
            'identity_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $user = auth()->user();

        // if ($user->is_active) {
        //     return response()->json(['message' => __('messages.account_already_activated')], 400);
        // }

        $imagePath = $request->file('identity_image')->storePublicly('users/images', 'public');

        $user->identity_image = $imagePath;
        $user->identity_image_verified_at = null; 
        $user->save();

        return response()->json([
            'message' => __('messages.identity_image_uploaded_pending_approval'), 
            'identity_image' => asset('storage/' . $imagePath) 
        ]);
    }


    public function getPendingIdentityImageRequests()
    {
        $users = User::whereNotNull('identity_image')
                    ->whereNull('identity_image_verified_at')
                    ->get();


        return response()->json([
            'users' => $users
        ]);
    }

    public function approveIdentityImage($userId)
    {
        $user = User::findOrFail($userId);

        if (empty($user->identity_image)) {
            return response()->json(['message' => __('messages.no_identity_image_uploaded')], 400);
        }

        $user->identity_image_verified_at = Carbon::now();
        $user->is_active = 1;
        $user->email_verified_at = Carbon::now();
        $user->save();

        return response()->json(['message' => __('messages.identity_image_approved_and_account_activated')]);
    }


}
