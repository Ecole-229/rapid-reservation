<?php

use App\Http\Controllers\Api\User\EquipementController;
use App\Http\Controllers\Api\User\ReservationController;
use App\Http\Controllers\Api\User\SalleController;
use App\Http\Controllers\Api\Responsable\ReservationController as ResponsableReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/salles', [SalleController::class, 'index']);
Route::get('/salles/{salle}', [SalleController::class, 'show']);
Route::get('/salles/{salle}/disponibilites', [SalleController::class, 'disponibilites']);

Route::get('/equipements', [EquipementController::class, 'index']);
Route::get('/equipements/{equipement}', [EquipementController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->prefix('responsable')->group(function () {
    Route::get('/reservations', [ResponsableReservationController::class, 'index']);
    Route::patch('/reservations/{reservation}/confirmer', [ResponsableReservationController::class, 'confirmer']);
    Route::patch('/reservations/{reservation}/rejeter', [ResponsableReservationController::class, 'rejeter']);
});
