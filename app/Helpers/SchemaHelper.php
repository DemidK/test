<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchemaHelper
{
    /**
     * Add this to any controller to debug schema issues
     */
    public static function debugSchemaInfo()
    {
        try {
            // Log the current search path
            $result = DB::select("SHOW search_path");
            Log::info("Current search_path: " . $result[0]->search_path);
            
            // Log the current schemas
            $schemas = DB::select("SELECT nspname FROM pg_catalog.pg_namespace");
            $schemaNames = collect($schemas)->pluck('nspname')->toArray();
            Log::info("Available schemas: " . implode(', ', $schemaNames));
            
            // Log the tables in the current search path
            $tables = DB::select("
                SELECT table_schema, table_name 
                FROM information_schema.tables 
                WHERE table_schema = ANY(current_schemas(false))
            ");
            
            foreach ($tables as $table) {
                Log::info("Table: {$table->table_schema}.{$table->table_name}");
            }
            
            // Log current user
            $user = DB::select("SELECT current_user");
            Log::info("Current PostgreSQL user: " . $user[0]->current_user);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Error in debugSchemaInfo: " . $e->getMessage());
            return false;
        }
    }
}