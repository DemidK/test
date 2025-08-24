<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class SchemaService
{
    /**
     * Создает новую схему для пользователя, запускает миграции и сидеры.
     */
    public function createUserSchema(string $schemaName): void
    {
        try {
            DB::beginTransaction();

            Log::info("Starting creation for schema: {$schemaName}");

            // 1. Создаем саму схему
            DB::statement("CREATE SCHEMA IF NOT EXISTS \"{$schemaName}\"");
            Log::info("Schema '{$schemaName}' created.");

            // 2. Временно устанавливаем соединение с новой схемой
            $this->setTenantConnection($schemaName);
            Log::info("Connection switched to '{$schemaName}'.");

            // 3. Программно запускаем миграции в новой схеме
            // Это гарантирует, что структура БД клиента всегда актуальна
            Artisan::call('migrate', [
                '--database' => 'pgsql_tenant', // Имя нашего временного соединения
                '--path' => 'database/migrations', // Путь к вашим миграциям
                '--force' => true, // Обязательно для неинтерактивного режима
            ]);
            Log::info("Migrations run for schema '{$schemaName}'. Output: " . Artisan::output());

            // 4. Программно запускаем сидер для наполнения схемы базовыми данными
            Artisan::call('db:seed', [
                '--database' => 'pgsql_tenant',
                '--class' => 'TenantDatabaseSeeder', // Указываем наш новый сидер
                '--force' => true,
            ]);
            Log::info("TenantDatabaseSeeder run for schema '{$schemaName}'. Output: " . Artisan::output());

            DB::commit();
            Log::info("Schema '{$schemaName}' successfully created and seeded.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create schema '{$schemaName}': " . $e->getMessage());
            throw $e;
        } finally {
            // 5. Всегда восстанавливаем соединение по умолчанию
            $this->restoreDefaultConnection();
            Log::info("Database connection restored to default.");
        }
    }

    /**
     * Временно устанавливает соединение с базой данных клиента.
     */
    protected function setTenantConnection(string $schemaName): void
    {
        $tenantConnectionName = 'pgsql_tenant';
        $defaultConnection = config('database.default');
        
        $config = config("database.connections.{$defaultConnection}");
        $config['schema'] = $schemaName;
        
        config(["database.connections.{$tenantConnectionName}" => $config]);
    }
    
    /**
     * Восстанавливает стандартное соединение с базой данных.
     */
    protected function restoreDefaultConnection(): void
    {
        DB::setDefaultConnection(config('database.default'));
    }
}