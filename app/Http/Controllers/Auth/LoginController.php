<?php
// app/Http/Controllers/Auth/LoginController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NavLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        $navLinks = NavLink::orderBy('position')->get();
        // Example data to pass to the view
        $data = [
            'navLinks' => $navLinks,
        ];
        return view('auth.login', $data);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        // Hash the input password using our hash_password function
        $hashedPassword = DB::selectOne('SELECT hash_password(?) as hash', [$request->input('password')])->hash;
        
        // Check if user exists with the hashed password
        $user = DB::selectOne('
            SELECT * FROM "user" 
            WHERE email = ? 
            AND password = ?', 
            [
                $request->input('email'),
                $hashedPassword
            ]
        );

        if ($user) {
            Auth::loginUsingId($user->id);
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}