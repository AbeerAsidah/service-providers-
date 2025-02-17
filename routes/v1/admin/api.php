<?php

use App\Constants\Constants;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

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
    Route::post('/', [ServiceController::class, 'store']); // إنشاء خدمة جديدة
    Route::get('/{id}', [ServiceController::class, 'show']); // عرض خدمة معينة
    Route::put('/{service}', [ServiceController::class, 'update']); // تحديث الخدمة
    Route::delete('/{id}', [ServiceController::class, 'destroy']); // حذف الخدمة (ناعم أو نهائي)
    Route::post('/restore/{id}', [ServiceController::class, 'restore']); // استعادة خدمة محذوفة
    Route::get('/all', [ServiceController::class, 'getAllServices']); // جلب كل الخدمات
    Route::get('/categories', [ServiceController::class, 'getCategories']); // جلب التصنيفات
    Route::get('/{id}/manufacture-years', [ServiceController::class, 'getManufactureYears']); // جلب سنوات التصنيع
});


Route::prefix('users')->group(function () {

    // Route::post('/upload-identity-image', [AuthController::class, 'uploadIdentityImage']);
    Route::get('/pending-identity-image-requests', [AuthController::class, 'getPendingIdentityImageRequests']);
    Route::post('/approve-identity-image/{userId}', [AuthController::class, 'approveIdentityImage']);
    
});


});



