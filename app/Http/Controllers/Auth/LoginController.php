<?php
// app/Http/Controllers/Auth/LoginController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NavLink;
use App\Models\SchemaRoute;
use App\Models\User;
use App\Services\SchemaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    protected $schemaService;

    public function __construct(SchemaService $schemaService)
    {
        $this->schemaService = $schemaService;
    }
    
    public function showLoginForm(Request $request, $schemaRoute = null)
    {
        $navLinks = NavLink::orderBy('position')->get();
        
        // Check if we're accessing a schema-specific login
        $schemaInfo = null;
        if ($schemaRoute) {
            $schemaInfo = SchemaRoute::where('route_name', $schemaRoute)
                ->where('is_active', true)
                ->first();
                
            if (!$schemaInfo) {
                abort(404, 'Schema route not found');
            }
        }
        
        // Example data to pass to the view
        $data = [
            'navLinks' => $navLinks,
            'schemaInfo' => $schemaInfo
        ];
        
        return view('auth.login', $data);
    }

    public function login(Request $request, $schemaRoute = null)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        // Store the plaintext password temporarily in the session
        // It will be used by the middleware to generate the PostgreSQL password
        // and then immediately removed
        $request->session()->put('plain_password', $request->password);
        
        // Check if we're accessing a schema-specific login
        if ($schemaRoute) {
            $schemaInfo = SchemaRoute::where('route_name', $schemaRoute)
                ->where('is_active', true)
                ->first();
                
            if (!$schemaInfo) {
                abort(404, 'Schema route not found');
            }
            
            // Get the user who owns this schema
            $user = User::where('schema_name', $schemaInfo->schema_name)->first();
            
            if (!$user) {
                return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ]);
            }
            
            // We need to verify the credentials against this specific user
            if ($user->email !== $credentials['email']) {
                return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ]);
            }
            
            // Attempt authentication with the specific user's schema
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                
                // Generate deterministic password for PostgreSQL
                $pgPassword = $this->schemaService->generateSecurePassword(
                    $user->id, 
                    $request->password, // Using plain password here
                    $user->schema_name
                );
                
                // Set the schema for the user's session
                session(['schema' => $user->schema_name]);
                
                // Connect to database with user's PostgreSQL credentials
                if ($user->pg_username) {
                    // Store original connection for admin operations
                    Config::set('database.connections.pgsql_admin', Config::get('database.connections.pgsql'));
                    
                    // Set user connection
                    Config::set('database.connections.pgsql.username', $user->pg_username);
                    Config::set('database.connections.pgsql.password', $pgPassword);
                    
                    // Reconnect with new credentials
                    DB::purge('pgsql');
                    DB::reconnect('pgsql');
                    
                    // IMPORTANT: Set search path to user's schema FIRST, then public
                    DB::statement("SET search_path TO {$user->schema_name}, public");
                    
                    // Verify the search path was set correctly
                    $result = DB::select("SHOW search_path");
                    Log::info("Login search_path: " . $result[0]->search_path);
                }
                
                return redirect()->intended('/dashboard');
            }
            
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }
        
        // Regular login process for admins/global schema
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Get the authenticated user
            $user = Auth::user();
            
            // Only set schema connection if the user has a schema
            if ($user->schema_name && $user->pg_username) {
                // Generate deterministic password for PostgreSQL
                $pgPassword = $this->schemaService->generateSecurePassword(
                    $user->id, 
                    $request->password, // Using plain password here
                    $user->schema_name
                );
                
                // Set the schema for the user's session
                session(['schema' => $user->schema_name]);
                
                // Store original connection for admin operations
                Config::set('database.connections.pgsql_admin', Config::get('database.connections.pgsql'));
                
                // Set user connection
                Config::set('database.connections.pgsql.username', $user->pg_username);
                Config::set('database.connections.pgsql.password', $pgPassword);
                
                // Reconnect with new credentials
                DB::purge('pgsql');
                DB::reconnect('pgsql');
                
                // IMPORTANT: Set search path to user's schema FIRST, then public
                DB::statement("SET search_path TO {$user->schema_name}, public");
                
                // Verify the search path was set correctly
                $result = DB::select("SHOW search_path");
                Log::info("Login search_path: " . $result[0]->search_path);
            }
            
            return redirect()->intended('/dashboard');
        }
    
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        // Reset connection to admin before logout
        if (Config::has('database.connections.pgsql_admin')) {
            Config::set('database.connections.pgsql', Config::get('database.connections.pgsql_admin'));
            DB::purge('pgsql');
            DB::reconnect('pgsql');
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}