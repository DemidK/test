<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SchemaRoute;
use Illuminate\Support\Facades\View;

class SetSchemaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $schemaName = $request->route('schemaName');

        if (!$schemaName) {
            return $next($request);
        }

        $schemaExists = SchemaRoute::where('schema_name', $schemaName)->exists();

        if ($schemaExists) {
            try {
                DB::statement("SET search_path TO {$schemaName}, public");
                Log::debug("Set search_path to {$schemaName}, public");
                
                session(['current_schema' => $schemaName]);
                View::share('currentSchemaName', $schemaName);
            } catch (\Exception $e) {
                Log::error("Error setting schema search path for {$schemaName}: " . $e->getMessage());
                abort(500, 'Could not connect to the database schema.');
            }
        } else {
            abort(404, 'Tenant not found.');
        }

        return $next($request);
    }
}