<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Пользователь аутентифицирован.
                $user = Auth::guard($guard)->user();
                $currentSchema = $request->session()->get('current_schema');

                // Сценарий 1: Пользователь на субдомене.
                if ($currentSchema) {
                    // Если схема пользователя НЕ совпадает со схемой субдомена...
                    if ($user->schema_name !== $currentSchema) {
                        // ...значит, он залогинен в другом клиенте. "Разлогиниваем" его.
                        Auth::guard($guard)->logout();
                        // Прерываем дальнейшие проверки и просто пропускаем запрос дальше,
                        // чтобы пользователь увидел форму входа текущего субдомена.
                        return $next($request); 
                    }

                    // Если же схема совпадает, значит, он уже вошел в этот клиент.
                    // Перенаправляем на дашборд этого клиента.
                    return redirect()->route('dashboard', ['schemaName' => $currentSchema]);
                } 
                
                // Сценарий 2: Пользователь на основном домене (не на субдомене).
                // Используем стандартное перенаправление.
                return redirect(RouteServiceProvider::HOME);
            }
        }

        // Если пользователь не аутентифицирован (или был только что разлогинен), пропускаем запрос.
        return $next($request);
    }
}