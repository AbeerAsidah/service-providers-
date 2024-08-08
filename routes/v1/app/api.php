<?php

use App\Http\Controllers\Api\General\Branch\BranchController;
use App\Http\Controllers\Api\General\Info\HomeController;
use App\Http\Controllers\Api\General\Product\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\General\Auth\AuthController;
use App\Http\Controllers\Api\General\Info\InfoController;
use App\Http\Controllers\Api\General\Offer\OfferController;
use App\Http\Controllers\Api\General\Section\SectionController;
use App\Http\Controllers\Api\App\Auth\AuthController as AppAuthController;
use App\Http\Controllers\Api\App\ContactMessage\ContactMessageController;
use App\Http\Controllers\Api\General\PurchaseCode\PurchaseCodeController;

/** @Auth */
Route::post('login', [AuthController::class, 'login'])->name('user.login');//
Route::post('register', [AppAuthController::class, 'register'])->name('user.register');//
Route::post('reset-password', [AuthController::class, 'resetPassword']);//
Route::post('send/verification-code', [AuthController::class, 'sendVerificationCode']);//
Route::post('check/verification-code', [AuthController::class, 'checkVerificationCode']);//


Route::prefix('sections')->group(function () {
    /**
     * parent_id options :
     * empty for all section at the top layer .
     * section id to get its sub sections .
     */
    Route::get('/{parentSection?}', [SectionController::class, 'index']);//
    Route::get('/detail/{section}', [SectionController::class, 'show']);//
    Route::prefix('/{parentSection}/products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);//
        Route::get('/{product}', [ProductController::class, 'show']);//
    });
});
Route::group(['middleware' => ['auth:api', 'last.active']], function () {
    /** @Auth */
    Route::post('logout', [AuthController::class, 'logout']);//
    Route::get('/check/auth', [AuthController::class, 'authCheck']);//
    Route::get('profile', [AuthController::class, 'profile']);//
    Route::put('change-password', [AuthController::class, 'changePassword']);//
    Route::put('profile/update', [AuthController::class, 'updateProfile']);//
});

/**@Guest */
Route::post('/contact-messages', [ContactMessageController::class, 'store']);//
Route::get('/offers', [OfferController::class, 'index']);//

