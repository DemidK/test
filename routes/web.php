<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\CustomTableController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NavController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TransportationOrderController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetSchemaMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Здесь регистрируются веб-маршруты для вашего приложения.
|
*/

// Получаем основной домен из .env, с 'domain.lv' в качестве значения по умолчанию
$appDomain = env('APP_DOMAIN', 'test');


/**
 * Хелпер для создания ресурсных маршрутов с middleware для прав доступа.
 */
function permissionResource($name, $controller, $options = [])
{
    $resource = $options['permission'] ?? $name;
    $only = $options['only'] ?? ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
    $except = $options['except'] ?? [];
    $permissions = [
        'index' => "view_{$resource}", 'show' => "view_{$resource}",
        'create' => "create_{$resource}", 'store' => "create_{$resource}",
        'edit' => "edit_{$resource}", 'update' => "edit_{$resource}",
        'destroy' => "delete_{$resource}"
    ];

    if (in_array('index', $only) && !in_array('index', $except)) {
        Route::get($name, [$controller, 'index'])->name("{$name}.index")->middleware("permission:{$permissions['index']}");
    }
    if (in_array('create', $only) && !in_array('create', $except)) {
        Route::get("{$name}/create", [$controller, 'create'])->name("{$name}.create")->middleware("permission:{$permissions['create']}");
    }
    if (in_array('store', $only) && !in_array('store', $except)) {
        Route::post($name, [$controller, 'store'])->name("{$name}.store")->middleware("permission:{$permissions['store']}");
    }
    if (in_array('show', $only) && !in_array('show', $except)) {
        Route::get("{$name}/{id}", [$controller, 'show'])->name("{$name}.show")->middleware("permission:{$permissions['show']}");
    }
    if (in_array('edit', $only) && !in_array('edit', $except)) {
        Route::get("{$name}/{id}/edit", [$controller, 'edit'])->name("{$name}.edit")->middleware("permission:{$permissions['edit']}");
    }
    if (in_array('update', $only) && !in_array('update', $except)) {
        Route::put("{$name}/{id}", [$controller, 'update'])->name("{$name}.update")->middleware("permission:{$permissions['update']}");
        Route::patch("{$name}/{id}", [$controller, 'update'])->middleware("permission:{$permissions['update']}");
    }
    if (in_array('destroy', $only) && !in_array('destroy', $except)) {
        Route::delete("{$name}/{id}", [$controller, 'destroy'])->name("{$name}.destroy")->middleware("permission:{$permissions['destroy']}");
    }
    return;
}

// =========================================================================
// ГЛОБАЛЬНЫЕ МАРШРУТЫ (не зависят от домена)
// =========================================================================
Route::get('/run-update', [DashboardController::class, 'runUpdate'])
    ->name('app.run_update')
    ->middleware('auth');

// =========================================================================
// МАРШРУТЫ ДЛЯ ОСНОВНОГО ДОМЕНА
// =========================================================================
Route::domain($appDomain)->group(function () {
    // Главная страница
    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

    // Страница статуса после регистрации клиента
    Route::get('platform-setup', function () {
        return view('status.platform-setup');
    })->name('platform.setup');

    // Маршруты для гостей (логин администраторов и регистрация новых клиентов)
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);

        // Регистрация нового клиента (создание схемы)
        Route::get('register', [RegisterController::class, 'showTenantRegistrationForm'])->name('register.tenant');
        Route::post('register', [RegisterController::class, 'registerTenant'])->name('register.tenant.submit');
    });

    // Маршрут выхода из системы для администраторов
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout.global');
    });
});


