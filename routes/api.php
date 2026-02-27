<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeApiController;
use App\Http\Controllers\Api\BossApiController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Employee routes
    Route::prefix('employee')->group(function () {
        Route::get('/dashboard', [EmployeeApiController::class, 'dashboard']);
        Route::post('/check-in', [EmployeeApiController::class, 'checkIn']);
        Route::get('/check-out-info', [EmployeeApiController::class, 'checkOutInfo']);
        Route::post('/check-out', [EmployeeApiController::class, 'checkOut']);
        Route::get('/history', [EmployeeApiController::class, 'history']);
        Route::get('/leaves', [EmployeeApiController::class, 'leaves']);
        Route::post('/leaves', [EmployeeApiController::class, 'storeLeave']);
    });

    // Boss routes
    Route::prefix('boss')->group(function () {
        Route::get('/dashboard', [BossApiController::class, 'dashboard']);
        Route::get('/monitor', [BossApiController::class, 'monitor']);
        Route::get('/employees', [BossApiController::class, 'employees']);
        Route::post('/employees', [BossApiController::class, 'storeEmployee']);
        Route::get('/employees/{employee}', [BossApiController::class, 'showEmployee']);
        Route::put('/employees/{employee}', [BossApiController::class, 'updateEmployee']);
        Route::delete('/employees/{employee}', [BossApiController::class, 'destroyEmployee']);
        Route::post('/employees/{employee}/reset-password', [BossApiController::class, 'resetPassword']);
        Route::get('/leaves', [BossApiController::class, 'leaves']);
        Route::post('/leaves/{leave}/approve', [BossApiController::class, 'approveLeave']);
        Route::post('/leaves/{leave}/reject', [BossApiController::class, 'rejectLeave']);
        Route::get('/reports', [BossApiController::class, 'reports']);
        Route::get('/settings', [BossApiController::class, 'settings']);
        Route::put('/settings', [BossApiController::class, 'updateSettings']);
    });
});
