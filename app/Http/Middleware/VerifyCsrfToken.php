<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log; // <-- Добавлено

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Determine if the session and input tokens match.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        // --- ДИАГНОСТИКА ---
        $sessionToken = $request->session()->token();
        $requestToken = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        Log::info('--- CSRF TOKEN CHECK ---');
        Log::info('Session ID on form submit: ' . $request->session()->getId());
        Log::info('Token from Session: ' . $sessionToken);
        Log::info('Token from Request (_token input): ' . $requestToken);
        
        $match = hash_equals($sessionToken, (string) $requestToken);
        Log::info('Do tokens match? ' . ($match ? 'YES' : 'NO'));
        // --- КОНЕЦ ДИАГНОСТИКИ ---

        return $match;
    }
}