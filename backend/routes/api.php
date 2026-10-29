<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FuelRecordController;
use App\Http\Controllers\MaintenanceRecordController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:auth')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/stats', [DashboardController::class, 'index']);

    Route::apiResource('cars', CarController::class);
    Route::post('/cars/{car}/photo', [CarController::class, 'uploadPhoto']);
    Route::delete('/cars/{car}/photo', [CarController::class, 'deletePhoto']);

    Route::get('/cars/{car}/maintenance', [MaintenanceRecordController::class, 'index']);
    Route::post('/cars/{car}/maintenance', [MaintenanceRecordController::class, 'store']);
    Route::put('/maintenance/{maintenanceRecord}', [MaintenanceRecordController::class, 'update']);
    Route::delete('/maintenance/{maintenanceRecord}', [MaintenanceRecordController::class, 'destroy']);

    Route::get('/cars/{car}/fuel', [FuelRecordController::class, 'index']);
    Route::post('/cars/{car}/fuel', [FuelRecordController::class, 'store']);
    Route::put('/fuel/{fuelRecord}', [FuelRecordController::class, 'update']);
    Route::delete('/fuel/{fuelRecord}', [FuelRecordController::class, 'destroy']);
});
