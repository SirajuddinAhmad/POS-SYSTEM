<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public Webhook Midtrans (No Auth, No CSRF)
Route::post('/midtrans/notification', [PosController::class, 'midtransNotification'])->name('midtrans.notification');

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Common Profile (Both Admin and Cashier can edit their personal profile)
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // POS Kasir System (Accessible by Cashier & Admin)
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::get('/products', [PosController::class, 'getProducts'])->name('products');
        Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
        Route::post('/midtrans-token', [PosController::class, 'getMidtransToken'])->name('midtrans-token');
        Route::get('/receipt/{id}', [PosController::class, 'printReceipt'])->name('receipt');
        Route::get('/history', [PosController::class, 'history'])->name('history');
        Route::get('/print-sales', [PosController::class, 'printSales'])->name('print-sales');
        Route::get('/export-sales-pdf', [PosController::class, 'exportSalesPdf'])->name('export-pdf');
    });

    // Administrator Only Routes
    Route::middleware('role:admin')->group(function () {
        // Admin Dashboard
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Products CRUD
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{id}', [ProductController::class, 'show'])->name('show');
            Route::put('/{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
        });

        // Laba Bersih & Rugi Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
            Route::get('/profit-loss/print', [ReportController::class, 'printProfitLoss'])->name('profit-loss.print');
            Route::get('/profit-loss/pdf', [ReportController::class, 'exportProfitLossPdf'])->name('profit-loss.pdf');
            Route::get('/profit-loss/excel', [ReportController::class, 'exportProfitLossExcel'])->name('profit-loss.excel');
        });

        // Expenses / Beban Operasional
        Route::resource('expenses', ExpenseController::class)->except(['create', 'show']);

        // User Management (Admin only)
        Route::resource('users', UserController::class)->except(['create', 'show']);

        // Store & Payment Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Hapus Riwayat Transaksi (Admin only)
        Route::delete('/pos/history/{id}', [PosController::class, 'destroyHistory'])->name('pos.history.destroy');
    });
});