// =========================================================================
// МАРШРУТЫ ДЛЯ СУБДОМЕНОВ КЛИЕНТОВ
// =========================================================================
Route::domain("{schemaName}.$appDomain")->middleware(SetSchemaMiddleware::class)->group(function () {
    // Маршруты для гостей на субдомене (логин и регистрация пользователя в клиенте)
    Route::middleware('guest')->group(function () {
        // Корневой маршрут субдомена ведет на страницу логина
        Route::get('/', [LoginController::class, 'showLoginForm']);
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login.schema');
        Route::post('login', [LoginController::class, 'login'])->name('login.schema.submit');

        // Регистрация нового пользователя внутри существующего клиента
        Route::get('register', [RegisterController::class, 'showUserRegistrationForm'])->name('register.user');
        Route::post('register', [RegisterController::class, 'registerUser'])->name('register.user.submit');
    });

    // Маршруты для аутентифицированных пользователей ВНУТРИ СХЕМЫ
    Route::middleware('auth')->group(function () {
        // Выход из системы
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        // Панель управления
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard')
            ->middleware('permission:access_dashboard');

        // Ресурсные маршруты с авто-правами
        permissionResource('partners', PartnerController::class);
        permissionResource('invoices', InvoiceController::class);
        permissionResource('transportation_orders', TransportationOrderController::class);
        permissionResource('custom-tables', CustomTableController::class);

        // Управление правами и ролями
        permissionResource('permissions', PermissionController::class);
        permissionResource('roles', RoleController::class);

        Route::post('/permissions/generate-crud', [PermissionController::class, 'generateCrud'])
            ->name('permissions.generate-crud')
            ->middleware('permission:create_permissions');

        // Управление правами пользователей
        Route::get('/users', [UserPermissionController::class, 'index'])
            ->name('users.index')
            ->middleware('permission:view_users');
        Route::get('/users/{id}', [UserPermissionController::class, 'show'])
            ->name('users.show')
            ->middleware('permission:view_users');
        Route::get('/users/{id}/permissions', [UserPermissionController::class, 'edit'])
            ->name('users.permissions.edit')
            ->middleware('permission:edit_users');
        Route::put('/users/{id}/permissions', [UserPermissionController::class, 'update'])
            ->name('users.permissions.update')
            ->middleware('permission:edit_users');

        // Экспорт PDF и прочее
        Route::get('/transportation_orders/{id}/export-pdf', [TransportationOrderController::class, 'exportPdf'])
            ->name('transportation_orders.exportPdf')
            ->middleware('permission:export_transportation_orders');
        Route::get('/search-partners', [PartnerController::class, 'searchPartners'])
            ->name('search.partners')
            ->middleware('permission:view_partners');
        Route::get('/invoices/{id}/preview-pdf', [InvoiceController::class, 'previewPdf'])
            ->name('invoices.previewPdf')
            ->middleware('permission:view_invoices');
        Route::get('/invoices/{id}/export-pdf', [InvoiceController::class, 'exportPdf'])
            ->name('invoices.exportPdf')
            ->middleware('permission:export_invoices');

        // Управление навигацией
        Route::post('/updateNavOrder', [NavController::class, 'updateOrder'])
            ->name('update.nav.order')
            ->middleware('permission:manage_navigation');

        // Маршруты конфигурации
        Route::middleware('permission:view_configs')->group(function () {
            Route::get('/configs', [ConfigController::class, 'index'])->name('configs.index');
            Route::middleware('permission:edit_configs')->group(function () {
                Route::get('/configs/{key}/edit', [ConfigController::class, 'edit'])->name('configs.edit');
                Route::put('/configs/{key}', [ConfigController::class, 'update'])->name('configs.update');
            });
        });

        // Управление данными в кастомных таблицах
        Route::prefix('data/{tableName}')->name('custom-tables.data.')->group(function () {
            Route::get('/', [CustomTableController::class, 'dataIndex'])->name('index')->middleware('permission:view_custom_table_data');
            Route::middleware('permission:create_custom_table_data')->group(function () {
                Route::get('/create', [CustomTableController::class, 'dataCreate'])->name('create');
                Route::post('/', [CustomTableController::class, 'dataStore'])->name('store');
            });
            Route::get('/{id}', [CustomTableController::class, 'dataShow'])->name('show')->middleware('permission:view_custom_table_data');
            Route::middleware('permission:edit_custom_table_data')->group(function () {
                Route::get('/{id}/edit', [CustomTableController::class, 'dataEdit'])->name('edit');
                Route::put('/{id}', [CustomTableController::class, 'dataUpdate'])->name('update');
            });
            Route::delete('/{id}', [CustomTableController::class, 'dataDestroy'])->name('destroy')->middleware('permission:delete_custom_table_data');
        });
    });
});