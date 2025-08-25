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

        // Если право не передано в middleware, используем имя текущего маршрута
        $permissionToCheck = $permission ?: $request->route()->getName();

        if (!$permissionToCheck) {
            abort(403, 'This route is not named and cannot be checked for permissions.');
        }

        // Теперь вся проверка прав, включая суперпользователя, происходит через Gate Laravel,
        // который мы настроили в AuthServiceProvider. Это централизует логику.
        // Auth::user()->can() использует настроенный Gate.
        if (Auth::user()->cannot($permissionToCheck)) {
            // Если у пользователя НЕТ прав, показываем ошибку 403. Это разрывает цикл редиректов.
            // Вместо abort(403), который может вызывать проблемы с поиском шаблонов ошибок,
            // мы возвращаем ответ с нашим собственным шаблоном напрямую.
            // Это гарантирует, что будет использован правильный макет ('layouts.app').
            // Мы также передаем объект исключения, чтобы сообщение отобразилось в шаблоне.
            return response()->view('errors.403', [
                'exception' => new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'You do not have permission to perform this action.')
            ], 403);
        }
        
        return $next($request);
    }
}