<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePermissionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create permissions table in public schema
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('type')->default('route'); // route, button, etc.
            $table->string('resource')->nullable(); // invoices, users, etc.
            $table->string('action')->nullable(); // view, edit, delete, etc.
            $table->timestamps();
        });

        // Create roles table in public schema
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Create role_permission pivot table in public schema
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['role_id', 'permission_id']);
        });

        // Create user_role pivot table in public schema
        Schema::create('user_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('user')->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_id', 'role_id']);
        });

        // Create user_permission pivot table in public schema
        Schema::create('user_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('user')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->boolean('granted')->default(true); // true = granted, false = denied (override)
            $table->timestamps();
            
            $table->unique(['user_id', 'permission_id']);
        });

        // Create function to add these tables to each new schema
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
        
        // Add this function to SchemaService for creating permissions tables in new schemas
        $this->addToSchemaService();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_permission');
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        
        DB::unprepared('DROP FUNCTION IF EXISTS public.create_permission_tables_in_schema(TEXT)');
    }
    
    /**
     * Add the permission tables creation to the SchemaService
     */
    private function addToSchemaService()
    {
        // This would normally be done by modifying the SchemaService class
        // But for migration purposes, we'll just add a comment as a reminder
        
        // NOTE: You need to update the SchemaService.php file to include this:
        // In the createSchemaTables method, add:
        //
        // // Create permission tables in the schema
        // DB::statement("SELECT public.create_permission_tables_in_schema('{$schemaName}')");
    }
}