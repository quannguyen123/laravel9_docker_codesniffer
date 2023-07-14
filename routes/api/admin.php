<?php

use App\Http\Controllers\Admins\ManagerAccountController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\OccupationController;
use App\Http\Controllers\Api\Admin\TagController;
use App\Http\Controllers\Api\Admin\TitleJobController;
use App\Http\Controllers\Api\Admin\WelfareController;
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

Route::post('admin/register', [AuthController::class, 'register']);
Route::post('admin/login', [AuthController::class, 'login']);

Route::group(['prefix' => 'admin', 'middleware' => ['auth:api-admin', 'role:admin'] ],function(){
    Route::post('test', function() {
        return 'trang chu admin';
    });

    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('occupation')->group(function() {
        Route::get('index', [OccupationController::class, 'index']);
        Route::post('store', [OccupationController::class, 'store']);
        Route::get('detail/{occupation}', [OccupationController::class, 'detail']);
        Route::post('update/{occupation}', [OccupationController::class, 'update']);
        Route::delete('destroy/{occupation}', [OccupationController::class, 'destroy']);
        Route::get('/change-status/{occupation}/{status}',  [OccupationController::class, 'changeStatus'])->whereIn('status', ['lock', 'active']);
    });

    Route::prefix('welfare')->group(function() {
        Route::get('index', [WelfareController::class, 'index']);
        Route::post('store', [WelfareController::class, 'store']);
        Route::get('detail/{welfare}', [WelfareController::class, 'detail']);
        Route::post('update/{welfare}', [WelfareController::class, 'update']);
        Route::delete('destroy/{welfare}', [WelfareController::class, 'destroy']);
        Route::get('/change-status/{welfare}/{status}',  [WelfareController::class, 'changeStatus'])->whereIn('status', ['lock', 'active']);
    });
    
    Route::prefix('tag')->group(function() {
        Route::get('index', [TagController::class, 'index']);
        Route::post('store', [TagController::class, 'store']);
        Route::get('detail/{tag}', [TagController::class, 'detail']);
        Route::post('update/{tag}', [TagController::class, 'update']);
        Route::delete('destroy/{tag}', [TagController::class, 'destroy']);
        Route::get('/change-status/{tag}/{status}',  [TagController::class, 'changeStatus'])->whereIn('status', ['lock', 'active']);
    });

    Route::prefix('job-title')->group(function() {
        Route::get('index', [TitleJobController::class, 'index']);
        Route::post('store', [TitleJobController::class, 'store']);
        Route::get('detail/{job_title}', [TitleJobController::class, 'detail']);
        Route::post('update/{job_title}', [TitleJobController::class, 'update']);
        Route::delete('destroy/{job_title}', [TitleJobController::class, 'destroy']);
        Route::get('/change-status/{job_title}/{status}',  [TitleJobController::class, 'changeStatus'])->whereIn('status', ['lock', 'active']);
    });
});



