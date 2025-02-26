<?php
use App\Constants\Constants;
use App\Http\Controllers\Api\General\Info\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Service\ServiceController;
use App\Http\Controllers\Api\Cart\CartItemController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Review\ReviewController;
use App\Http\Controllers\Api\Auth\AuthController as AppAuthController;

/** @Auth */
Route::post('login', [AuthController::class, 'login'])->name('user.login');//
Route::post('reset-password', [AuthController::class, 'resetPassword']);//
Route::post('send/verification-code', [AuthController::class, 'sendVerificationCode']);//
Route::post('check/verification-code', [AuthController::class, 'checkVerificationCode']);//
Route::post('register', [AppAuthController::class, 'register'])->name('user.register');//
Route::post('registerServiceProvider', [AppAuthController::class, 'registerServiceProvider'])->name('user.register');//


Route::group(['middleware' => ['auth:api', 'last.active', 'ability:' . Constants::USER_ROLE]], function () {
   
    Route::prefix('cart')->group(function () {
        Route::post('/', [CartItemController::class, 'addToCart']);
        Route::put('/update', [CartItemController::class, 'updateCart']);
        Route::get('/', [CartItemController::class, 'viewCart']); 
        Route::delete('/remove/{serviceId}', [CartItemController::class, 'removeFromCart']);
    });


    Route::prefix('/orders')->group(function () {
        Route::post('/', [OrderController::class, 'placeOrder'])->name('order.place');
        Route::get('/myOrders', [OrderController::class, 'myOrders'])->name('order.myOrders');
    });

    Route::prefix('reviews')->group(function () {
        Route::post('/{service}', [ReviewController::class, 'store']); 
        Route::put('/{review}', [ReviewController::class, 'update']); 
        Route::delete('/{review}', [ReviewController::class, 'destroy']); 
    });
});

Route::group(['middleware' => ['auth:api', 'last.active', 'ability:' . Constants::SERVICE_PROVIDER_ROLE]], function () {
    Route::post('upload-identity-image', [AppAuthController::class, 'uploadIdentityImage']);

    Route::prefix('services')->group(function () {
        Route::post('/', [ServiceController::class, 'store']); 
        Route::put('/{service}', [ServiceController::class, 'update']); 
        Route::delete('/{id}', [ServiceController::class, 'destroy']); 
        Route::patch('/{id}/restore', [ServiceController::class, 'restore']);
        Route::put('/{id}/changeStatus', [ServiceController::class, 'changeStatus']);
    });


    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'getProviderOrders'])->name('orders.index');

    });

});

Route::middleware(['auth:api', 'role:' . Constants::SERVICE_PROVIDER_ROLE . '|' . Constants::USER_ROLE])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);//
    Route::get('/check/auth', [AuthController::class, 'authCheck']);//
    Route::get('profile', [AuthController::class, 'profile']);//
    Route::put('change-password', [AuthController::class, 'changePassword']);//
    Route::put('profile/update', [AuthController::class, 'updateProfile']);//

    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index']); 
        Route::get('/{id}', [ServiceController::class, 'show']); 
        Route::post('/search', [ServiceController::class, 'searchServices']); 

    });
    Route::prefix('reviews')->group(function () {
        Route::get('/{service}', [ReviewController::class, 'getReviewsByService']); 
        Route::get('/{service}/average', [ReviewController::class, 'getAverageRating']); 
    });

    Route::prefix('orders')->group(function () {
        Route::get('/show/{orderItemId}', [OrderController::class, 'getOrder'])->name('orders.show');
        Route::put('/{id}/update-status', [OrderController::class, 'updateStatus'])->name('updateStatus.orders');


    });

    
    });

