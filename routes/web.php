<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;

// ----------------------------------------------------
// LOGIN (sin Breeze - controlador simple hecho a mano)
// ----------------------------------------------------
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ----------------------------------------------------
// RUTAS PARA COLABORADORES (requieren login)
// ----------------------------------------------------
Route::middleware(['auth'])->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('marcador');
        Route::post('/api/check-in', [AttendanceController::class, 'checkIn']);
        Route::post('/api/check-out', [AttendanceController::class, 'checkOut']);
});

// ----------------------------------------------------
// SUPERVISOR Y ADMIN (permisos, horas extra)
// ----------------------------------------------------
Route::middleware(['auth', 'role:admin,supervisor'])->prefix('supervisor')->group(function () {
        Route::get('/dashboard', function () {
                    return view('admin.dashboard');
        })->name('supervisor.dashboard');
});

// ----------------------------------------------------
// SOLO ADMIN (equipos, reportes generales)
// ----------------------------------------------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
                    return view('admin.dashboard');
        })->name('admin.dashboard');
});
