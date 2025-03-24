<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatePermissionTablesFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the PostgreSQL function for making permission tables in schemas
        DB::unprepared('
            CREATE OR REPLACE FUNCTION public.create_permission_tables_in_schema(schema_name TEXT)
            RETURNS void AS $$
            BEGIN
                -- Create permissions table
                EXECUTE FORMAT(\'
                    CREATE TABLE IF NOT EXISTS %I.permissions (
                        id BIGSERIAL PRIMARY KEY,
                        name VARCHAR(255) UNIQUE NOT NULL,
                        slug VARCHAR(255) UNIQUE NOT NULL,
                        description VARCHAR(255) NULL,
                        type VARCHAR(255) DEFAULT \'\'route\'\',
                        resource VARCHAR(255) NULL,
                        action VARCHAR(255) NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL
                    )
                \', schema_name);
                
                -- Create roles table
                EXECUTE FORMAT(\'
                    CREATE TABLE IF NOT EXISTS %I.roles (
                        id BIGSERIAL PRIMARY KEY,
                        name VARCHAR(255) NOT NULL,
                        slug VARCHAR(255) UNIQUE NOT NULL,
                        description VARCHAR(255) NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL
                    )
                \', schema_name);
                
                -- Create role_permission pivot table
                EXECUTE FORMAT(\'
                    CREATE TABLE IF NOT EXISTS %I.role_permission (
                        id BIGSERIAL PRIMARY KEY,
                        role_id BIGINT NOT NULL,
                        permission_id BIGINT NOT NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        UNIQUE(role_id, permission_id)
                    )
                \', schema_name);
                
                -- Create user_role pivot table
                EXECUTE FORMAT(\'
                    CREATE TABLE IF NOT EXISTS %I.user_role (
                        id BIGSERIAL PRIMARY KEY,
                        user_id BIGINT NOT NULL,
                        role_id BIGINT NOT NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        UNIQUE(user_id, role_id)
                    )
                \', schema_name);
                
                -- Create user_permission pivot table
                EXECUTE FORMAT(\'
                    CREATE TABLE IF NOT EXISTS %I.user_permission (
                        id BIGSERIAL PRIMARY KEY,
                        user_id BIGINT NOT NULL,
                        permission_id BIGINT NOT NULL,
                        granted BOOLEAN NOT NULL DEFAULT TRUE,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        UNIQUE(user_id, permission_id)
                    )
                \', schema_name);
            END;
            $$ LANGUAGE plpgsql;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP FUNCTION IF EXISTS public.create_permission_tables_in_schema(TEXT);');
    }
}