<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\CreateUserRequest;
use App\Http\Requests\Api\User\GetUsersRequest;
use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Http\Requests\Api\User\UpdateEmailRequest;
use App\Services\User\UserService;
use App\Services\Auth\AuthService;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService,AuthService $authService)
    {
        $this->userService = $userService;
        $this->authService = $authService;

    }

    public function index(Request $request)
    {
        try {
            $roles = [];
            if ($request->role) {
                $roles[] = $request->role;
            }
            return success($this->userService->getAllUsers($roles));
        } catch (\Exception $e) {
            return error($e->getMessage(), ['error' => $e->getMessage()], $e->getCode());
        }
    }

    
    public function show($id)
    {
        try {
            return success($this->userService->getUserById($id));
        } catch (\Exception $e) {
            return error($e->getMessage(), ['error' => $e->getMessage()], $e->getCode());
        }
    }

    
    public function store(CreateUserRequest $request)
    {
        try {
            $user = $this->userService->createUser($request);
            return success($user, 201);
        } catch (\Exception $e) {   
            return error($e->getMessage(), ['error' => $e->getMessage()], $e->getCode());
        }
    }

   
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = $this->userService->updateUser($request, $id);
            return success($user);
        } catch (\Exception $e) {
            return error($e->getMessage(), ['error' => $e->getMessage()], $e->getCode());
        }
    }

   
    public function destroy($id)
    {
        try {
            $this->userService->deleteUser($id);
            return success(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return error($e->getMessage(), ['error' => $e->getMessage()], $e->getCode());
        }
    }




  




     public function updateProfile(UpdateProfileRequest $request)
     {
         try {
             $user = $this->userService->updateProfile($request);
             return success($user);
         } catch (\Exception $e) {
             return error($e->getMessage(), ['error' => $e->getMessage()], $e->getCode());
         }
     }


       
    public function updateEmail(UpdateEmailRequest $request)
    {
        try {
            $user =$this->authService->updateEmail($request);
            return success($user);
        } catch (\Exception $e) {
            return error($e->getMessage(), ['error' => $e->getMessage()], $e->getCode());
        }
    }

}
