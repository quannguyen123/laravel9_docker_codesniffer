<?php

use App\Http\Controllers\Admins\ManagerAccountController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\OccupationController;
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
        Route::get('index', [OccupationController::class, 'index'])->name('admin.user.index');
        Route::post('store', [OccupationController::class, 'store'])->name('admin.user.store');
        Route::get('detail/{occupation}', [OccupationController::class, 'detail'])->name('admin.user.index');
        Route::post('update/{occupation}', [OccupationController::class, 'update'])->name('admin.user.update');
        Route::delete('destroy/{occupation}', [OccupationController::class, 'destroy'])->name('admin.user.destroy');
        Route::get('/change-status/{occupation}/{status}',  [OccupationController::class, 'changeStatus'])->whereIn('status', ['lock', 'active']);
    });

    Route::prefix('welfare')->group(function() {
        Route::get('index', [WelfareController::class, 'index'])->name('admin.user.index');
        Route::post('store', [WelfareController::class, 'store'])->name('admin.user.store');
        Route::get('detail/{occupation}', [WelfareController::class, 'detail'])->name('admin.user.index');
        Route::post('update/{occupation}', [WelfareController::class, 'update'])->name('admin.user.update');
        Route::delete('destroy/{occupation}', [WelfareController::class, 'destroy'])->name('admin.user.destroy');
        Route::get('/change-status/{occupation}/{status}',  [WelfareController::class, 'changeStatus'])->whereIn('status', ['lock', 'active']);
    });
    
});



