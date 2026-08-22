<?php

use App\Http\Controllers\Admin\ListadoController;
use App\Http\Controllers\Admin\MesaController;
use App\Http\Controllers\Api\HorariosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservaCancelacionController;
use App\Http\Controllers\ReservaController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('reservas.create'));

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/reservas', [ReservaController::class, 'create'])->name('reservas.create');
Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
Route::get('/mis-reservas', [ReservaController::class, 'misReservas'])->name('reservas.mis-reservas');
Route::post('/mis-reservas', [ReservaController::class, 'misReservas'])->name('reservas.mis-reservas.buscar');
Route::get('/reservas/{reserva}/cancelar', [ReservaCancelacionController::class, 'cancelar'])
    ->middleware('signed')
    ->name('reservas.cancelar');
Route::get('/api/horarios', [HorariosController::class, 'slots'])->name('api.horarios.slots');

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('mesas', MesaController::class)->except(['show']);
        Route::delete('reservas/{reserva}', [ReservaCancelacionController::class, 'cancelar'])
            ->name('reservas.cancelar');
        Route::get('listado', [ListadoController::class, 'index'])->name('listado.index');
    });

require __DIR__.'/auth.php';