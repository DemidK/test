<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                \Illuminate\Support\Facades\Log::info('--- RedirectIfAuthenticated Middleware: Auth check PASSED ---', [
                    'url' => $request->fullUrl(),
                    'session_id' => session()->getId(),
                    'message' => 'User is already authenticated. Redirecting away from guest page.'
                ]);

                $currentSchema = session('current_schema');

                // Если пользователь на субдомене - редиректим на dashboard субдомена
                if ($currentSchema) {
                    return redirect()->route('dashboard', ['schemaName' => $currentSchema]);
                }
                
                // Если пользователь на основном домене - редиректим на HOME
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}