<?php

use App\Constants\Constants;
use App\Http\Controllers\Api\General\Branch\BranchController;
use App\Http\Controllers\Api\Admin\Branch\BranchController as AdminBranchController;
use App\Http\Controllers\Api\General\Product\ProductController;
use App\Http\Controllers\Api\Admin\Product\ProductController as AdminProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\General\Info\InfoController;
use App\Http\Controllers\Api\Admin\Offer\OfferController as AdminOfferController;
use App\Http\Controllers\Api\General\Offer\OfferController;
use App\Http\Controllers\Api\General\Auth\AuthController;
use App\Http\Controllers\Api\General\Section\SectionController;
use App\Http\Controllers\Api\Admin\Info\InfoController as AdminInfoController;
use App\Http\Controllers\Api\Admin\ContactMessage\ContactMessageController;
use App\Http\Controllers\Api\Admin\Section\SectionController as AdminSectionController;

/** @Auth */
Route::post('login', [AuthController::class, 'login'])->name('admin.login');//
Route::post('reset-password', [AuthController::class, 'resetPassword']);//
Route::post('send/verification-code', [AuthController::class, 'sendVerificationCode']);//
Route::post('check/verification-code', [AuthController::class, 'checkVerificationCode']);//

Route::group(['middleware' => ['auth:api', 'last.active', 'ability:' . Constants::ADMIN_ROLE]], function () {

    /** @Auth */
    Route::post('logout', [AuthController::class, 'logout']);//
    Route::get('/check/auth', [AuthController::class, 'authCheck']);//
    Route::get('profile', [AuthController::class, 'profile']);//
    Route::put('change-password', [AuthController::class, 'changePassword']);//
    Route::put('profile/update', [AuthController::class, 'updateProfile']);//


    Route::prefix('contact-messages')->group(function () {
        Route::get('/', [ContactMessageController::class, 'index']);//
        Route::delete('{contactMessage}/{force?}', [ContactMessageController::class, 'delete']);//
        Route::patch('{contactMessage}/restore', [ContactMessageController::class, 'restore']);//
    });


});
Route::prefix('sections')->group(function () {
    /**
     * parent_id options :
     * empty for all section at the top layer
     * section id to get its sub sections .
     */
    Route::get('/{parentSection?}', [AdminSectionController::class, 'index']);//
    Route::get('/detail/{section}', [SectionController::class, 'show']);//
    Route::post('/{parentSection?}/{type?}', [AdminSectionController::class, 'store']);//
    Route::put('/{section}', [AdminSectionController::class, 'update']);//
    Route::delete('/{id}/{force?}', [AdminSectionController::class, 'delete']);//
    Route::get('/{id}/restore', [AdminSectionController::class, 'restore']);

});
Route::prefix('infos')->group(function () {
    Route::get('/', [InfoController::class, 'index']);//
    Route::post('/update', [AdminInfoController::class, 'update']);//
});

