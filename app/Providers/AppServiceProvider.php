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
        Log::debug('boot');
        if (Auth::check()) {
            Log::debug('check');

            $user = Auth::user();
            $schemaName = $user->schema_name;
            Log::debug($user->schema_name);

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
        Log::debug('isValidSchema');

        return !empty($schemaName);
    }
}
