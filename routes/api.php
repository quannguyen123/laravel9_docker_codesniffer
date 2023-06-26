<?php

use App\Http\Controllers\Admins\ManagerAccountController;
use App\Http\Controllers\Admins\RegisterController;
use Illuminate\Http\Request;
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
Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('manager-account', [ManagerAccountController::class, 'index']);

    // Route::prefix('management-user')->group(function() {
    //     Route::get('index', [UserController::class, 'index'])->name('');
        
    // });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return 'màn hình user';
});
