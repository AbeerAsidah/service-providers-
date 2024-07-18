<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\Api\App\AuthController;
use App\Http\Controllers\Api\General\SectionController;
use App\Http\Controllers\ContactMessageController;

/** @Auth */
Route::post('login', [AuthController::class, 'login'])->name('user.login');
Route::post('register', [AuthController::class, 'register'])->name('user.register');
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::post('send/verification-code', [AuthController::class, 'sendVerificationCode']);
Route::post('check/verification-code', [AuthController::class, 'checkVerificationCode']);


Route::prefix('sections')->group(function () {
    /**
     * parent_id options :
     * empty for all section at the top layer
     * section id to get its sub sections .
     */
    Route::get('/{parentSection?}', [SectionController::class, 'index']);
    Route::post('/{parentSection?}/{type?}', [SectionController::class, 'store']);
    Route::put('/{section}', [SectionController::class, 'update']);
    Route::delete('/{section}', [SectionController::class, 'destroy']);
});
Route::group(['middleware' => ['auth:api', 'last.active']], function () {
    /** @Auth */
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/check/auth', [AuthController::class, 'authCheck']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::put('change-password', [AuthController::class, 'changePassword']);
    Route::put('profile/update', [AuthController::class, 'updateProfile']);
});

/**@Guest */
//todo add the admin index and delete for messages .
Route::post('contact-messages', [ContactMessageController::class, 'store']);

//todo : for landing page add new api called home and return all home data with it .
Route::get('infos', [InfoController::class, 'index']);
