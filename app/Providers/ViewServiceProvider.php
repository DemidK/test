<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\NavLink; // <-- Добавьте эту строку

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Делимся переменными со ВСЕМИ шаблонами.
        View::composer('*', function ($view) {
            // Переменная для определения схемы (уже была)
            $schemaName = session('current_schema');
            
            // Загружаем навигационные ссылки из базы данных
            $navLinks = NavLink::orderBy('position')->get();

            // Передаём обе переменные в шаблон.
            $view->with('currentSchemaName', $schemaName)
                 ->with('navLinks', $navLinks); // <-- Добавьте эту часть
        });
    }
}