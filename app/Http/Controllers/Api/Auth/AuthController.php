<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\UpdateProfileRequest;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\Auth\SendVerificationCodeRequest;
use App\Http\Requests\Api\Auth\CheckVerificationCodeRequest;
use App\Http\Requests\Api\Auth\SignUpRequest;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    
    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request,str_contains($request->url(), 'admin'));
            return success($data);
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }


    public function profile(Request $request)
    {
        try {
            return $this->authService->getProfile($request);
        } catch (\Exception $e) {
            return error($e->getMessage(), [$e->getMessage()], $e->getCode());
        }
    }

    public function authCheck(): JsonResponse
    {
        return success();
    }

    function logout()
    {
        try {
            $this->authService->logout();
            return success(['message' => __('messages.successfully_logged_out')]);
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }


    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->changePassword($request);
            return success(__('messages.password_updated_successfully'));
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->resetPassword($request);
            return success(__('messages.password_updated_successfully'));
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }

    public function checkVerificationCode(CheckVerificationCodeRequest $request): JsonResponse
    {
        try {
            $response = $this->authService->checkVerificationCode($request);
            return success($response);
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }

    public function sendVerificationCode(SendVerificationCodeRequest $request): JsonResponse
    {
        try {
            $this->authService->sendVerificationCode($request);
            return success(__('messages.verification_code_sent_successfully'));
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->updateProfile($request);
            return $data;
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }

     
    public function register(SignUpRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->register($request);
            return success($data);
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }

    public function registerServiceProvider(SignUpRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->registerServiceProvider($request);
            return success($data);
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }

    


    public function uploadIdentityImage(Request $request)
    {
        try {
            $user = auth()->user();
            $response = $this->authService->uploadIdentityImage($request, $user);
            return success($response); 
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());  
        }
    }

    public function getPendingIdentityImageRequests()
    {
        try {
            $users = $this->authService->getPendingIdentityImageRequests();
            return success(['users' => $users]);  
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());  
        }
    }

    public function approveIdentityImage($userId)
    {
        try {
            $response = $this->authService->approveIdentityImage($userId);
            return success($response);  
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());  
        }
    }
}
