<?php

use App\Http\Controllers\Api\Admin\EquipementController as AdminEquipementController;
use App\Http\Controllers\Api\Admin\ImageController as AdminImageController;
use App\Http\Controllers\Api\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Api\Admin\SalleController as AdminSalleController;
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
        Route::apiResource('salles', AdminSalleController::class);
        Route::get('images/salle/{salleId}', [AdminImageController::class, 'bySalle']);
        Route::apiResource('images', AdminImageController::class);
        Route::apiResource('equipements', AdminEquipementController::class);

        // Gestion des réservations (actions spécifiques & CRUD complet)
        Route::patch('reservations/{id}/confirm', [AdminReservationController::class, 'confirmer']);
        Route::patch('reservations/{id}/reject', [AdminReservationController::class, 'rejeter']);
        Route::patch('reservations/{id}/terminate', [AdminReservationController::class, 'terminer']);
        Route::apiResource('reservations', AdminReservationController::class);
    });
});