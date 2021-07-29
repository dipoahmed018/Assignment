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

Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/products/{product}', [ProductController::class, 'getProduct'])->name('product');

Route::middleware('guest')->group(function(){
    Route::post('/user/login', [UserController::class, 'login'])->name('user.login');
    Route::post('/user/register', [UserController::class, 'register'])->name('user.register');
});

Route::middleware(['auth:sanctum'])->group(function(){
    Route::prefix('user')->group(function(){
        //read
        Route::get('/{user}', [UserController::class, 'userInfo'])->name('user');
        //update
        
    });

    Route::prefix('product')->group(function(){
        //create
        Route::post('/create', [ProductController::class, 'createProduct'])->name('product.create');

        //update
        Route::put('/update', [ProductController::class, 'updateProduct'])->name('product.update');

        //delete
        Route::delete('/delete', [ProductController::class, 'deleteProduct'])->name('product.delete');

    });
});