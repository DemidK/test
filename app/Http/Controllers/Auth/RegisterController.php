<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NavLink;
use App\Models\Client;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Services\SchemaService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            
            // Create client record
            $client = Client::create([
                'name' => $user->name,
                'identification_number' => 'USR' . $user->id,
                'json_data' => null
            ]);

            // Create schema for user
            $schemaName = 'user_' . $user->id;
            $this->schemaService->createUserSchema($schemaName);

            // Update user with schema name
            $user->update(['schema_name' => $schemaName]);

            DB::commit();

            $this->guard()->login($user);

            return redirect($this->redirectTo);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        $hashedPassword = DB::selectOne('SELECT hash_password(?) as hash', [$data['password']])->hash;
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'schema_name' => null, // Will be updated after schema creation
        ]);
    }
}