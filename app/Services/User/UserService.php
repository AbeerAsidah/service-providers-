<?php

namespace App\Services\User;

use App\Models\User;
use App\Constants\Constants;
use App\Models\UserFcmToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\User\CreateUserRequest;
use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Http\Requests\Api\User\UpdateProfileRequest;
use App\Http\Requests\Api\User\UpdateEmailRequest;
use DB;


class UserService
{

    public function createUser(FormRequest $request): User
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $user->assignRole(Constants::USER_ROLE);
        $this->handleUserImage($user, $request);
        $user['token'] = $this->generateUserToken($user);
        if ($request->fcm_token) {
            $this->handleFcmToken($user, $request->fcm_token);
        }
        return $user;
    }

    public function createServiceProvider(FormRequest $request): User
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $user->assignRole(Constants::SERVICE_PROVIDER_ROLE);
        $user->wallet()->create([
            'balance' => 0,
        ]);
        $this->handleUserImage($user, $request);
        $user['token'] = $this->generateUserToken($user);
        if ($request->fcm_token) {
            $this->handleFcmToken($user, $request->fcm_token);
        }
        return $user;
    }

    public function handleUserImage(?User $user, FormRequest $request): void
    {
        if ($request->hasFile('image')) {
            $user->images()->updateOrCreate(
                ['user_id' => $user->id], // Search criteria
                ['image' => $request->file('image')->storePublicly('users/images', 'public')] // Values to update or create
            );
        } elseif ($request->has('image') && $request->image === null) {
            $image = $user->images()->first();
            if ($image && Storage::exists('public/' . $image->image)) {
                Storage::delete('public/' . $image->image);
            }
            $user->images()->delete();
        }
    }
    protected function generateUserToken(User $user): string
    {
        return $user->createToken('auth')->plainTextToken;
    }

    public function handleFcmToken($user, $fcmToken)
    {
        //check if the fcm token stored in guest mode to link it with the user .
        $existingFcmToken = UserFcmToken::where('fcm_token', $fcmToken)->first();
        if ($existingFcmToken) {
            return  $existingFcmToken->update([
                'token_id' => $user->tokens()->orderBy('id', 'DESC')->first()->id,
                'user_id' => $user->id,
            ]);
        }
        return  $user->fcmTokens()->firstOrCreate(
            [
                'fcm_token' => $fcmToken,
                'token_id' => $user->tokens()->orderBy('id', 'DESC')->first()->id
            ]
        );
    }









    public function getAllUsers(array $roles)
    {
        $currentUserId = auth()->id(); 
    
        $users = User::with('roles')
                     ->where('id', '!=', $currentUserId); 
    
        if (count($roles)) {
            $users->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('name', $roles);
            });
        }
    
        return ['data' => $users->get()];
    }

    public function getUserById($id)
    {
        return ['data' => User::with('roles')->findOrFail($id)];
    }

    public function getUserByColumns(array $data): User|null
    {
        return User::with('roles')->where($data)->first();
    }

    public function create(CreateUserRequest $request)
    {
        $data = $request->validated();
        // Encrypt the password
        $data['password'] = Hash::make($data['password']);
        if ($request->hasFile('identity_image')) {
            $data['identity_image'] = $request->file('identity_image')->storePublicly('users', 'public');
        }
        $user = User::create($data);

        // Assign role
        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        } else {
            $user->assignRole(Constants::USER_ROLE);
        }

        return ['data' => $user];
    }

    public function updateUser(UpdateUserRequest $request, $id)
    {
        DB::beginTransaction();
        $data = $request->validated();
        if ($request->hasFile('identity_image')) {
            $data['identity_image'] = $request->file('identity_image')->storePublicly('users', 'public');
        }
        $user = User::findOrFail($id);
        $user->update($data);
        DB::commit();
        DB::afterCommit(function () use ($user) {
            $oldImage = $user->getOriginal('identity_image');
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
        });
        return ['data' => $data];
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole(Constants::ADMIN_ROLE)) {
            throw new \Exception(__("messages.can not delete this user "));
        }
        return ['data' => $user->delete()];
    }

   

   
}
