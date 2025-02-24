<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SchemaService
{
    public function createUserSchema(string $schemaName)
    {
        // Create new schema
        DB::statement("CREATE SCHEMA IF NOT EXISTS {$schemaName}");
        
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

        // Create partners table in new schema
        DB::statement("
            CREATE TABLE {$schemaName}.partners (
                id BIGSERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                identification_number VARCHAR(255) NOT NULL,
                json_data JSON NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )
        ");

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

        // Copy nav_links data from public schema
        DB::statement("
            INSERT INTO {$schemaName}.nav_links (name, url, position, created_at, updated_at)
            SELECT name, url, position, created_at, updated_at
            FROM public.nav_links
        ");

        DB::statement("
            CREATE TABLE {$schemaName}.invoices (
                id bigint PRIMARY KEY DEFAULT nextval('invoices_id_seq'::regclass),
                invoice_number character varying(255) NOT NULL,
                invoice_date date NOT NULL,
                partner_name character varying(255) NOT NULL,
                partner_email character varying(255),
                items json NOT NULL,
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
            );
        ");

        // Create configs table in new schema
        DB::statement("
        CREATE TABLE {$schemaName}.configs (
            id BIGSERIAL PRIMARY KEY,
            key VARCHAR(255) UNIQUE NOT NULL,
            value TEXT NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )
        ");

        // Copy config data from ConfigSeeder
        DB::statement("
        INSERT INTO {$schemaName}.configs (key, value, created_at, updated_at)
        VALUES (
            'partner_data_objects',
            " . DB::getPdo()->quote(json_encode([
                'partner_create' => [
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
    }
}