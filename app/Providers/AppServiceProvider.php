<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $schemaName = $user->schema_name;

            if ($schemaName && $this->isValidSchema($schemaName)) {
                DB::statement("SET search_path TO $schemaName");
            } else {
                DB::statement('SET search_path TO public');
            }
        } else {
            DB::statement('SET search_path TO public');
        }
    }

    protected function isValidSchema($schemaName)
    {
        return !empty($schemaName);
    }
}
