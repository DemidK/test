<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SchemaRoute;
use App\Models\User;
use App\Services\SchemaService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        // Определяем, это регистрация на главном домене или на субдомене
        $isTenantRegistration = !$request->route('schemaName');
        
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
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user,email'], // Проверяем главную таблицу users
            'schema_name' => ['required', 'string', 'max:63', 'alpha_dash', 'unique:schema_routes,route_name'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        $schemaName = Str::snake($request->schema_name);

        DB::beginTransaction();
        try {
            // 1. Создаем пользователя-владельца в ГЛАВНОЙ таблице (public.users)
            $owner = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'schema_name' => $schemaName, // Сразу связываем его со схемой
            ]);

            // 2. Создаем запись о маршруте для субдомена
            SchemaRoute::create([
                'schema_name' => $schemaName,
                'route_name' => $schemaName,
                'user_id' => $owner->id,
                'is_active' => true
            ]);

            // 3. Вызываем сервис, который создает схему, запускает миграции и сидеры
            $this->schemaService->createUserSchema($schemaName);

            // 4. Устанавливаем соединение с новой схемой, чтобы создать там пользователя
            $this->setTenantConnection($schemaName);

            // 5. Создаем первого пользователя ВНУТРИ схемы клиента
            $tenantUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 6. Назначаем этому пользователю роль 'superuser' (сидер уже создал эту роль)
            $tenantUser->assignRoles('superuser');

            DB::commit();

            // 7. Авторизуем пользователя и перенаправляем
            Auth::login($owner);
            
            // После логина Laravel регенерирует сессию, нужно снова установить контекст
            session(['current_schema' => $schemaName]);

            return redirect()->route('dashboard', ['schemaName' => $schemaName]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Tenant registration error: " . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return back()->with('error', 'Registration failed. Please try again or check the logs.')->withInput();
        } finally {
            $this->restoreDefaultConnection();
        }
    }

    /**
     * Регистрирует нового ПОЛЬЗОВАТЕЛЯ в уже существующей компании.
     * Вызывается с субдомена.
     */
    public function registerUser(Request $request, $schemaName)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            // Валидация уникальности email происходит в контексте схемы клиента (благодаря Middleware)
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        // Middleware уже установил правильное соединение с БД
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        // Назначаем новому пользователю роль 'user' (сидер уже создал эту роль)
        $user->assignRoles('user');
    
        Auth::login($user);

        return redirect()->route('dashboard', ['schemaName' => $schemaName]);
    }

    /**
     * Вспомогательные методы для смены соединения
     */
    protected function setTenantConnection(string $schemaName): void
    {
        $tenantConnectionName = 'pgsql_tenant';
        $defaultConnection = config('database.default');
        
        $config = config("database.connections.{$defaultConnection}");
        $config['schema'] = $schemaName;
        
        config(["database.connections.{$tenantConnectionName}" => $config]);
        DB::setDefaultConnection($tenantConnectionName);
    }

    protected function restoreDefaultConnection(): void
    {
        DB::setDefaultConnection(config('database.default'));
    }
}