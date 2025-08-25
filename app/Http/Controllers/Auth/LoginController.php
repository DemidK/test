<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends CrudController
{
    public function showLoginForm()
    {
        return view('auth.login', ['schemaName' => session('current_schema')]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            Log::info('Login successful for user: ' . Auth::user()->email);
            // Параметр 'schemaName' будет подставлен автоматически благодаря
            // URL::defaults в SetSchemaMiddleware, так как мы находимся на маршруте субдомена.
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}