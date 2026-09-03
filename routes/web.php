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
        ->middleware('throttle:5,1')
        ->name('login.store');
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
    