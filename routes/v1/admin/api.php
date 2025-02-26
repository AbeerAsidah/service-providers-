<?php

use App\Constants\Constants;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Service\ServiceController;
use App\Http\Controllers\Api\Order\OrderController;



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



Route::prefix('services')->group(function () {
    Route::get('/', [ServiceController::class, 'index']); 
    Route::post('/', [ServiceController::class, 'store']); 
    Route::get('/{id}', [ServiceController::class, 'show']); 
    Route::put('/{service}', [ServiceController::class, 'update']); 
    Route::delete('/{id}', [ServiceController::class, 'destroy']); 
    Route::patch('/{id}/restore', [ServiceController::class, 'restore']);
    Route::put('/{id}/changeStatus', [ServiceController::class, 'changeStatus']);
    Route::post('/search', [ServiceController::class, 'searchServices']); 



});


Route::prefix('users')->group(function () {

    // Route::post('/upload-identity-image', [AuthController::class, 'uploadIdentityImage']);
    Route::get('/pending-identity-image-requests', [AuthController::class, 'getPendingIdentityImageRequests']);
    Route::post('/approve-identity-image/{userId}', [AuthController::class, 'approveIdentityImage']);
    
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/{category}', [CategoryController::class, 'update']);
    Route::delete('/{id}/{force?}', [CategoryController::class, 'delete']);
    Route::patch('/{id}/restore', [CategoryController::class, 'restore']);
    Route::get('/{categoryId}/services', [CategoryController::class, 'getServicesByCategory'])->name('getServicesByCategory');

});

Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/show/{orderId}', [OrderController::class, 'getOrder']);
    Route::put('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('updateStatus.orders');
    Route::delete('/{id}/{force?}', [OrderController::class, 'deleteOrder']);

});
Route::prefix('reviews')->group(function () {
    Route::get('/{service}', [ReviewController::class, 'getReviewsByService']); 
    Route::get('/{service}/average', [ReviewController::class, 'getAverageRating']); 
});


});



