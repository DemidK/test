<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class SchemaService
{
    /**
     * Создает новую схему для пользователя.
     * Логика миграций и сидов вынесена в RegisterController,
     * чтобы гарантировать правильный контекст выполнения.
     */
    public function createUserSchema(string $schemaName): void
    {
        // Этот метод теперь выполняет только одну задачу - создание схемы.
        // Транзакции, миграции и сиды управляются из вызывающего кода (RegisterController).
        try {
            DB::statement("CREATE SCHEMA IF NOT EXISTS \"{$schemaName}\"");
            Log::info("Schema '{$schemaName}' created successfully by SchemaService.");
        } catch (\Exception $e) {
            Log::error("SchemaService failed to create schema '{$schemaName}': " . $e->getMessage());
            // Перебрасываем исключение, чтобы внешняя транзакция откатилась.
            throw $e;
        }
    }
}