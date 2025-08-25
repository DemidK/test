<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Partner;
use App\Models\NavLink;
use Symfony\Component\Process\Process;

class DashboardController extends CrudController
{
    public function index(Request $request) // Добавьте Request
    {
        // --- ДИАГНОСТИЧЕСКОЕ ЛОГИРОВАНИЕ ---
        \Illuminate\Support\Facades\Log::info('--- DASHBOARD REQUEST START ---');
        \Illuminate\Support\Facades\Log::info('Request to URL: ' . $request->fullUrl());
        \Illuminate\Support\Facades\Log::info('Session ID on arrival: ' . $request->session()->getId());
        \Illuminate\Support\Facades\Log::info('Auth::check() status: ' . (Auth::check() ? 'TRUE' : 'FALSE'));
        
        if (Auth::check()) {
            \Illuminate\Support\Facades\Log::info('Authenticated User ID: ' . Auth::id());
        } else {
            \Illuminate\Support\Facades\Log::info('User is NOT authenticated on dashboard request.');
        }
        // --- КОНЕЦ ДИАГНОСТИКИ ---

        // ... ваш остальной код метода index ...
        $totalUsers = \App\Models\User::count();
        $totalPartners = \App\Models\Partner::count();
        $navLinks = \App\Models\NavLink::orderBy('position')->get();
        $updateUrl = null;
        // Используем имя маршрута в качестве права для консистентности.
        if (Auth::user()?->can('app.run_update')) {
            $updateUrl = route('app.run_update');
        }

        return view('dashboard', [
            'totalUsers' => $totalUsers,
            'totalPartners' => $totalPartners,
            'updateUrl' => $updateUrl,
        ]);
    }

    public function runUpdate()
    {
        // Заменяем жестко закодированный ID на проверку прав через Gate.
        if (!Auth::user()?->can('app.run_update')) {
            abort(403, 'Unauthorized action.');
        }

        $scriptPath = base_path('update_app.sh');

        if (!file_exists($scriptPath)) {
            Log::channel('deploy')->error("Update script not found at {$scriptPath}");
            return redirect()->route('dashboard')->with('error', 'Скрипт обновления не найден!');
        }
        
        // Просто запускаем процесс. Логирование теперь происходит внутри скрипта.
        $process = new Process(['sh', $scriptPath]);
        $process->setWorkingDirectory(base_path());
        $process->setTimeout(360);
        $process->run();

        // Проверяем только код завершения процесса.
        if (!$process->isSuccessful()) {
            // Запишем в лог только факт ошибки, т.к. детали уже будут в логе от самого скрипта
            Log::channel('deploy')->error("Deployment script failed with exit code " . $process->getExitCode());
            return redirect()->route('dashboard')->with('error', 'Произошла ошибка во время обновления. Проверьте deploy.log.');
        }
        
        return redirect()->route('dashboard')->with('success', 'Приложение успешно обновлено!');
    }
}