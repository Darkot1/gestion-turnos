<?php

use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TurnosController;
use Inertia\Inertia;


Route::get('/turnos', [TurnosController::class, 'standby'])->name("turnos");

Route::get('/', function () {
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('modules')->group(function () {
        Route::get('/', [ModuleController::class, 'index'])->name('modules.index');
        Route::post('/', [ModuleController::class, 'store'])->name('modules.store');
        Route::put('/{module}', [ModuleController::class, 'update'])->name('modules.update');
    });

    Route::get('/shifts/pending', [ShiftController::class, 'showPendingShifts'])
        ->name('shifts.pending');
    Route::put('/shifts/{shift}/status', [ShiftController::class, 'updateShiftStatus'])
        ->name('shifts.updateStatus');
});

// Ruta para invitados
Route::get('/shifts/in-process', [ShiftController::class, 'showInProcessShifts'])
    ->name('shifts.inProcess');

require __DIR__.'/auth.php';
