<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\CustomTableController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NavController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TransportationOrderController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\TodoCommentController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetSchemaMiddleware;
use App\Http\Middleware\CheckPermission;

$appDomain = env('APP_DOMAIN', 'b4me.local');

// =========================================================================
// ОСНОВНОЙ ДОМЕН
// =========================================================================
Route::domain($appDomain)->group(function () {
    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
    Route::get('platform-setup', fn() => view('status.platform-setup'))->name('platform.setup');

    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
        // Единый метод для показа формы регистрации
        Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register.tenant');
        Route::post('register', [RegisterController::class, 'registerTenant'])->name('register.tenant.submit');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout.global')->middleware('auth');
});


// =========================================================================
// СУБДОМЕНЫ КЛИЕНТОВ
// =========================================================================
Route::domain("{schemaName}.$appDomain")->middleware(SetSchemaMiddleware::class)->group(function () {
    // Гостевые маршруты на субдомене
    Route::middleware('guest')->group(function () {
        Route::get('/', [LoginController::class, 'showLoginForm']);
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login.schema');
        Route::post('login', [LoginController::class, 'login'])->name('login.schema.submit');
        // Единый метод для показа формы регистрации
        Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register.user');
        Route::post('register', [RegisterController::class, 'registerUser'])->name('register.user.submit');
    });

    // Маршруты для аутентифицированных пользователей
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(CheckPermission::class);

        // Маршрут для запуска обновления приложения
        Route::get('/app-update', [DashboardController::class, 'runUpdate'])->name('app.run_update')->middleware(CheckPermission::class);

        // Ресурсные маршруты. Middleware теперь в контроллерах.
        Route::resource('partners', PartnerController::class)->middleware(CheckPermission::class);
        Route::resource('invoices', InvoiceController::class)->middleware(CheckPermission::class);
        Route::resource('transportation_orders', TransportationOrderController::class)->middleware(CheckPermission::class);
        Route::resource('custom-tables', CustomTableController::class)->middleware(CheckPermission::class);
        Route::resource('roles', RoleController::class)->middleware(CheckPermission::class);
        
        // Упрощаем маршруты для UserPermissionController
        Route::resource('users', UserPermissionController::class)->only(['index', 'show', 'edit', 'update'])->middleware(CheckPermission::class);

        // Прочие маршруты
        Route::get('/transportation_orders/{id}/export-pdf', [TransportationOrderController::class, 'exportPdf'])->name('transportation_orders.exportPdf')->middleware(CheckPermission::class);
        Route::get('/search-partners', [PartnerController::class, 'searchPartners'])->name('search.partners')->middleware(CheckPermission::class);
        Route::get('/invoices/{id}/preview-pdf', [InvoiceController::class, 'previewPdf'])->name('invoices.previewPdf')->middleware(CheckPermission::class);
        Route::get('/invoices/{id}/export-pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.exportPdf')->middleware(CheckPermission::class);
        Route::post('/updateNavOrder', [NavController::class, 'updateOrder'])->name('update.nav.order')->middleware(CheckPermission::class);
        
        // Маршруты конфигурации
        Route::resource('configs', ConfigController::class)->only(['index', 'edit', 'update'])->parameters(['configs' => 'key'])->middleware(CheckPermission::class);

        // Управление данными в кастомных таблицах
        Route::prefix('data/{tableName}')->name('custom-tables.data.')->controller(CustomTableController::class)->middleware(CheckPermission::class)->group(function () {
            Route::get('/', 'dataIndex')->name('index');
            Route::get('/create', 'dataCreate')->name('create');
            Route::post('/', 'dataStore')->name('store');
            Route::get('/{id}', 'dataShow')->name('show');
            Route::get('/{id}/edit', 'dataEdit')->name('edit');
            Route::put('/{id}', 'dataUpdate')->name('update');
            Route::delete('/{id}', 'dataDestroy')->name('destroy');
        });

        // =========================================================================
        // Управление задачами (ToDo)
        // =========================================================================
        Route::prefix('todos')->name('todos.')->controller(TodoController::class)->middleware(CheckPermission::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{todo}', 'show')->name('show');
            Route::get('/{todo}/edit', 'edit')->name('edit');
            Route::put('/{todo}', 'update')->name('update');
            Route::delete('/{todo}', 'destroy')->name('destroy');
        });

        // Маршрут для добавления комментариев к задачам
        Route::post('todos/{todo}/comments', [TodoCommentController::class, 'store'])->name('todos.comments.store')->middleware(CheckPermission::class);
    });
});