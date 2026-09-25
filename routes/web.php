<?php

use App\Http\Controllers\ChargeController;
use App\Http\Controllers\ManageAccount;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', [ChargeController::class, 'index'])->name('home');
    Route::get('/dashboard', [ChargeController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export-csv', [ChargeController::class, 'exportCsv'])->name('dashboard.export-csv');
    Route::get('/payments/one-time', [ChargeController::class, 'oneTime'])->name('charges.one-time');
    Route::get('/payments/monthly', [ChargeController::class, 'monthly'])->name('charges.monthly');
    Route::get('/charges/{charge}/documents/{document}', [ChargeController::class, 'printDocument'])
        ->whereIn('document', ['contract', 'ba'])
        ->name('charges.documents.print');
    Route::get('/charges/{charge}', [ChargeController::class, 'show'])->name('charges.show');
    Route::get('/charges/{charge}/print', [ChargeController::class, 'print'])->name('charges.print');
    Route::get('/charges/{charge}/edit', [ChargeController::class, 'edit'])->name('charges.edit');
    Route::put('/charges/{charge}', [ChargeController::class, 'update'])->name('charges.update');
    Route::post('/charges', [ChargeController::class, 'store'])->name('charges.store');
    Route::get('/account', [ManageAccount::class, 'resetpassword'])->name('user.reset.password');
    Route::put('/account', [ManageAccount::class, 'changepassword'])->name('user.change.password');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/account', [ManageAccount::class, 'showAccounts'])->name('account.show');
    Route::get('/account/create', [ManageAccount::class, 'createAccount'])->name('account.create');
    Route::post('/account/create', [ManageAccount::class, 'storeAccount'])->name('account.store');
    Route::get('/account/edit/{id}', [ManageAccount::class, 'editAccount'])->name('account.edit');
    Route::put('/account/edit/{id}', [ManageAccount::class, 'editpush'])->name('account.edit.push');
    Route::delete('/account/delete/{id}', [ManageAccount::class, 'deleteAccount'])->name('account.delete');
    Route::delete('/charges/delete', [ChargeController::class, 'deleteSelected'])->name('charges.delete.selected');
});

require __DIR__.'/auth.php';
