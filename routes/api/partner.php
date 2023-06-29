<?php

use App\Http\Controllers\Api\Partner\AuthController;
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
Route::post('partner/login', [AuthController::class, 'login'])->name('login');

Route::group( ['prefix' => 'partner', 'middleware' => ['auth:api-user', 'role:partner'] ],function(){
    Route::post('test', function() {
        return 'trang chu hr';
    });

    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('partner-management')->group(function() {
        Route::get('index', [UserController::class, 'index'])->name('admin.user.index');
        Route::get('create', [UserController::class, 'create'])->name('admin.user.create');
        Route::post('store', [UserController::class, 'store'])->name('admin.user.store');
        Route::get('edit/{id}', [UserController::class, 'edit'])->name('admin.user.edit');
        Route::put('update/{id}', [UserController::class, 'update'])->name('admin.user.update');
        Route::delete('destroy/{id}', [UserController::class, 'destroy'])->name('admin.user.destroy');
        
    });

});
