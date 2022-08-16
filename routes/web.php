<?php

use App\Http\Controllers\Admins\AuthController;
use App\Http\Controllers\Users\AuthController as UserAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::controller(OrderController::class)->group(function () {
    Route::get('/orders/{id}', 'show');
    Route::post('/orders', 'store');
});

Route::controller(UserAuthController::class)->group(function () {
    Route::get('login', 'showLoginForm')->name('user-get-login');
    Route::post('login', 'login')->name('user-login');

    Route::get('register', 'register')->name('user-get-register');
    Route::post('register', 'postRegister')->name('user-register');

    Route::get('reset-password', 'resetPassword')->name('user-get-reset-password');
    Route::post('reset-password', 'sendMailResetPassword')->name('user-reset-password');
    
    Route::get('reset-password/{token}', 'formResetPassword')->name('user-form-reset-password');
    Route::post('reset-password/{token}', 'reset')->name('user-confirm-reset-password');
});

Route::prefix('admin')->group(function() {
    Route::controller(AuthController::class)->group(function () {
        Route::get('login', 'showLoginForm')->name('admin-get-login');
        Route::post('login', 'login')->name('admin-login');
    
        Route::get('register', 'register')->name('admin-get-register');
        Route::post('register', 'postRegister')->name('admin-register');
    
        Route::get('reset-password', 'resetPassword')->name('admin-get-reset-password');
        Route::post('reset-password', 'sendMailResetPassword')->name('admin-reset-password');
        
        Route::get('reset-password/{token}', 'formResetPassword')->name('admin-form-reset-password');
        Route::post('reset-password/{token}', 'reset')->name('admin-confirm-reset-password');
    });
});




Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
