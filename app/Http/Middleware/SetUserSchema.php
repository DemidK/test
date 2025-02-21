<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SetUserSchema
{
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Get the authenticated user
            $user = Auth::user();

            // Get the schema name from the user's record
            $schemaName = $user->schema_name;

            // Validate the schema name (optional but recommended)
            if ($schemaName && $this->isValidSchema($schemaName)) {
                // Set the schema dynamically
                DB::statement("SET search_path TO $schemaName");
            } else {
                // Fallback to a default schema (e.g., 'public')
                DB::statement('SET search_path TO public');
            }
        } else {
            // Guests use the default 'public' schema
            DB::statement('SET search_path TO public');
        }

        return $next($request);
    }

    /**
     * Validate the schema name (optional but recommended).
     *
     * @param string $schemaName
     * @return bool
     */
    protected function isValidSchema($schemaName)
    {
        // Add validation logic here (e.g., check if the schema exists in the database)
        // For simplicity, we'll assume the schema name is valid if it's not empty.
        return !empty($schemaName);
    }
}