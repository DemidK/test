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

function permissionResource($name, $controller, $options = [])
{
    // Get the resource name for permission generation
    $resource = $options['permission'] ?? $name;
    
    // Only register the specified methods
    $only = $options['only'] ?? ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
    $except = $options['except'] ?? [];
    
    // Map controller methods to permissions
    $permissions = [
        'index' => "view_{$resource}",
        'show' => "view_{$resource}",
        'create' => "create_{$resource}",
        'store' => "create_{$resource}",
        'edit' => "edit_{$resource}",
        'update' => "edit_{$resource}",
        'destroy' => "delete_{$resource}"
    ];
    
    // Register individual routes instead of using Route::resource
    
    // INDEX route
    if (in_array('index', $only) && !in_array('index', $except)) {
        Route::get($name, [$controller, 'index'])
            ->name("{$name}.index")
            ->middleware("permission:{$permissions['index']}");
    }
    
    // CREATE route
    if (in_array('create', $only) && !in_array('create', $except)) {
        Route::get("{$name}/create", [$controller, 'create'])
            ->name("{$name}.create")
            ->middleware("permission:{$permissions['create']}");
    }
    
    // STORE route
    if (in_array('store', $only) && !in_array('store', $except)) {
        Route::post($name, [$controller, 'store'])
            ->name("{$name}.store")
            ->middleware("permission:{$permissions['store']}");
    }
    
    // SHOW route
    if (in_array('show', $only) && !in_array('show', $except)) {
        Route::get("{$name}/{id}", [$controller, 'show'])
            ->name("{$name}.show")
            ->middleware("permission:{$permissions['show']}");
    }
    
    // EDIT route
    if (in_array('edit', $only) && !in_array('edit', $except)) {
        Route::get("{$name}/{id}/edit", [$controller, 'edit'])
            ->name("{$name}.edit")
            ->middleware("permission:{$permissions['edit']}");
    }
    
    // UPDATE route
    if (in_array('update', $only) && !in_array('update', $except)) {
        Route::put("{$name}/{id}", [$controller, 'update'])
            ->name("{$name}.update")
            ->middleware("permission:{$permissions['update']}");
        
        Route::patch("{$name}/{id}", [$controller, 'update'])
            ->middleware("permission:{$permissions['update']}");
    }
    
    // DESTROY route
    if (in_array('destroy', $only) && !in_array('destroy', $except)) {
        Route::delete("{$name}/{id}", [$controller, 'destroy'])
            ->name("{$name}.destroy")
            ->middleware("permission:{$permissions['destroy']}");
    }
    
    // Return nothing since we're not using the fluent API
    return;
}

// Public routes
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Schema-specific login routes
Route::get('/login/{schemaRoute}', [LoginController::class, 'showLoginForm'])->name('login.schema');
Route::post('/login/{schemaRoute}', [LoginController::class, 'login'])->name('login.schema.submit');

// Standard login and registration routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout route (everyone can access)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard with permission
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:access_dashboard');
    
    // Resource routes with automatic permissions
    permissionResource('partners', PartnerController::class);
    permissionResource('invoices', InvoiceController::class);
    permissionResource('transportation_orders', TransportationOrderController::class);
    permissionResource('custom-tables', CustomTableController::class);
    
    // Permission Management Routes
    permissionResource('permissions', PermissionController::class);
    permissionResource('roles', RoleController::class);
    
    // Generate CRUD permissions for a resource
    Route::post('/permissions/generate-crud', [PermissionController::class, 'generateCrud'])
        ->name('permissions.generate-crud')
        ->middleware('permission:create_permissions');
    
    // User Permission Management
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
    
    // Transportation Order PDF export
    Route::get('/transportation_orders/{id}/export-pdf', [TransportationOrderController::class, 'exportPdf'])
        ->name('transportation_orders.exportPdf')
        ->middleware('permission:export_transportation_orders');
    
    // Partner search
    Route::get('/search-partners', [PartnerController::class, 'searchPartners'])
        ->name('search.partners')
        ->middleware('permission:view_partners');
    
    // Invoice PDF routes
    Route::get('/invoices/{id}/preview-pdf', [InvoiceController::class, 'previewPdf'])
        ->name('invoices.previewPdf')
        ->middleware('permission:view_invoices');
    
    Route::get('/invoices/{id}/export-pdf', [InvoiceController::class, 'exportPdf'])
        ->name('invoices.exportPdf')
        ->middleware('permission:export_invoices');
    
    // Navigation management
    Route::post('/updateNavOrder', [NavController::class, 'updateOrder'])
        ->name('update.nav.order')
        ->middleware('permission:manage_navigation');
    
    // Config routes
    Route::middleware('permission:view_configs')->group(function () {
        Route::get('/configs', [ConfigController::class, 'index'])->name('configs.index');
        
        Route::middleware('permission:edit_configs')->group(function () {
            Route::get('/configs/{key}/edit', [ConfigController::class, 'edit'])->name('configs.edit');
            Route::put('/configs/{key}', [ConfigController::class, 'update'])->name('configs.update');
        });
    });

    // Custom Table Data Management
    Route::prefix('data/{tableName}')->name('custom-tables.data.')->group(function () {
        Route::get('/', [CustomTableController::class, 'dataIndex'])
            ->name('index')
            ->middleware('permission:view_custom_table_data');
        
        Route::middleware('permission:create_custom_table_data')->group(function () {
            Route::get('/create', [CustomTableController::class, 'dataCreate'])->name('create');
            Route::post('/', [CustomTableController::class, 'dataStore'])->name('store');
        });
        
        Route::get('/{id}', [CustomTableController::class, 'dataShow'])
            ->name('show')
            ->middleware('permission:view_custom_table_data');
        
        Route::middleware('permission:edit_custom_table_data')->group(function () {
            Route::get('/{id}/edit', [CustomTableController::class, 'dataEdit'])->name('edit');
            Route::put('/{id}', [CustomTableController::class, 'dataUpdate'])->name('update');
        });
        
        Route::delete('/{id}', [CustomTableController::class, 'dataDestroy'])
            ->name('destroy')
            ->middleware('permission:delete_custom_table_data');
    });
});