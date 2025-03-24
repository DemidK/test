<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SetSchemaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $schemaName = $user->schema_name;

                if ($schemaName && $this->isValidSchema($schemaName)) {
                    // Set search path to user's schema first, then public
                    DB::statement("SET search_path TO {$schemaName}, public");
                    
                    // Log for debugging
                    Log::debug("Set search_path to {$schemaName}, public for user {$user->id}");
                } else {
                    // If no schema or invalid schema, use public only
                    DB::statement('SET search_path TO public');
                    Log::debug("Set search_path to public only (no valid schema for user)");
                }
            } else {
                // For guests, use public schema only
                DB::statement('SET search_path TO public');
                Log::debug("Set search_path to public (guest user)");
            }
        } catch (Exception $e) {
            // Log the error but don't block the request
            Log::error("Error setting schema search path: " . $e->getMessage());
        }

        return $next($request);
    }

    /**
     * Check if a schema name is valid
     *
     * @param string $schemaName
     * @return bool
     */
    protected function isValidSchema($schemaName)
    {
        if (empty($schemaName)) {
            return false;
        }
        
        // Optional: Add additional validation to check if schema exists
        try {
            $exists = DB::selectOne("SELECT EXISTS(SELECT 1 FROM information_schema.schemata WHERE schema_name = ?)", [$schemaName]);
            return $exists && isset($exists->exists) && $exists->exists;
        } catch (Exception $e) {
            Log::error("Error checking schema existence: " . $e->getMessage());
            // If we can't verify, assume it's valid if it's not empty
            return true; 
        }
    }
}