<?php

namespace App\Services;

use App\Constants\Constants;
use App\Models\User;
use App\Models\UserFcmToken;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
            $this->handelFcmToken($user, $request->fcm_token);
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

    public function handelFcmToken($user, $fcmToken)
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
}
