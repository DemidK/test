<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetUserSchema
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->schema_name) {
            // Set schema for this request
            DB::statement('SET search_path TO ' . Auth::user()->schema_name . ', public');
        }

        return $next($request);
    }
}