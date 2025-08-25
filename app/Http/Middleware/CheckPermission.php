<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\PermissionService; //
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    protected $permissionService;
    
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }
    
    public function handle(Request $request, Closure $next, string $permission = null)
    {
        // Проверяем, авторизован ли пользователь
        // Добавляем логирование, чтобы отследить причину редиректов
        if (!Auth::check()) {
            \Illuminate\Support\Facades\Log::info('--- CheckPermission Middleware: Auth check FAILED ---', [
                'url' => $request->fullUrl(),
                'session_id' => session()->getId(),
                'message' => 'User is not authenticated. Redirecting to login.'
            ]);

            // Если нет, перенаправляем на нужную страницу входа
            $schemaName = $request->route('schemaName');
            $routeName = $schemaName ? 'login.schema' : 'login';
            $routeParams = $schemaName ? ['schemaName' => $schemaName] : [];
            
            return redirect()->route($routeName, $routeParams)->with('error', 'Please log in to access this page');
        }

        // Дашборд доступен всем авторизованным пользователям,
        // поэтому для него не требуется специальная проверка прав.
        if ($request->route()->named('dashboard')) {
            return $next($request);
        }

        // Если право не передано в middleware, используем имя текущего маршрута
        $permissionToCheck = $permission ?: $request->route()->getName();

        if (!$permissionToCheck) {
            abort(403, 'This route is not named and cannot be checked for permissions.');
        }

        // Теперь вся проверка прав, включая суперпользователя, происходит через Gate Laravel,
        // который мы настроили в AuthServiceProvider. Это централизует логику.
        // Auth::user()->can() использует настроенный Gate.
        // Если у пользователя НЕТ прав, и он авторизован, перенаправляем на дашборд с сообщением.
        if (Auth::user()->cannot($permissionToCheck)) {
            // Определяем параметры для редиректа на дашборд, сохраняя schemaName
            $schemaName = $request->route('schemaName');
            $dashboardRouteParams = $schemaName ? ['schemaName' => $schemaName] : [];

            // Перенаправляем на дашборд с информационным сообщением
            return redirect()->route('dashboard', $dashboardRouteParams)
                             ->with('warning', 'У вас нет прав доступа к этой странице.');
        }
        
        return $next($request);
    }
}