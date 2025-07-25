<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Route::prefix('admin')->group(function () {


    Route::group(['middleware' => 'auth'], function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.index');
        Route::resource('/category', CategoryController::class, ['as' => 'admin']);
        Route::resource('/product', ProductController::class, ['as' => 'admin']);
        Route::resource('/order', OrderController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy'], 'as' => 'admin']);
        Route::get('/customer', [CustomerController::class, 'index'])->name('admin.customer.index');
        Route::resource('/slider', SliderController::class, ['except' => ['show', 'create', 'edit', 'update'], 'as' => 'admin']);
        Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile.index');
        Route::resource('/user', UserController::class, ['except' => ['show'], 'as' => 'admin']);
    });
});
