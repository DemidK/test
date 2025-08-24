<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RestoreTenantConnection
{
    public function handle(Request $request, Closure $next)
    {
        // Восстанавливаем соединение после регенерации сессии
        $schemaName = session('current_schema');
        $tenantConnectionName = session('tenant_connection');

        if ($schemaName && $tenantConnectionName) {
            // Восстанавливаем конфигурацию соединения
            $defaultConnection = DB::getDefaultConnection();
            $tenantConnectionConfig = config("database.connections.{$defaultConnection}");
            $tenantConnectionConfig['schema'] = $schemaName;
            
            Config::set("database.connections.{$tenantConnectionName}", $tenantConnectionConfig);
            
            // Восстанавливаем соединение для аутентификации
            config(['auth.providers.users.connection' => $tenantConnectionName]);
            
            // ВАЖНО: Очищаем кэш соединений
            DB::purge($tenantConnectionName);
            
            Log::info("Restored tenant connection in middleware: {$tenantConnectionName} for schema {$schemaName}");
        }

        return $next($request);
    }
}