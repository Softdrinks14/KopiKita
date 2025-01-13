<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;


Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Route::post('/create-order', function () {
    //     return 'create-order';
    // })->middleware('ableCreateOrder');

    // Route::post('/finish-order', function () {
    //     return 'finish-order';
    // })->middleware('finishOrder');

    Route::get('/item', [ItemController::class, 'index']);
    Route::post('/item', [ItemController::class, 'store'])->middleware('ableCreateUpdateItem');
    Route::delete('/item/{id}', [ItemController::class, 'destroy'])->middleware('ableCreateUpdateItem');
    Route::patch('/item/{id}', [ItemController::class, 'update'])->middleware('ableCreateUpdateItem');
    Route::post('/category', [CategoryController::class, 'store'])->middleware('ableCreateUpdateItem');
    

    Route::post('/order', [OrderController::class, 'store'])->middleware('ableCreateOrder');
    Route::get('/order', [OrderController::class, 'index']);
    Route::get('/order/{id}', [OrderController::class, 'show']);
    Route::get('/order/{id}/done', [OrderController::class, 'done'])->middleware(['ableFinishOrder']);
    Route::get('/order/{id}/paid', [OrderController::class, 'paid'])->middleware(['ablePayOrder']);
});

Route::get('/category', [CategoryController::class, 'index']);
Route::post('/register', [UserController::class, 'store']);
