<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Get hashed password using our PostgreSQL function
        $hashedPassword = DB::selectOne('SELECT hash_password(?) as hash', [$validated['password']])->hash;

        // Create user with raw query to ensure password is stored as-is
        $user = DB::insert('
            INSERT INTO "user" (name, email, password, created_at, updated_at) 
            VALUES (?, ?, ?, NOW(), NOW()) 
            RETURNING id', 
            [$validated['name'], $validated['email'], $hashedPassword]
        );

        // Get the newly created user
        $newUser = User::find(DB::getPdo()->lastInsertId());
        
        Auth::login($newUser);
        return redirect('/dashboard');
    }
}
