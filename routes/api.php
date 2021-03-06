<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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
Route::get('/email/verify/{user}/{code}', [UserController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/forgot-password', [UserController::class, 'forgotPassword'])->name('password.email');
Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('password.update');

Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/products/{product}', [ProductController::class, 'getProduct'])->name('product');

Route::middleware('guest')->group(function(){
    Route::post('/user/login', [UserController::class, 'login'])->name('user.login');
    Route::post('/user/register', [UserController::class, 'register'])->name('user.register');
});

Route::middleware(['auth:sanctum'])->group(function(){
    Route::prefix('user')->group(function(){
        //read
        Route::get('/', [UserController::class, 'userInfo'])->name('user');
        //update
        Route::put('/update', [UserController::class, 'updateUserInfo'])->name('update.user');
        Route::put('/update/password',[UserController::class, 'changePassword'])->name('update.password');

        //logout
        Route::post('/logout', [UserController::class, 'logout'])->name('user.logout');
        //verification
        Route::get('/email/verify',[UserController::class, 'sendVerificationMail'])->name('verification.mail');
    });
    
    Route::prefix('product')->group(function(){
        //create
        Route::post('/create', [ProductController::class, 'createProduct'])->name('product.create');

        //update
        Route::put('/update/{product}', [ProductController::class, 'updateProduct'])->name('product.update');
        //delete
        Route::delete('/delete/{product}', [ProductController::class, 'deleteProduct'])->name('product.delete');

    });
});