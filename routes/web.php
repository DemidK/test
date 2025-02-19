<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\NavController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ClientController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::resource('invoices', InvoiceController::class);
Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.pdf');
Route::get('/invoices/{invoice}/preview', [InvoiceController::class, 'previewPdf'])->name('invoices.preview-pdf');
Route::resource('clients', ClientController::class);
Route::get('/invoices/{id}/export-pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.exportPdf');
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');
Route::post('/updateNavOrder', [NavController::class, 'updateOrder'])->name('update.nav.order');
Route::get('/', [WelcomeController::class, 'index']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
