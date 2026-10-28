<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Route Sederhana (Step 1 MIDDLEWARE_RBAC.pdf)
Route::get('/hello', function () {
    return "Halo, ini halaman percobaan route!";
});

// Halaman Utama / Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// 2. Route Autentikasi Pengunjung (AUTH_2.pdf)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// 3. Route yang Memerlukan Login (Middleware 'auth' - Step 3 & Latihan 1 MIDDLEWARE_RBAC.pdf)
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard & Profile
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');

    // Menampilkan & Pencarian Transaksi (Bisa diakses User & Admin)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/search', [TransactionController::class, 'search'])->name('transactions.search');

    // 4. Route Khusus Admin (Middleware 'isAdmin' - Step 6 & Latihan 2 MIDDLEWARE_RBAC.pdf)
    Route::middleware('isAdmin')->group(function () {
        // Halaman Admin Dashboard
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::post('/admin/users/{id}/role', [AdminController::class, 'updateRole'])->name('admin.users.updateRole');

        // Hak Akses Manajemen Transaksi (Create, Edit, Delete Khusus Admin)
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/{id}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');
        Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    });
});
