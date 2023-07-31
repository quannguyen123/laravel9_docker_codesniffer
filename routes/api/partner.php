<?php

use App\Http\Controllers\Api\Partner\AuthController;
use App\Http\Controllers\Api\Partner\CompanyController;
use App\Http\Controllers\Api\Partner\CompanyLocationController;
use App\Http\Controllers\Api\Partner\JobController;
use App\Http\Controllers\Api\Partner\JobTitleController;
use App\Http\Controllers\Api\Partner\OccupationController;
use App\Http\Controllers\Api\Partner\OrderController;
use App\Http\Controllers\Api\Partner\PartnerManagementController;
use App\Http\Controllers\Api\Partner\PaymentController;
use App\Http\Controllers\Api\Partner\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('partner/register', [AuthController::class, 'register']);
Route::post('partner/login', [AuthController::class, 'login']);

Route::group( ['prefix' => 'partner', 'middleware' => ['auth:api-user', 'role:partner'] ],function(){
    Route::post('test', function() {
        return 'trang chu hr';
    });

    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('partner-management')->group(function() {
        Route::get('index', [PartnerManagementController::class, 'index']);
        Route::get('detail/{id}', [PartnerManagementController::class, 'detail']);
        Route::post('store', [PartnerManagementController::class, 'store']);
        Route::put('update/{id}', [PartnerManagementController::class, 'update']);
        Route::delete('destroy/{id}', [PartnerManagementController::class, 'destroy']);
    });

    Route::prefix('company')->group(function() {
        Route::get('detail', [CompanyController::class, 'detail']);
        // Route::post('store', [CompanyController::class, 'store']);
        Route::post('update', [CompanyController::class, 'update']);
    });

    Route::prefix('company-location')->group(function() {
        Route::get('index', [CompanyLocationController::class, 'index']);
        Route::post('store', [CompanyLocationController::class, 'store']);
        Route::get('detail/{id}', [CompanyLocationController::class, 'detail']);
        Route::post('update/{id}', [CompanyLocationController::class, 'update']);
        Route::delete('destroy/{id}', [CompanyLocationController::class, 'destroy']);
    });

    Route::prefix('service')->group(function() {
        Route::get('list', [ServiceController::class, 'list']);
        Route::get('detail/{service}', [ServiceController::class, 'detail']);

        Route::post('add-to-cart', [ServiceController::class, 'addToCart']);
        Route::post('edit-cart-item', [ServiceController::class, 'editCartItem']);
        Route::get('cart-info', [ServiceController::class, 'cartInfo']);
        Route::post('delete-cart-item', [ServiceController::class, 'deleteCartItem']);

        Route::get('delete-cart', [ServiceController::class, 'deleteCart']);
    });

    Route::prefix('order')->group(function() {
        Route::get('index', [OrderController::class, 'index']);
        Route::get('store', [OrderController::class, 'store']);
        Route::get('{id}/order-info', [OrderController::class, 'orderInfo']);
    });

    Route::prefix('payment')->group(function() {
        Route::get('/{order}/vnpay', [PaymentController::class, 'pay']);
        Route::get('payment-return', [PaymentController::class, 'paymentReturn'])->name('payment-return');
        Route::get('callback', [PaymentController::class, 'callback'])->name('payment-callback');
    });

    Route::prefix('job')->group(function() {
        Route::get('index', [JobController::class, 'index']);
        Route::post('store', [JobController::class, 'store']);
        Route::get('detail/{id}', [JobController::class, 'detail']);
        Route::post('update/{id}', [JobController::class, 'update']);
        Route::get('destroy/{id}', [JobController::class, 'destroy']);
        Route::get('change-status/{id}',  [JobController::class, 'changeStatus']);
        
    });
});
