<?php

use App\Http\Controllers\ChargeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ChargeController::class, 'index'])->name('home');
Route::get('/dashboard', [ChargeController::class, 'index'])->name('dashboard');
Route::get('/payments/one-time', [ChargeController::class, 'oneTime'])->name('charges.one-time');
Route::get('/payments/monthly', [ChargeController::class, 'monthly'])->name('charges.monthly');
Route::get('/charges/{charge}', [ChargeController::class, 'show'])->name('charges.show');
Route::get('/charges/{charge}/edit', [ChargeController::class, 'edit'])->name('charges.edit');
Route::put('/charges/{charge}', [ChargeController::class, 'update'])->name('charges.update');
Route::post('/charges', fn () => to_route('dashboard'))->name('charges.store');
