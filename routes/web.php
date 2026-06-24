<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ItemController;

Route::get('/', [IndexController::class, 'index']);

// CRUD Itens (protegidas)
Route::middleware(['auth', 'can:admin'])->group(function () { 
    Route::get('/itens', [ItemController::class, 'index']);
    Route::get('/itens/create', [ItemController::class, 'create']);
    Route::post('/itens', [ItemController::class, 'store']);
    Route::get('/itens/{item}', [ItemController::class, 'show']);
    Route::get('/itens/{item}/edit', [ItemController::class, 'edit']);
    Route::patch('/itens/{item}', [ItemController::class, 'update']);
    Route::delete('/itens/{item}', [ItemController::class, 'destroy']);
});