<?php

namespace App\Http\Controllers\Api\App;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserRecourse;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\SignUpRequest;
use App\Http\Resources\NotificationRecourse;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Requests\Api\ChangePasswordRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Requests\Api\SendVerificationCodeRequest;
use App\Http\Requests\Api\CheckVerificationCodeRequest;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    /**
     * @OA\Get(
     *     path="/profile",
     *     operationId="app/profile",
     *     summary="get profile data ",
     *     tags={"App", "App - Auth"},
     *      @OA\Parameter(
     *     name="read",
     *     in="query",
     *     description="pass it as 1 if wanted the notification to be read ",
     *     required=false,
     *     @OA\Schema(
     *         type="integer",
     *          enum={1,0}
     *     )
     *      ),
     *    security={{ "bearerAuth": {}, "Accept": "json/application" }},
     *    @OA\Response(response=200, description="Successful operation"),
     * )
     */

    public function profile(Request $request)
    {
        try {
            $profileData = $this->authService->getProfile($request);
            return $profileData;
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode() ?? 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/check/auth",
     *     operationId="app/check/auth",
     *     summary="Check Auth",
     *    tags={"App", "App - Auth"},
     *    security={{ "bearerAuth": {} }},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(property="errors", type="object", example={"password": {"The password field is required."}})
     *         )
     *     )
     * )
     */
    public function authCheck(): JsonResponse
    {
        return success();
    }




    /**
     * @OA\Post(
     *      path="/register",
     *      operationId="app/register",
     *      tags={"App", "App - Auth"},
     *      summary="register a new user",
     *      description="register a new user with the provided information",
     *      @OA\RequestBody(
     *          required=true,
     *          description="User data",
     *         @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              required={"username","first_name","last_name","phone_number", "email", "password"},
     *              @OA\Property(property="username", type="string", example="johndoe"),
     *              @OA\Property(property="first_name", type="string", example="john doe"),
     *              @OA\Property(property="last_name", type="string", example="john doe"),
     *              @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *              @OA\Property(property="password", type="string", example="password"),
     *              @OA\Property(property="phone_number", type="string", example="1234567890"),
     *              @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file to upload"
     *                 ),
     *          ),
     *     ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User created successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User created successfully"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *          )
     *      ),
     * )
     */
    public function register(SignUpRequest $request)
    {
        try {
            $data = $this->authService->register($request);
            return success($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }


    /**
     * @OA\Post(
     *     path="/login",
     *     operationId="app/login",
     *     summary="Login",
     *    tags={"App", "App - Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="App Login",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"username","password"},
     *                 @OA\Property(property="username", type="string", example="johndoe"),
     *                 @OA\Property(property="fcm_token", type="string", example="#####"),
     *                 @OA\Property(property="password", type="string", example="password"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(property="errors", type="object", example={"username": {"The username field is required."}})
     *         )
     *     )
     * )
     */

    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request);
            return success($data);
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     operationId="app/logout",
     *     summary="App Logout",
     *    tags={"App", "App - Auth"},
     *    security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="successfully logged out",
     *     ),
     * )
     */
    function logout()
    {
        try {
            $this->authService->logout();
            return success(['message' => __('messages.successfully_logged_out')]);
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }


    /**
     * @OA\Post(
     *     path="/change-password",
     *     operationId="change-password",
     *     summary="Change password",
     *    tags={"App", "App - Auth"},
     *    security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Change password",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="_method", type="string", example="PUT"),
     *                 @OA\Property(property="old_password", type="string", example="12345678"),
     *                 @OA\Property(property="password", type="string", example="password"),
     *                 @OA\Property(property="password_confirmation", type="string", example="password"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successfully Changed password",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(property="errors", type="object", example={"password": {"The password field is required."}})
     *         )
     *     )
     * )
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->changePassword($request);
            return success(__('messages.password_updated_successfully'));
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/reset-password",
     *     operationId="reset-password",
     *     summary="Reset password",
     *    tags={"App", "App - Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Reset password",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="password", type="string", example="12345678"),
     *                 @OA\Property(property="password_confirmation", type="string", example="12345678"),
     *                 @OA\Property(property="verification_code", type="string", example="1234"),
     *                 @OA\Property(property="email", type="string", example="yosofbayan75@gmail.com"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successfully Changed password",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(property="errors", type="object", example={"password": {"The password field is required."}})
     *         )
     *     )
     * )
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->authService->resetPassword($request);
            return success(__('messages.password_updated_successfully'));
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/check/verification-code",
     *     operationId="check-verification-code",
     *     summary="check verification-code",
     *    tags={"App", "App - Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Check verification-code",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="verification_code", type="string", example="1234"),
     *                 @OA\Property(property="email", type="string", example="yosofbayan75@gmail.com"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="oK",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(property="errors", type="object", example={"password": {"The password field is required."}})
     *         )
     *     )
     * )
     */
    public function checkVerificationCode(CheckVerificationCodeRequest $request): JsonResponse
    {
        try {
            $response = $this->authService->checkVerificationCode($request);
            return success($response);
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/send/verification-code",
     *     operationId="send-verification-code",
     *     summary="send verification code ",
     *    tags={"App", "App - Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="send verification-code",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", example="yosofbayan75@gmail.com"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successfully sent",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(property="errors", type="object", example={"email": {"The email field is required."}})
     *         )
     *     )
     * )
     */
    public function sendVerificationCode(SendVerificationCodeRequest $request): JsonResponse
    {
        try {
            $this->authService->sendVerificationCode($request);
            return success(__('messages.verification_code_sent_successfully'));
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }


    /**
     * @OA\Post(
     *      path="/profile/update",
     *      operationId="updateProfile",
     *      tags={"App", "App - Auth"},
     *      security={{ "bearerAuth": {} }},
     *      summary="Update Profile data",
     *      description="Update user profile with the provided information",
     *      @OA\RequestBody(
     *          required=true,
     *          description="User data",
     *             *         @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              @OA\Property(property="username", type="string", example="johndoe"),
     *              @OA\Property(property="first_name", type="string", example="john doe"),
     *              @OA\Property(property="last_name", type="string", example="john doe"),
     *              @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *              @OA\Property(property="password", type="string", example="password"),
     *              @OA\Property(property="phone_number", type="string", example="1234567890"),
     *              @OA\Property(property="_method", type="string", example="PUT"),
     *              @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file to upload"
     *                 ),
     *          ),
     *   ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User udpated successfully"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *          )
     *      ),
     * )
     */

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->updateProfile($request);
            return $data;
        } catch (\Exception $e) {
            return error($e->getMessage(), null, $e->getCode());
        }
    }
}
