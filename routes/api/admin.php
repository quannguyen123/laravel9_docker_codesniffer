<?php

use App\Http\Controllers\Admins\ManagerAccountController;
use App\Http\Controllers\Api\Admin\AuthController;
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
    
});



