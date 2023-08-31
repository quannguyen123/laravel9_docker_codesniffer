<?php

use App\Http\Controllers\Api\Partner\AuthController as PartnerAuthController;
use App\Http\Controllers\Api\Partner\TagController;
use App\Http\Controllers\Api\Public\JobTitleController;
use App\Http\Controllers\Api\Public\OccupationController;
use App\Http\Controllers\Api\Public\ProvinceController;
use App\Http\Controllers\Api\User\AlertJobController;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\JobController;
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
Route::group(['prefix' => 'user'],function(){
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])->name('login');
    
    Route::post('reset-password', [PartnerAuthController::class, 'resetPassword']);
    Route::post('create-new-password', [PartnerAuthController::class, 'createNewPassword']);
});

Route::group(['prefix' => 'user', 'middleware' => ['auth:api-user', 'role:user']],function(){
    Route::post('test', function() {
        return 'trang chu user';
    });

    Route::post('change-password', [PartnerAuthController::class, 'changePassword']);

    Route::prefix('alert-job')->group(function() {
        Route::get('index', [AlertJobController::class, 'index']);
        Route::post('store', [AlertJobController::class, 'store']);
        Route::get('detail/{id}', [AlertJobController::class, 'detail']);
        Route::post('update/{id}', [AlertJobController::class, 'update']);
        Route::delete('destroy/{id}', [AlertJobController::class, 'destroy']);
    });

    Route::post('logout', [AuthController::class, 'logout']);
});

Route::prefix('job')->group(function() {
    Route::get('index', [JobController::class, 'index']);
    Route::get('detail/{id}', [JobController::class, 'detail']);
});

Route::group(['prefix' => 'job', 'middleware' => ['auth:api-user', 'role:user'] ],function(){
    Route::post('job-apply/{id}', [JobController::class, 'applyJob']);
    Route::get('job-favourite/{id}', [JobController::class, 'jobFavourite']);

});

Route::prefix('public')->group(function() {
    Route::prefix('occupation')->group(function() {
        Route::get('index', [OccupationController::class, 'index']);
    });
    
    Route::prefix('job-title')->group(function() {
        Route::get('index', [JobTitleController::class, 'index']);
    });

    Route::prefix('tag')->group(function() {
        Route::get('index', [TagController::class, 'index']);
        Route::post('suggest', [TagController::class, 'suggest']);
    });

    Route::get('list-province', [ProvinceController::class, 'listProvince']);
    Route::get('list-district-by-province', [ProvinceController::class, 'listDistrictByProvince']);
});




