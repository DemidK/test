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
use App\Providers\RouteServiceProvider;

class LoginController extends Controller
{
    protected $schemaService;

    public function __construct(SchemaService $schemaService)
    {
        $this->schemaService = $schemaService;
    }
    
    public function showLoginForm()
    {
        $navLinks = \App\Models\NavLink::orderBy('position')->get();
        $schemaName = session('current_schema');

        // --- ДИАГНОСТИКА ---
        \Illuminate\Support\Facades\Log::info('--- SHOW LOGIN PAGE ---');
        \Illuminate\Support\Facades\Log::info('Schema context: ' . ($schemaName ?? 'main_domain'));
        \Illuminate\Support\Facades\Log::info('Session ID on page load: ' . session()->getId());
        \Illuminate\Support\Facades\Log::info('CSRF token on page load: ' . session()->token());
        // --- КОНЕЦ ДИАГНОСТИКИ ---

        return view('auth.login', ['navLinks' => $navLinks, 'schemaName' => $schemaName]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $schemaName = $request->session()->get('current_schema');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $sessionId = $request->session()->getId();

            // --- ДИАГНОСТИЧЕСКОЕ ЛОГИРОВАНИЕ ---
            \Illuminate\Support\Facades\Log::info('--- LOGIN SUCCESSFUL ---');
            \Illuminate\Support\Facades\Log::info('User ID Authenticated: ' . $user->id);
            \Illuminate\Support\Facades\Log::info('User Name: ' . $user->name);
            \Illuminate\Support\Facades\Log::info('Session ID after regenerate: ' . $sessionId);
            \Illuminate\Support\Facades\Log::info('Current Schema from session: ' . $schemaName);
            
            // Проверяем, что ключ аутентификации был установлен в сессию
            $authKey = 'login_web_' . sha1(get_class($user));
            \Illuminate\Support\Facades\Log::info('Session has auth key (' . $authKey . '): ' . ($request->session()->has($authKey) ? 'YES' : 'NO'));
            // --- КОНЕЦ ДИАГНОСТИКИ ---

            if ($schemaName) {
                $redirectUrl = route('dashboard', ['schemaName' => $schemaName]);
                \Illuminate\Support\Facades\Log::info('Redirecting to: ' . $redirectUrl);
                return redirect()->to($redirectUrl);
            }
            
            return redirect()->intended(\App\Providers\RouteServiceProvider::HOME);
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Reset connection to admin before logout
        // if (Config::has('database.connections.pgsql_admin')) {
        //     Config::set('database.connections.pgsql', Config::get('database.connections.pgsql_admin'));
        //     DB::purge('pgsql');
        //     DB::reconnect('pgsql');
        // }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}