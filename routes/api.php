<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// User Registration and Login
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('/login2', [UserController::class, 'login']);
