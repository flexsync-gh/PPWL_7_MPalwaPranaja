<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;


Route::get('/', function () {
    return view('welcome');
});

// read
Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

// create
Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');

// edit-update
Route::get('/transactions/{id}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
Route::put('/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');

// delete
Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
