<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\AttendanceController;
use App\Http\Controllers\Employee\LeaveController as EmployeeLeaveController;
use App\Http\Controllers\Boss\DashboardController as BossDashboardController;
use App\Http\Controllers\Boss\MonitorController;
use App\Http\Controllers\Boss\EmployeeController;
use App\Http\Controllers\Boss\LeaveApprovalController;
use App\Http\Controllers\Boss\ReportController;
use App\Http\Controllers\Boss\SettingController;

// Auth Routes
Route::get('/', [AuthController::class , 'showLogin'])->name('home');
Route::get('/login', [AuthController::class , 'showLogin'])->name('login');
Route::post('/login', [AuthController::class , 'login']);
Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

// Employee Routes
Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class , 'index'])->name('dashboard');

    Route::get('/check-in', [AttendanceController::class , 'checkInForm'])->name('check-in');
    Route::post('/check-in', [AttendanceController::class , 'checkIn'])->name('check-in.store');

    Route::get('/check-out', [AttendanceController::class , 'checkOutForm'])->name('check-out');
    Route::post('/check-out', [AttendanceController::class , 'checkOut'])->name('check-out.store');

    Route::get('/history', [AttendanceController::class , 'history'])->name('history');

    Route::get('/leaves', [EmployeeLeaveController::class , 'index'])->name('leaves.index');
    Route::get('/leaves/create', [EmployeeLeaveController::class , 'create'])->name('leaves.create');
    Route::post('/leaves', [EmployeeLeaveController::class , 'store'])->name('leaves.store');
});

// Boss Routes
Route::middleware(['auth', 'role:boss'])->prefix('boss')->name('boss.')->group(function () {
    Route::get('/dashboard', [BossDashboardController::class , 'index'])->name('dashboard');

    Route::get('/monitor', [MonitorController::class , 'index'])->name('monitor');

    Route::resource('employees', EmployeeController::class)->except('show');
    Route::post('/employees/{employee}/reset-password', [EmployeeController::class , 'resetPassword'])->name('employees.reset-password');

    Route::get('/leaves', [LeaveApprovalController::class , 'index'])->name('leaves.index');
    Route::post('/leaves/{leave}/approve', [LeaveApprovalController::class , 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject', [LeaveApprovalController::class , 'reject'])->name('leaves.reject');

    Route::get('/reports', [ReportController::class , 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class , 'export'])->name('reports.export');

    Route::get('/settings', [SettingController::class , 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class , 'update'])->name('settings.update');
});
