<?php

use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Routes protégées par authentification Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Espace d'administration (accessible uniquement avec le rôle 'admin')
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('users/role/{role}', [AdminUserController::class, 'byRole']);
        Route::apiResource('users', AdminUserController::class);
    });
});