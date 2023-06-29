<?php

use App\Http\Controllers\Admins\ManagerAccountController;
use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Partner\AuthController;
use App\Http\Controllers\Api\User\AuthController as UserAuthController;
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

// Route::post('partner/register', [AuthController::class, 'register']);
// Route::post('partner/login', [AuthController::class, 'login'])->name('login');

// Route::group( ['prefix' => 'partner', 'middleware' => ['auth:api-partner'] ],function(){
//     Route::post('test', function() {
//         return 'trang chu hr';
//     });
// });


// Route::group( ['prefix' => 'partner2', 'middleware' => ['auth:api-user'] ],function(){
//     Route::post('test', function() {
//         return 'trang chu hr2';
//     });
// });


// Route::post('admin/register', [AdminAuthController::class, 'register']);
// Route::post('admin/login', [AdminAuthController::class, 'login'])->name('login');

// // Route::group(['prefix' => 'admin', 'middleware' => ['auth:api-user'] ],function(){
// Route::group(['prefix' => 'admin', 'middleware' => ['auth:api-admin', 'role:admin'] ],function(){
//     Route::post('test', function() {
//         return 'trang chu admin';
//     });
// });



// Route::post('user/register', [UserAuthController::class, 'register']);
// Route::post('user/login', [UserAuthController::class, 'login'])->name('login');

// Route::group(['prefix' => 'user', 'middleware' => ['auth:api-user']],function(){
//     Route::post('test', function() {
//         return 'trang chu user';
//     });
// });