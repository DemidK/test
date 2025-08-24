<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }
        
        // --- ДИАГНСТИЧЕСКИЙ ЛОГ ---
        Log::info('--- AUTH MIDDLEWARE: REDIRECTING TO LOGIN ---');
        Log::info('Reason: Auth::check() failed.');
        Log::info('Default DB Connection on redirect: ' . DB::getDefaultConnection());
        // --- КОНЕЦ ЛОГА ---
        
        // Проверяем, есть ли в текущем маршруте параметр schemaName
        if ($schemaName = $request->route('schemaName')) {
            // Если есть, значит мы на субдомене. Перенаправляем на логин этого субдомена.
            return route('login.schema', ['schemaName' => $schemaName]);
        }
        
        // Если параметра нет, значит мы на основном домене. Перенаправляем на главный логин.
        return route('login');
    }
}