<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/acerca', [HomeController::class, 'about'])->name('about');
Route::get('/contacto', [HomeController::class, 'contact'])->name('contact');

Route::post('/contacto', [HomeController::class, 'sendContact'])
    ->middleware('throttle:10,1')
    ->name('contact.send');

use App\Http\Controllers\AuthController;

Route::middleware('guest')->group(function () {
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.store');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.store');

    // CMS-04 - Recuperación de contraseña
    Route::get('/olvide-password', [AuthController::class, 'showForgotPassword'])
        ->name('password.request');

    Route::post('/olvide-password', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:6,1')
        ->name('password.email');

    Route::get('/restablecer-password/{token}', [AuthController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/restablecer-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:6,1')
        ->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::resource('posts', PostController::class)
    ->middleware('auth');

use App\Http\Controllers\ProductoController;

// CMS-09 a 12 - Gestión de productos e inventario (panel de administración)
// 'show' se excluye aquí porque la versión pública vive en /catalogo/{producto}
// más abajo (sin middleware auth, con su propia lógica de visibilidad).
Route::resource('productos', ProductoController::class)
    ->except(['show'])
    ->middleware('auth');

// CMS-11 - Catálogo público: sin login, solo productos activos y con stock
Route::get('/catalogo', [ProductoController::class, 'catalogo'])->name('catalogo.index');
Route::get('/catalogo/{producto}', [ProductoController::class, 'show'])->name('catalogo.show');