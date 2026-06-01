<?php

use App\Http\Controllers\BarberController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (Peluquería / Barbería)
|--------------------------------------------------------------------------
*/

// Página de inicio (Landing Page)
Route::get('/', [BarberController::class, 'index'])->name('home');

// Formulario interactivo de reservas (Ver y Enviar)
Route::get('/reservar', [BarberController::class, 'showBookingForm'])->name('booking.form');
Route::post('/reservar', [BarberController::class, 'storeBooking'])->name('booking.store');

/*
|--------------------------------------------------------------------------
| Rutas del Panel de Administración (Privado)
|--------------------------------------------------------------------------
*/

// Pantalla de login y procesamiento
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');

// Cerrar sesión
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// Panel de control (Dashboard) y acciones sobre las citas
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::post('/admin/appointments/{appointment}/status', [AdminController::class, 'updateStatus'])->name('admin.appointment.status');

// Gestión de Barberos (Especialistas)
Route::get('/admin/specialists', [AdminController::class, 'specialistsIndex'])->name('admin.specialists.index');
Route::post('/admin/specialists', [AdminController::class, 'storeSpecialist'])->name('admin.specialists.store');
Route::put('/admin/specialists/{specialist}', [AdminController::class, 'updateSpecialist'])->name('admin.specialists.update');
Route::delete('/admin/specialists/{specialist}', [AdminController::class, 'destroySpecialist'])->name('admin.specialists.destroy');

// Dashboard Exclusivo de Barberos (Privado)
Route::get('/barber/dashboard', [AdminController::class, 'barberDashboard'])->name('barber.dashboard');
