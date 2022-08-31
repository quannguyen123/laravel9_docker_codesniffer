<?php

use App\Http\Controllers\Admins\AuthController;
use App\Http\Controllers\Admins\HomeController;
use App\Http\Controllers\Admins\UserController;
use App\Http\Controllers\Users\AuthController as UserAuthController;
use App\Http\Controllers\Users\HomeController as UsersHomeController;
use App\Http\Controllers\Users\SendMailController;
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

// Route::name()->group(function () {
    Route::get('login', [UserAuthController::class, 'showLoginForm'])->name('user-get-login');
    Route::post('login', [UserAuthController::class, 'login'])->name('user-login');

    Route::get('register', [UserAuthController::class, 'register'])->name('user-get-register');
    Route::post('register', [UserAuthController::class, 'postRegister'])->name('user-register');

    Route::get('reset-password', [UserAuthController::class, 'resetPassword'])->name('user-get-reset-password');
    Route::post('reset-password', [UserAuthController::class, 'sendMailResetPassword'])->name('user-reset-password');

    Route::get('reset-password/{token}', [UserAuthController::class, 'formResetPassword'])->name('user-form-reset-password');
    Route::post('reset-password/{token}', [UserAuthController::class, 'reset'])->name('user-confirm-reset-password');
// });

Route::middleware(['auth:web'])->group(function () {
    Route::get('', [UsersHomeController::class, 'index'])->name('home');

    Route::get('logout', [UserAuthController::class, 'logout'])->name('logout');
    Route::get('send-mail', [SendMailController::class, 'sendMail'])->name('send-mail');
});

Route::prefix('admin')->group(function() {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('admin-get-login');
    Route::post('login', [AuthController::class, 'login'])->name('admin-login');

    Route::get('register', [AuthController::class, 'register'])->name('admin-get-register');
    Route::post('register', [AuthController::class, 'postRegister'])->name('admin-register');

    Route::get('reset-password', [AuthController::class, 'resetPassword'])->name('admin-get-reset-password');
    Route::post('reset-password', [AuthController::class, 'sendMailResetPassword'])->name('admin-reset-password');
    
    Route::get('reset-password/{token}', [AuthController::class, 'formResetPassword'])->name('admin-form-reset-password');
    Route::post('reset-password/{token}', [AuthController::class, 'reset'])->name('admin-confirm-reset-password');

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('', [HomeController::class, 'dashboard'])->name('admin-dashboard');

        Route::get('logout', [AuthController::class, 'logout'])->name('admin-logout');
        
        Route::post('/set-cookie', [HomeController::class, 'setCookie'])->name('admin.set-cookie');

        Route::prefix('management-user')->group(function() {
            Route::get('index', [UserController::class, 'index'])->name('admin.user.index');
            Route::get('create', [UserController::class, 'create'])->name('admin.user.create');
            Route::post('store', [UserController::class, 'store'])->name('admin.user.store');
            Route::get('edit/{id}', [UserController::class, 'edit'])->name('admin.user.edit');
            Route::put('update/{id}', [UserController::class, 'update'])->name('admin.user.update');
            Route::delete('destroy/{id}', [UserController::class, 'destroy'])->name('admin.user.destroy');
            
        });
    });
});

Route::fallback(function () {
    return view('404');
});


