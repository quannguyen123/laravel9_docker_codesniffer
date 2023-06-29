<?php

use App\Http\Controllers\Api\User\AuthController;
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

Route::post('user/register', [AuthController::class, 'register']);
Route::post('user/login', [AuthController::class, 'login'])->name('login');

Route::group(['prefix' => 'user', 'middleware' => ['auth:api-user', 'role:user']],function(){
    Route::post('test', function() {
        return 'trang chu user';
    });

    Route::post('logout', [AuthController::class, 'logout']);
});


