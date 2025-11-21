<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RecuentoController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\PromesaController;
use App\Http\Controllers\CultoController;
use App\Http\Controllers\IngresosAsistenciaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard principal - todos los usuarios autenticados
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Vista pública para rol "general" - solo lectura
Route::middleware(['auth', 'role:general,tesorero,admin'])->group(function () {
    Route::get('/ingresos-asistencia', [IngresosAsistenciaController::class, 'index'])->name('ingresos-asistencia.index');
});

// Rutas para admin y tesorero
Route::middleware(['auth', 'role:admin,tesorero'])->group(function () {
    // Cultos
    Route::resource('cultos', CultoController::class);
    
    // Recuento (Sobres)
    Route::prefix('recuento')->name('recuento.')->group(function () {
        Route::get('/', [RecuentoController::class, 'index'])->name('index');
        Route::get('/create', [RecuentoController::class, 'create'])->name('create');
        Route::post('/', [RecuentoController::class, 'store'])->name('store');
        Route::get('/{sobre}/edit', [RecuentoController::class, 'edit'])->name('edit');
        Route::put('/{sobre}', [RecuentoController::class, 'update'])->name('update');
        Route::delete('/{sobre}', [RecuentoController::class, 'destroy'])->name('destroy');
    });
    
    // Asistencia
    Route::resource('asistencia', AsistenciaController::class);
    
    // Personas y Promesas
    Route::resource('personas', PersonaController::class);
    Route::resource('promesas', PromesaController::class);
});

// Solo admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Configuraciones adicionales si es necesario
});

require __DIR__.'/auth.php';
