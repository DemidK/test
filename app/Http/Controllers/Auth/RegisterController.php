<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SchemaRoute;
use App\Models\User;
use App\Services\SchemaService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\TenantDatabaseSeeder;

class RegisterController extends Controller
{
    protected $schemaService;

    public function __construct(SchemaService $schemaService)
    {
        $this->middleware('guest');
        $this->schemaService = $schemaService;
    }

    /**
     * Показывает форму регистрации (как для клиента, так и для пользователя).
     */
    public function showRegistrationForm(Request $request)
    {
        $isTenantRegistration = !$request->route('schemaName');

        Log::info('RegisterController: Showing registration form.', [
            'is_tenant_registration' => $isTenantRegistration,
            'url' => $request->fullUrl(),
        ]);
        
        return view('auth.register', [
            'isTenantRegistration' => $isTenantRegistration
        ]);
    }

    /**
     * Регистрирует нового КЛИЕНТА (создает схему, первого пользователя и т.д.).
     * Вызывается с основного домена.
     */
    public function registerTenant(Request $request)
    {
        Log::info('RegisterController: Attempting to register a new tenant.', [
            'input' => $request->except('password', 'password_confirmation'),
            'ip' => $request->ip(),
        ]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'schema_name' => ['required', 'string', 'max:63', 'alpha_dash', 'unique:schema_routes,route_name'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Log::info('RegisterController: Tenant registration validation passed.');

        $schemaName = Str::snake($request->schema_name);

        // Сохраняем исходные настройки для восстановления
        $originalDefaultConnection = config('database.default');
        $originalMigrationsTable = config('database.migrations');

        // Сначала выполняем операции, которые не требуют транзакции
        try {
            // 1. Создаем схему в БД (без транзакции)
            Log::info('RegisterController: Calling SchemaService to create schema.', ['schema_name' => $schemaName]);
            $this->schemaService->createUserSchema($schemaName);
            Log::info('RegisterController: SchemaService finished.', ['schema_name' => $schemaName]);

            // 2. Запускаем миграции в контексте новой схемы (без транзакции)
            Log::info('RegisterController: Running migrations for schema.', ['schema_name' => $schemaName]);
            $this->runMigrationsForSchema($schemaName);
            
            // 3. Запускаем сидеры в контексте новой схемы (без транзакции)
            Log::info('RegisterController: Running seeders for schema.', ['schema_name' => $schemaName]);
            $this->runSeedersForSchema($schemaName);

        } catch (\Exception $e) {
            Log::error("Error during schema setup: " . $e->getMessage());
            return back()->with('error', 'Schema setup failed. Please try again.')->withInput();
        }

        // Теперь выполняем операции с данными в транзакции
        DB::beginTransaction();
        try {
            // 4. Создаем пользователя-владельца в ГЛАВНОЙ таблице (public.users)
            Log::info('RegisterController: Creating owner user in public schema.', ['email' => $request->email]);
            $owner = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'schema_name' => $schemaName,
            ]);

            // 5. Создаем запись о маршруте для субдомена
            Log::info('RegisterController: Creating schema route.', ['schema_name' => $schemaName]);
            SchemaRoute::create([
                'schema_name' => $schemaName,
                'route_name' => $schemaName,
                'user_id' => $owner->id,
                'is_active' => true
            ]);

            // 6. Создаем первого пользователя ВНУТРИ схемы клиента
            Log::info('RegisterController: Creating user inside tenant schema.', ['email' => $request->email]);
            $tenantUser = $this->createTenantUser($schemaName, $request);

            // 7. Назначаем роль superuser
            Log::info('RegisterController: Assigning "superuser" role to tenant user.', ['user_id' => $tenantUser->id]);
            $tenantUser->assignRoles('superuser');

            Log::info('RegisterController: Committing transaction.');
            DB::commit();

            // 8. Авторизуем пользователя
            Log::info('RegisterController: Logging in the new tenant user.', ['user_id' => $tenantUser->id]);
            Auth::login($tenantUser);
            
            Log::info('RegisterController: Setting current_schema in session.', ['schema_name' => $schemaName]);
            session(['current_schema' => $schemaName]);

            Log::info('RegisterController: Tenant registration successful. Redirecting to dashboard.', [
                'redirect_url' => route('dashboard', ['schemaName' => $schemaName])
            ]);
            return redirect()->route('dashboard', ['schemaName' => $schemaName]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Tenant registration error: " . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            
            // Если произошла ошибка в транзакции, пытаемся удалить созданную схему
            try {
                Log::info('RegisterController: Attempting to cleanup schema after error.', ['schema_name' => $schemaName]);
                DB::statement("DROP SCHEMA IF EXISTS \"{$schemaName}\" CASCADE");
                Log::info('RegisterController: Schema cleaned up successfully.', ['schema_name' => $schemaName]);
            } catch (\Exception $cleanupException) {
                Log::error('RegisterController: Failed to cleanup schema: ' . $cleanupException->getMessage());
            }
            
            return back()->with('error', 'Registration failed. Please try again or check the logs.')->withInput();
        } finally {
            // Восстанавливаем исходные настройки
            config(['database.migrations' => $originalMigrationsTable]);
            Log::info('RegisterController: Restored migrations table.', ['table' => config('database.migrations')]);
        }
    }

    /**
     * Выполняет миграции для конкретной схемы
     */
    private function runMigrationsForSchema($schemaName)
    {
        // Сохраняем текущий search_path и настройки миграций
        $originalMigrationsTable = config('database.migrations');
        
        // Проверяем, что схема существует
        $schemaExists = DB::selectOne("SELECT 1 FROM information_schema.schemata WHERE schema_name = ?", [$schemaName]);
        if (!$schemaExists) {
            throw new \Exception("Schema '{$schemaName}' does not exist before running migrations");
        }
        Log::info('RegisterController: Schema exists, proceeding with migrations.', ['schema_name' => $schemaName]);
        
        // Устанавливаем search_path для текущего соединения
        DB::statement("SET search_path TO \"{$schemaName}\", public");
        Log::info('RegisterController: Set search_path for migrations.', ['schema_name' => $schemaName]);
        
        // Настраиваем таблицу миграций для этой схемы
        Config::set('database.migrations', $schemaName . '.migrations');
        
        try {
            // Получаем мигратор
            /** @var \Illuminate\Database\Migrations\Migrator $migrator */
            $migrator = app('migrator');
            $migrator->setConnection(config('database.default'));
            
            $migrationPath = database_path('migrations');
            
            // Создаем таблицу миграций если её нет
            if (!$migrator->repositoryExists()) {
                Log::info('RegisterController: Creating migrations repository.', ['schema_name' => $schemaName]);
                $migrator->getRepository()->createRepository();
            }
            
            // Получаем список файлов миграций
            $migrationFiles = $migrator->getMigrationFiles($migrationPath);
            Log::info('RegisterController: Found migration files.', [
                'schema_name' => $schemaName,
                'files_count' => count($migrationFiles),
                'files' => array_keys($migrationFiles)
            ]);
            
            // Запускаем миграции
            $migrator->run([$migrationPath]);
            
            // Проверяем, что таблицы созданы
            $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = ? AND table_type = 'BASE TABLE'", [$schemaName]);
            $tableNames = array_column($tables, 'table_name');
            
            Log::info('RegisterController: Migrations completed for schema.', [
                'schema_name' => $schemaName,
                'created_tables' => $tableNames
            ]);
            
            // Проверяем конкретно таблицу users
            if (!in_array('users', $tableNames)) {
                throw new \Exception("Users table was not created in schema '{$schemaName}'. Created tables: " . implode(', ', $tableNames));
            }
            
        } finally {
            // Восстанавливаем настройки
            Config::set('database.migrations', $originalMigrationsTable);
            DB::statement("SET search_path TO public");
        }
    }

    /**
     * Запускает сидеры для конкретной схемы
     */
    private function runSeedersForSchema($schemaName)
    {
        // Устанавливаем search_path для текущего соединения
        DB::statement("SET search_path TO \"{$schemaName}\", public");
        
        try {
            Log::info('RegisterController: Running TenantDatabaseSeeder for schema.', ['schema_name' => $schemaName]);
            $seeder = app(TenantDatabaseSeeder::class);
            $seeder->run();
            Log::info('RegisterController: TenantDatabaseSeeder completed for schema.', ['schema_name' => $schemaName]);
            
        } finally {
            // Восстанавливаем search_path
            DB::statement("SET search_path TO public");
        }
    }

    /**
     * Создает пользователя в схеме клиента
     */
    private function createTenantUser($schemaName, $request)
    {
        // Получаем соединение
        $connection = DB::connection();
        
        // Проверяем существование таблицы users в схеме
        $tableExists = DB::selectOne("
            SELECT 1 FROM information_schema.tables 
            WHERE table_schema = ? AND table_name = 'users'
        ", [$schemaName]);
        
        if (!$tableExists) {
            // Получаем список всех таблиц в схеме для диагностики
            $tables = DB::select("
                SELECT table_name FROM information_schema.tables 
                WHERE table_schema = ? AND table_type = 'BASE TABLE'
            ", [$schemaName]);
            $tableNames = array_column($tables, 'table_name');
            
            throw new \Exception("Table 'users' does not exist in schema '{$schemaName}'. Available tables: " . implode(', ', $tableNames));
        }
        
        Log::info('RegisterController: Users table exists in schema.', ['schema_name' => $schemaName]);
        
        try {
            // Создаем пользователя напрямую через Query Builder с явным указанием схемы
            $userId = $connection->table("{$schemaName}.users")->insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('RegisterController: User inserted into tenant schema.', [
                'schema_name' => $schemaName,
                'user_id' => $userId
            ]);

            // Временно устанавливаем search_path для получения пользователя через Eloquent
            $connection->statement("SET search_path TO \"{$schemaName}\", public");
            
            // Получаем пользователя через Eloquent
            $tenantUser = User::find($userId);
            
            // Восстанавливаем search_path
            $connection->statement("SET search_path TO public");

            if (!$tenantUser) {
                throw new \Exception("Could not find newly created tenant user with ID: {$userId}");
            }

            Log::info('RegisterController: Tenant user created successfully.', ['user_id' => $userId]);
            
            return $tenantUser;
            
        } catch (\Exception $e) {
            // В случае ошибки пытаемся восстановить search_path
            try {
                $connection->statement("SET search_path TO public");
            } catch (\Exception $restoreException) {
                // Игнорируем ошибку восстановления, так как транзакция уже прервана
                Log::warning('Could not restore search_path after error: ' . $restoreException->getMessage());
            }
            throw $e;
        }
    }

    /**
     * Регистрирует нового ПОЛЬЗОВАТЕЛЯ в уже существующей компании.
     * Вызывается с субдомена.
     */
    public function registerUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Middleware уже установил правильное соединение с БД
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        // Назначаем новому пользователю роль 'user'
        $user->assignRoles('user');
    
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}