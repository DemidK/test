<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SchemaService
{
    /**
     * Create a new user schema with deterministic credentials
     */
    public function createUserSchema(string $schemaName, int $userId)
    {
        try {
            // Generate PostgreSQL username from schema name
            // Using "usr_" prefix instead of "pg_" to avoid reserved name error
            $pgUsername = 'usr_' . preg_replace('/[^a-z0-9_]/', '', $schemaName);
            
            // Generate an initial password for schema creation only
            // This password will be regenerated during login
            $initialPassword = Str::random(32);
            
            // Start transaction for schema creation
            DB::beginTransaction();
            
            // Create PostgreSQL role for the user with limited permissions
            DB::unprepared("
                CREATE ROLE {$pgUsername} WITH LOGIN PASSWORD '{$initialPassword}';
            ");
            
            // Create new schema
            DB::statement("CREATE SCHEMA IF NOT EXISTS {$schemaName}");
            
            // Create tables in the new schema
            $this->createSchemaTables($schemaName);
            
            // Grant permissions to the user on their schema only
            DB::unprepared("
                GRANT ALL PRIVILEGES ON SCHEMA {$schemaName} TO {$pgUsername};
                GRANT USAGE ON SCHEMA public TO {$pgUsername};
                GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA {$schemaName} TO {$pgUsername};
                GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA {$schemaName} TO {$pgUsername};
                ALTER DEFAULT PRIVILEGES IN SCHEMA {$schemaName} 
                GRANT ALL PRIVILEGES ON TABLES TO {$pgUsername};
                ALTER DEFAULT PRIVILEGES IN SCHEMA {$schemaName} 
                GRANT ALL PRIVILEGES ON SEQUENCES TO {$pgUsername};
            ");
            
            // Revoke permissions from registration service on this schema
            DB::unprepared("REVOKE ALL ON SCHEMA {$schemaName} FROM CURRENT_USER");
            
            DB::commit();
            
            return $pgUsername;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating schema: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Generate a secure, deterministic password based on user credentials
     */
    public function generateSecurePassword($userId, $userPassword, $schemaName)
    {
        // Get server-side secret key from environment
        $serverSecret = env('SCHEMA_SECRET_KEY', 'default_secret_change_this');
        
        // Generate a deterministic but secure password
        $passwordBase = $userId . $userPassword . $schemaName . $serverSecret;
        
        // Create a strong hash and then convert to a usable password
        $passwordHash = hash('sha256', $passwordBase);
        
        // Convert to a strong alphanumeric password (32 chars)
        return substr(preg_replace('/[^a-zA-Z0-9]/', '', base64_encode($passwordHash)), 0, 32);
    }
    
    /**
     * Reset the PostgreSQL password for a schema
     */
    public function resetSchemaPassword($schemaName)
    {
        $pgUsername = 'usr_' . preg_replace('/[^a-z0-9_]/', '', $schemaName);
        
        // Generate a temporary random password
        $tempPassword = Str::random(32);
        
        // Reset the password in PostgreSQL
        DB::unprepared("ALTER ROLE {$pgUsername} WITH PASSWORD '{$tempPassword}'");
        
        return $tempPassword;
    }
    
    /**
     * Create PostgreSQL schema tables
     */
    private function createSchemaTables(string $schemaName)
    {
        try {
            // Log for debugging
            Log::info("Creating tables in schema: {$schemaName}");
            
            // Create users table in new schema
            DB::statement("
                CREATE TABLE {$schemaName}.user (
                    id BIGSERIAL PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    email_verified_at TIMESTAMP NULL,
                    password VARCHAR(255) NOT NULL,
                    remember_token VARCHAR(100) NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ");
            Log::info("Created user table in schema: {$schemaName}");
    
            // Create partners table in new schema
            DB::statement("
                CREATE TABLE {$schemaName}.partners (
                    id BIGSERIAL PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    identification_number VARCHAR(255) NOT NULL,
                    json_data JSONB NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ");
            Log::info("Created partners table in schema: {$schemaName}");
    
            // Create nav_links table in new schema
            DB::statement("
                CREATE TABLE {$schemaName}.nav_links (
                    id BIGSERIAL PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    url VARCHAR(255) NOT NULL,
                    position INTEGER NOT NULL DEFAULT 0,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ");
            Log::info("Created nav_links table in schema: {$schemaName}");
    
            // Copy nav_links data from public schema
            DB::statement("
                INSERT INTO {$schemaName}.nav_links (name, url, position, created_at, updated_at)
                SELECT name, url, position, created_at, updated_at
                FROM public.nav_links
            ");
            Log::info("Copied nav_links data to schema: {$schemaName}");
    
            // Create invoices table
            $this->createInvoicesTable($schemaName);
            
            // Create transportation_orders table 
            $this->createTransportationOrdersTable($schemaName);
            
            // Create configs table
            $this->createConfigsTable($schemaName);
            
            Log::info("Successfully created all tables in schema: {$schemaName}");
    
            // Create permission tables in the new schema - WITH ERROR HANDLING
            try {
                // Check if the function exists first
                $functionExists = DB::selectOne("
                    SELECT EXISTS (
                        SELECT 1 
                        FROM pg_proc 
                        WHERE proname = 'create_permission_tables_in_schema'
                    ) AS exists
                ");
                
                if ($functionExists && $functionExists->exists) {
                    // Use the PostgreSQL function if it exists
                    DB::statement("SELECT public.create_permission_tables_in_schema('{$schemaName}')");
                    Log::info("Created permission tables in schema using function: {$schemaName}");
                } else {
                    // Fallback to creating the tables directly if the function doesn't exist
                    Log::warning("Function create_permission_tables_in_schema not found, creating tables directly");
                    $this->createPermissionTablesDirectly($schemaName);
                }
            } catch (\Exception $e) {
                Log::error("Error using permission tables function: " . $e->getMessage());
                // Fallback to creating the tables directly
                try {
                    $this->createPermissionTablesDirectly($schemaName);
                } catch (\Exception $innerException) {
                    Log::error("Fallback creation of permission tables failed: " . $innerException->getMessage());
                    // Continue with other operations, don't throw here
                }
            }
    
            // Create default admin role in the schema
            DB::statement("
                INSERT INTO {$schemaName}.roles (name, slug, description, created_at, updated_at)
                VALUES (
                    'Administrator',
                    'administrator',
                    'Full access to all features',
                    NOW(),
                    NOW()
                )
            ");
    
            // Create basic permissions in the schema
            $resources = ['users', 'roles', 'permissions', 'invoices', 'partners'];
            $actions = ['view', 'create', 'edit', 'delete'];
    
            foreach ($resources as $resource) {
                foreach ($actions as $action) {
                    $name = ucfirst($action) . ' ' . ucfirst($resource);
                    $slug = strtolower($action . '_' . $resource);
                    
                    DB::statement("
                        INSERT INTO {$schemaName}.permissions (name, slug, description, type, resource, action, created_at, updated_at)
                        VALUES (
                            '{$name}',
                            '{$slug}',
                            'Can {$action} {$resource}',
                            'route',
                            '{$resource}',
                            '{$action}',
                            NOW(),
                            NOW()
                        )
                    ");
                }
            }
    
            // Get the admin role ID
            $adminRoleId = DB::selectOne("SELECT id FROM {$schemaName}.roles WHERE slug = 'administrator'")->id;
    
            // Get all permission IDs
            $permissionIds = DB::select("SELECT id FROM {$schemaName}.permissions");
    
            // Assign all permissions to the admin role
            foreach ($permissionIds as $permission) {
                DB::statement("
                    INSERT INTO {$schemaName}.role_permission (role_id, permission_id, created_at, updated_at)
                    VALUES (
                        {$adminRoleId},
                        {$permission->id},
                        NOW(),
                        NOW()
                    )
                ");
            }
    
            // Assign admin role to the first user in the schema
            DB::statement("
                INSERT INTO {$schemaName}.user_role (user_id, role_id, created_at, updated_at)
                VALUES (
                    1,
                    {$adminRoleId},
                    NOW(),
                    NOW()
                )
            ");
    
            Log::info("Created default roles and permissions in schema: {$schemaName}");
    
            // Create superuser role in the schema - this bypasses all permission checks
            DB::statement("
            INSERT INTO {$schemaName}.roles (name, slug, description, created_at, updated_at)
            VALUES (
                'Superuser',
                'superuser',
                'Schema owner with full access to all features regardless of permissions',
                NOW(),
                NOW()
            )
            ");
    
            // Get the superuser role ID
            $superuserRoleId = DB::selectOne("SELECT id FROM {$schemaName}.roles WHERE slug = 'superuser'")->id;
    
            // Assign superuser role to the schema owner (the first user in the schema)
            DB::statement("
            INSERT INTO {$schemaName}.user_role (user_id, role_id, created_at, updated_at)
            VALUES (
                1,  -- The first user is typically the schema owner
                {$superuserRoleId},
                NOW(),
                NOW()
            )
            ");
    
            Log::info("Created superuser role and assigned it to schema owner in schema: {$schemaName}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Error creating tables in schema {$schemaName}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create permission tables directly without using the PostgreSQL function
     */
    private function createPermissionTablesDirectly($schemaName)
    {
        // Create permissions table
        DB::statement("
            CREATE TABLE IF NOT EXISTS {$schemaName}.permissions (
                id BIGSERIAL PRIMARY KEY,
                name VARCHAR(255) UNIQUE NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                description VARCHAR(255) NULL,
                type VARCHAR(255) DEFAULT 'route',
                resource VARCHAR(255) NULL,
                action VARCHAR(255) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )
        ");
        
        // Create roles table
        DB::statement("
            CREATE TABLE IF NOT EXISTS {$schemaName}.roles (
                id BIGSERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                description VARCHAR(255) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )
        ");
        
        // Create role_permission pivot table
        DB::statement("
            CREATE TABLE IF NOT EXISTS {$schemaName}.role_permission (
                id BIGSERIAL PRIMARY KEY,
                role_id BIGINT NOT NULL,
                permission_id BIGINT NOT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                UNIQUE(role_id, permission_id)
            )
        ");
        
        // Create user_role pivot table
        DB::statement("
            CREATE TABLE IF NOT EXISTS {$schemaName}.user_role (
                id BIGSERIAL PRIMARY KEY,
                user_id BIGINT NOT NULL,
                role_id BIGINT NOT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                UNIQUE(user_id, role_id)
            )
        ");
        
        // Create user_permission pivot table
        DB::statement("
            CREATE TABLE IF NOT EXISTS {$schemaName}.user_permission (
                id BIGSERIAL PRIMARY KEY,
                user_id BIGINT NOT NULL,
                permission_id BIGINT NOT NULL,
                granted BOOLEAN NOT NULL DEFAULT TRUE,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                UNIQUE(user_id, permission_id)
            )
        ");
        
        Log::info("Created permission tables directly in schema: {$schemaName}");
    }
    
    /**
     * Create invoices table in schema
     */
    private function createInvoicesTable(string $schemaName) 
    {
        // First create the sequence if it doesn't exist
        DB::statement("
            CREATE SEQUENCE IF NOT EXISTS {$schemaName}.invoices_id_seq
            INCREMENT 1
            START 1
            MINVALUE 1
            MAXVALUE 9223372036854775807
            CACHE 1
        ");
        
        // Create invoices table
        DB::statement("
            CREATE TABLE {$schemaName}.invoices (
                id bigint PRIMARY KEY DEFAULT nextval('{$schemaName}.invoices_id_seq'::regclass),
                invoice_number character varying(255) NOT NULL,
                invoice_date date NOT NULL,
                partner_name character varying(255) NOT NULL,
                partner_email character varying(255),
                items jsonb NOT NULL,
                total_amount numeric(10,2) NOT NULL,
                created_at timestamp without time zone,
                updated_at timestamp without time zone,
                partner_id bigint,
                partner_vat character varying(255),
                partner_address text,
                partner_post_address text,
                updater character varying(255),
                total_vat numeric(10,2) NOT NULL DEFAULT 0,
                total_wo_vat numeric(10,2) NOT NULL DEFAULT 0
            )
        ");
        
        Log::info("Created invoices table in schema: {$schemaName}");
    }
    
    /**
     * Create transportation_orders table in schema
     */
    private function createTransportationOrdersTable(string $schemaName)
    {
        DB::statement("
            CREATE TABLE {$schemaName}.transportation_orders (
                id BIGSERIAL PRIMARY KEY,
                carrier_name VARCHAR(255),
                reg_number VARCHAR(255),
                address VARCHAR(255),
                is_our_vehicle BOOLEAN,
                vehicle_number VARCHAR(255),
                vehicle_brand VARCHAR(255),
                vehicle_type VARCHAR(255),
                driver_name VARCHAR(255),
                cargo_type VARCHAR(255),
                cargo_description TEXT,
                max_tonnage VARCHAR(255),
                ldm VARCHAR(255),
                pallet_count INTEGER,
                volume_m3 VARCHAR(255),
                load_address VARCHAR(255),
                load_address_info TEXT,
                load_datetime TIMESTAMP WITHOUT TIME ZONE,
                adr_level INTEGER,
                unload_address VARCHAR(255),
                unload_address_info TEXT,
                unload_datetime TIMESTAMP WITHOUT TIME ZONE,
                freight_amount NUMERIC(10,2),
                vat_status VARCHAR(255),
                currency VARCHAR(255),
                payment_term_days INTEGER,
                penalty_amount VARCHAR(255),
                documents_required TEXT,
                special_conditions TEXT,
                order_number VARCHAR(255),
                partner_id BIGINT,
                created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        Log::info("Created transportation_orders table in schema: {$schemaName}");
    }
    
    /**
     * Create configs table in schema
     */
    private function createConfigsTable(string $schemaName)
    {
        DB::statement("
            CREATE TABLE {$schemaName}.configs (
                id BIGSERIAL PRIMARY KEY,
                route VARCHAR(255) UNIQUE NOT NULL,
                data JSONB NOT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )
        ");
        
        // Copy config data from ConfigSeeder
        DB::statement("
            INSERT INTO {$schemaName}.configs (route, data, created_at, updated_at)
            VALUES (
                'partners_create',
                " . DB::getPdo()->quote(json_encode([
                    'default_inpupts' => [
                        [
                            'name' => 'Contact Information',
                            'background_color' => 'bg-gray-50',
                            'fields' => [
                                ['key' => 'Phone', 'value' => ''],
                                ['key' => 'Email', 'value' => ''],
                                ['key' => 'Address', 'value' => '']
                            ]
                        ]
                    ]
                ])) . ",
                NOW(),
                NOW()
            )
        ");
        
        Log::info("Created configs table in schema: {$schemaName}");
    }
}