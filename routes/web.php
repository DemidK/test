<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\CustomTableController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NavController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\TransportationOrderController;
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
    Route::get('/transportation_orders/{id}/export-pdf', [TransportationOrderController::class, 'exportPdf'])
    ->name('transportation_orders.exportPdf');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Existing routes
    Route::resource('partners', PartnerController::class);
    Route::resource('invoices', InvoiceController::class);
    Route::get('/search-partners', [App\Http\Controllers\PartnerController::class, 'searchPartners'])->name('search.partners');

    // New Transportation Orders Routes
    Route::resource('transportation_orders', TransportationOrderController::class);
    
    // Additional Invoice Routes
    Route::get('/invoices/{id}/preview-pdf', [InvoiceController::class, 'previewPdf'])->name('invoices.previewPdf');
    Route::get('/invoices/{id}/export-pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.exportPdf');
    
    Route::post('/updateNavOrder', [NavController::class, 'updateOrder'])->name('update.nav.order');
    
    Route::prefix('configs')->group(function () {
        Route::get('/', [ConfigController::class, 'index'])->name('configs.index');
        Route::get('/{key}/edit', [ConfigController::class, 'edit'])->name('configs.edit');
        Route::put('/{key}', [ConfigController::class, 'update'])->name('configs.update');
    });

    // Custom Tables Management
    Route::resource('custom-tables', CustomTableController::class);
    Route::get('/custom-tables/{id}/preview', [CustomTableController::class, 'preview'])
        ->name('custom-tables.preview');

    // Custom Table Data Management
    Route::prefix('data/{tableName}')->name('custom-tables.data.')->group(function () {
        Route::get('/', [CustomTableController::class, 'dataIndex'])->name('index');
        Route::get('/create', [CustomTableController::class, 'dataCreate'])->name('create');
        Route::post('/', [CustomTableController::class, 'dataStore'])->name('store');
        Route::get('/{id}', [CustomTableController::class, 'dataShow'])->name('show');
        Route::get('/{id}/edit', [CustomTableController::class, 'dataEdit'])->name('edit');
        Route::put('/{id}', [CustomTableController::class, 'dataUpdate'])->name('update');
        Route::delete('/{id}', [CustomTableController::class, 'dataDestroy'])->name('destroy');
    });
});