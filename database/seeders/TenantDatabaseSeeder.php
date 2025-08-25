<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Log::info('Running TenantDatabaseSeeder...');

        // 1. Создаем роли
        Role::updateOrCreate(
            ['slug' => 'superuser'],
            [
                'name' => 'Superuser',
                'description' => 'Schema owner with full access',
            ]
        );
        
        Role::updateOrCreate(
            ['slug' => 'user'],
            [
                'name' => 'User',
                'description' => 'Default role for new users',
            ]
        );
        Log::info('Roles created or verified.');

        Log::info('TenantDatabaseSeeder completed successfully.');
    }
}