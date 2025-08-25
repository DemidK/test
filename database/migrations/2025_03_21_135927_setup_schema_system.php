<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Create schema_routes table
        Schema::create('schema_routes', function (Blueprint $table) {
            $table->id();
            $table->string('schema_name')->unique();
            $table->string('route_name')->unique();
            $table->bigInteger('user_id')->unsigned();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
        });
        
        // 2. Add PostgreSQL username column to user table (but not password)
        Schema::table('users', function (Blueprint $table) {
            $table->string('pg_username')->nullable();
        });
        
        // 3. Create PostgreSQL registration role
        DB::unprepared("
            DO $$
            BEGIN
                IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'registration_service') THEN
                    CREATE ROLE registration_service WITH LOGIN PASSWORD 'secure_password';
                END IF;
            END
            $$;
            
            -- Grant necessary permissions to registration_service
            GRANT CREATE ON DATABASE " . env('DB_DATABASE') . " TO registration_service;
            GRANT USAGE, CREATE ON SCHEMA public TO registration_service;
            
            -- Ensure the application user can create roles (only run if you have permission)
            -- Uncomment this line if your database user has SUPERUSER privileges
            -- ALTER USER " . env('DB_USERNAME') . " WITH CREATEROLE;
        ");
    }

    public function down()
    {
        // Drop the schema_routes table
        Schema::dropIfExists('schema_routes');
        
        // Remove pg_username column from user table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pg_username');
        });
        
        // Remove the PostgreSQL role
        // Note: This might fail if there are dependencies
        DB::unprepared("
            DROP ROLE IF EXISTS registration_service;
        ");
    }
};