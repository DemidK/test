<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NavLink;
use App\Models\Partner;
use App\Models\SchemaRoute;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Services\SchemaService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/';
    protected $schemaService;

    public function __construct(SchemaService $schemaService)
    {
        $this->middleware('guest');
        $this->schemaService = $schemaService;
    }

    public function showRegistrationForm()
    {
        $navLinks = NavLink::orderBy('position')->get();
        $data = [
            'navLinks' => $navLinks,
        ];
        return view('auth.register', $data);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        DB::beginTransaction();
        try {
            // Create user
            $user = $this->create($request->all());
            
            // Create partner record
            $partner = Partner::create([
                'name' => $user->name,
                'identification_number' => 'USR' . $user->id,
                'json_data' => null
            ]);

            // Format schema name (lowercase, replace spaces with underscores)
            $schemaName = Str::snake($request->schema_name);
            
            // Create schema route entry in public schema
            $routeName = $schemaName;
            $schemaRoute = SchemaRoute::create([
                'schema_name' => $schemaName,
                'route_name' => $routeName,
                'user_id' => $user->id,
                'is_active' => true
            ]);

            // Create schema for user with their provided schema name
            // This now only returns the username, not the password
            $pgUsername = $this->schemaService->createUserSchema($schemaName, $user->id);

            // Update user with schema name and PostgreSQL username only (no password)
            $user->update([
                'schema_name' => $schemaName,
                'pg_username' => $pgUsername
            ]);

            // Store plain password in session for connection after login
            $request->session()->put('plain_password', $request->password);

            // Generate deterministic password for PostgreSQL
            $initialPassword = $this->schemaService->generateSecurePassword(
                $user->id,
                $request->password, // This is the plain text password from the request
                $schemaName
            );
            
            // Update the PostgreSQL role password
            DB::unprepared("ALTER ROLE {$pgUsername} WITH PASSWORD '{$initialPassword}'");

            // Assign the user as a superuser of their schema
            // First get the superuser role from the schema
            // $superuserRole = DB::selectOne("SELECT id FROM {$schemaName}.roles WHERE slug = 'superuser'");
            // if ($superuserRole) {
            //     // Assign the role to the user
            //     DB::statement("
            //         INSERT INTO {$schemaName}.user_role (user_id, role_id, created_at, updated_at)
            //         VALUES (
            //             {$user->id},
            //             {$superuserRole->id},
            //             NOW(),
            //             NOW()
            //         )
            //     ");
                
            //     Log::info("Assigned superuser role to user {$user->id} in schema {$schemaName}");
            // }

            DB::commit();

            $this->guard()->login($user);
            
            // CRITICAL: Connect to the new schema with the correct credentials immediately after login
            if ($user->pg_username) {
                // Store original connection for admin operations
                // Config::set('database.connections.pgsql_admin', Config::get('database.connections.pgsql'));
                
                // // Set user connection
                // Config::set('database.connections.pgsql.username', $user->pg_username);
                // Config::set('database.connections.pgsql.password', $initialPassword);
                
                // // Reconnect with new credentials
                // DB::purge('pgsql');
                // DB::reconnect('pgsql');
                
                // Set search path to user's schema FIRST, then public
                DB::statement("SET search_path TO {$user->schema_name}, public");
                
                // Verify the search path was set correctly
                $result = DB::select("SHOW search_path");
                Log::info("Registration search_path: " . $result[0]->search_path);
            }

            return redirect($this->redirectTo);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Registration error: " . $e->getMessage());
            throw $e;
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user'],
            'schema_name' => ['required', 'string', 'max:64', 'alpha_dash', 'unique:schema_routes,route_name'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'schema_name' => null,
            'pg_username' => null,
        ]);
    }

    public function showTenantRegistrationForm()
    {
        $navLinks = NavLink::orderBy('position')->get();
        return view('auth.register', ['navLinks' => $navLinks, 'isTenantRegistration' => true]);
    }

    public function registerTenant(Request $request)
    {
        // Валидация данных, включая проверку на уникальность имени схемы
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user'],
            'schema_name' => ['required', 'string', 'max:63', 'alpha_dash', 'unique:schema_routes,route_name'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        DB::beginTransaction();
        try {
            // 1. Создаем пользователя-владельца в public.users
            $owner = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            
            $schemaName = Str::snake($request->schema_name);

            // 2. Вызываем сервис, который создает схему, таблицы и НАЗНАЧАЕТ РОЛИ внутри себя
            $this->schemaService->createUserSchema($schemaName, $owner->id);

            // 3. Обновляем пользователя-владельца, связывая его со схемой
            $owner->update(['schema_name' => $schemaName]);

            // 4. Создаем запись о маршруте для субдомена
            SchemaRoute::create([
                'schema_name' => $schemaName,
                'route_name' => $schemaName, 
                'user_id' => $owner->id,
                'is_active' => true
            ]);

            // 5. Создаем пользователя ВНУТРИ новой схемы
            DB::statement("
                INSERT INTO {$schemaName}.user (id, name, email, password, created_at, updated_at)
                VALUES (?, ?, ?, ?, NOW(), NOW())
            ", [1, $owner->name, $owner->email, $owner->password]);
            
            // --- БЛОК С ДУБЛИРУЮЩИМСЯ НАЗНАЧЕНИЕМ РОЛИ УДАЛЕН ---

            DB::commit();

            // Перенаправляем на страницу "подождите" с указанием нового домена
            return redirect()->route('platform.setup')->with('new_domain', "http://{$schemaName}." . env('APP_DOMAIN', 'domain.lv'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Tenant registration error: " . $e->getMessage());
            return back()->with('error', 'Registration failed. Please try again.');
        }
    }

    public function showUserRegistrationForm()
    {
        $navLinks = NavLink::orderBy('position')->get();
        return view('auth.register', ['navLinks' => $navLinks, 'isTenantRegistration' => false]);
    }

    public function registerUser(Request $request)
    {       
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->save();

        Auth::login($user);

        return redirect('/dashboard');
    }
}