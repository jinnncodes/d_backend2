<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

// REQUEST
Route::middleware('auth:sanctum')->post('/requests', [RequestController::class, 'store']);
Route::middleware('auth:sanctum')->put('/requests/{id}', [RequestController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/requests/{id}', [RequestController::class, 'destroy']);

// USERS
// Get current authenticated user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->post('/user', [UserController::class, 'store']);
Route::middleware('auth:sanctum')->put('/user/{id}', [UserController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/user/{id}', [UserController::class, 'destroy']);