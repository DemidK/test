<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class SetSchemaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $schemaName = $request->route('schemaName');

        if ($schemaName) {
            $schemaExists = DB::selectOne("SELECT 1 FROM pg_namespace WHERE nspname = ?", [$schemaName]);

            if ($schemaExists) {
                $tenantConnectionName = 'pgsql_tenant';
                $defaultConnection = config('database.default');
                
                $tenantConnectionConfig = config("database.connections.{$defaultConnection}");
                $tenantConnectionConfig['schema'] = $schemaName;
                
                Config::set("database.connections.{$tenantConnectionName}", $tenantConnectionConfig);
                
                // Устанавливаем соединение ПО УМОЛЧАНИЮ для всего приложения
                DB::setDefaultConnection($tenantConnectionName);

                URL::defaults(['schemaName' => $schemaName]);
                $request->session()->put('current_schema', $schemaName);
            } else {
                abort(404, 'Tenant not found.');
            }
        }

        return $next($request);
    }
}