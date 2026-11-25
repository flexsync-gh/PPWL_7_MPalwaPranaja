<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SavingGoalController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Route Sederhana
Route::get('/hello', function () {
    return 'Halo, ini halaman percobaan route!';
});

// Halaman Utama / Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// 2. Route Autentikasi Pengunjung (Middleware Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Login SSO Google OAuth
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

// 3. Route yang Memerlukan Login (Middleware auth - Unlocked untuk User & Admin)
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard & Profile
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');

    // Manajemen Transaksi (Unlocked untuk seluruh User terautentikasi & Admin)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/search', [TransactionController::class, 'search'])->name('transactions.search');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{id}', [TransactionController::class, 'show'])->whereNumber('id')->name('transactions.show');
    Route::get('/transactions/{id}/edit', [TransactionController::class, 'edit'])->whereNumber('id')->name('transactions.edit');
    Route::put('/transactions/{id}', [TransactionController::class, 'update'])->whereNumber('id')->name('transactions.update');
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->whereNumber('id')->name('transactions.destroy');

    // Manajemen Akun Keuangan, Kategori, Budget Bulanan, dan Target Tabungan (Fitur Finote)
    Route::resource('accounts', AccountController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('budgets', BudgetController::class)->except(['show']);
    Route::resource('savings', SavingGoalController::class);
    Route::get('/savings/{saving}/transactions/create', [SavingGoalController::class, 'createTransaction'])->name('savings.transactions.create');
    Route::post('/savings/{saving}/transactions', [SavingGoalController::class, 'storeTransaction'])->name('savings.transactions.store');
    Route::delete('/savings/transactions/{transaction}', [SavingGoalController::class, 'destroyTransaction'])->name('savings.transactions.destroy');

    // 4. Route Khusus Admin (Middleware isAdmin - RBAC)
    Route::middleware('isAdmin')->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::post('/admin/users/{id}/role', [AdminController::class, 'updateRole'])->name('admin.users.updateRole');
        Route::post('/admin/notifications/mark-all-read', [AdminController::class, 'markAllNotificationsRead'])->name('admin.notifications.markAllRead');
        Route::post('/admin/notifications/{id}/read', [AdminController::class, 'markNotificationRead'])->name('admin.notifications.read');
    });
});

// 5. Route Galeri & Konten Finansial
Route::resource('gallery', GalleryController::class);

// 6. Route Page Dengan SEO
Route::get('/edukasi-keuangan', [GalleryController::class, 'financialIndex'])->name('fintrack.edukasi');
Route::get('/edukasi-keuangan/{title}', [GalleryController::class, 'financialDetail'])->name('fintrack.edukasi.detail');
Route::get('/gallery/detail/{title}', [GalleryController::class, 'financialDetail'])->name('gallery.detail.seo');
Route::get('/gallery/{id}/upload-foto', [GalleryController::class, 'createGalleryPhoto'])->name('gallery.photo.create');
Route::post('/gallery/{id}/upload-foto', [GalleryController::class, 'storeGalleryPhoto'])->name('gallery.photo.store');

// 7. Search Engine Optimization (XML Sitemap)
Route::get('/sitemap.xml', [GalleryController::class, 'sitemap'])->name('fintrack.sitemap');
