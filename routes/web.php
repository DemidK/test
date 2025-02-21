<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NavController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('clients', ClientController::class);
    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/{id}/preview-pdf', [InvoiceController::class, 'previewPdf'])->name('invoices.previewPdf');
    Route::get('/invoices/{id}/export-pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.exportPdf');
    Route::post('/updateNavOrder', [NavController::class, 'updateOrder'])->name('update.nav.order');
    Route::prefix('configs')->group(function () {
        Route::get('/', [ConfigController::class, 'index'])->name('configs.index');
        Route::get('/{key}/edit', [ConfigController::class, 'edit'])->name('configs.edit');
        Route::put('/{key}', [ConfigController::class, 'update'])->name('configs.update');
    });
});