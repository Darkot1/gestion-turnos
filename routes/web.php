<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//turnos
Route::get('/turnos/generar', [ShiftController::class, 'generate']);
Route::post('/turnos/imprimir/{id}', [ShiftController::class, 'imprimir']);

//modulos use App\Http\Controllers\ModuleController;

Route::post('/modules', [ModuleController::class, 'create']);  // Crear caja
Route::delete('/modules/{id}', [ModuleController::class, 'delete']);  // Eliminar caja

// usuarios
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::patch('/users/restore/{id}', [UserController::class, 'restore']);  


require __DIR__.'/auth.php';
