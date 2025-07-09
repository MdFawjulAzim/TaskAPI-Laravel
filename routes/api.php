<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::post('/create-task', [TaskController::class, 'store'])->middleware('auth:api')->name('create.task');
Route::get('/show-task/{id}', [TaskController::class, 'show'])->middleware('auth:api')->name('show.task');
Route::get('/delete-task/{id}', [TaskController::class, 'delete'])->middleware('auth:api')->name('delete.task');
Route::post('/update-task/{id}', [TaskController::class, 'update'])->middleware('auth:api')->name('update.task');
