<?php

use App\Http\Controllers\Api\Partner\AuthController;
use App\Http\Controllers\Api\Partner\CompanyController;
use App\Http\Controllers\Api\Partner\PartnerManagementController;
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

});
