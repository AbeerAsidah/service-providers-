<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\Api\App\AuthController;
use App\Http\Controllers\Api\General\SectionController;
use App\Http\Controllers\ContactMessageController;

/** @Auth */
Route::post('login', [AuthController::class, 'login'])->name('user.login');
Route::post('register', [AuthController::class, 'register'])->name('user.register');
Route::post('register/validate', [AuthController::class, 'registerValidation']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::post('send/verification-code', [AuthController::class, 'sendVerificationCode']);
Route::post('check/verification-code', [AuthController::class, 'checkVerificationCode']);

/**@Auth */
Route::prefix('contact-messages')->group(function () {
    Route::get('/', [ContactMessageController::class, 'index']);
    Route::patch('{trashed_contact_message}/restore', [ContactMessageController::class, 'restore']);
    Route::delete('{contact_message}', [ContactMessageController::class, 'delete']);
    Route::delete('{trashed_contact_message}/force', [ContactMessageController::class, 'forceDelete']);
    //todo ? mass delete , mass force delete
});

Route::prefix('infos')->group(function () {
    Route::get('/', [InfoController::class, 'index']);
    Route::put('/', [InfoController::class, 'update']);
});
//todo add admin auth check
Route::group(['middleware' => ['auth:api', 'last.active']], function () {

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
});
