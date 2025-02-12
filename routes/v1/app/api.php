<?php
use App\Constants\Constants;
use App\Http\Controllers\Api\General\Info\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\AuthController as AppAuthController;

/** @Auth */
Route::post('login', [AuthController::class, 'login'])->name('user.login');//
Route::post('reset-password', [AuthController::class, 'resetPassword']);//
Route::post('send/verification-code', [AuthController::class, 'sendVerificationCode']);//
Route::post('check/verification-code', [AuthController::class, 'checkVerificationCode']);//
Route::post('register', [AppAuthController::class, 'register'])->name('user.register');//
Route::post('registerServiceProvider', [AppAuthController::class, 'registerServiceProvider'])->name('user.register');//


Route::group(['middleware' => ['auth:api', 'last.active', 'ability:' . Constants::USER_ROLE]], function () {
 
});

Route::group(['middleware' => ['auth:api', 'last.active', 'ability:' . Constants::SERVICE_PROVIDER_ROLE]], function () {
 
});

Route::middleware(['auth:api', 'role:' . Constants::SERVICE_PROVIDER_ROLE . '|' . Constants::USER_ROLE])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);//
    Route::get('/check/auth', [AuthController::class, 'authCheck']);//
    Route::get('profile', [AuthController::class, 'profile']);//
    Route::put('change-password', [AuthController::class, 'changePassword']);//
    Route::put('profile/update', [AuthController::class, 'updateProfile']);//
    });

