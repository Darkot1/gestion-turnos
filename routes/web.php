<?php

use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Ruta principal para registro de usuario
Route::get('/', [UserController::class, 'index']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Rutas para módulos
    Route::resource('modules', ModuleController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/shifts/pending', [ShiftController::class, 'showPendingShifts'])
        ->name('shifts.pending');
    Route::put('/shifts/{shift}/status', [ShiftController::class, 'updateShiftStatus'])
        ->name('shifts.updateStatus');
});

// Rutas públicas
Route::get('/modules/public', [ModuleController::class, 'index']);

Route::get('/clinica', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/register-user', [UserController::class,'index']);
Route::post('/users',[UserController::class,'store']);

Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
Route::post('/shifts', [ShiftController::class, 'store']);

// Ruta para invitados
Route::get('/shifts/in-process', [ShiftController::class, 'showInProcessShifts'])
    ->name('shifts.inProcess');

require __DIR__.'/auth.php';
