<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
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
            $schemaName = session('current_schema');
            $view->with('currentSchemaName', $schemaName);

            // Кэшируем навигационные ссылки. Ключ кэша зависит от схемы,
            // чтобы у каждого клиента был свой кэш навигации.
            // Это предотвращает лишние запросы к БД при рендере каждого шаблона.
            // `try...catch` нужен, чтобы сайт не падал во время миграций, когда таблицы еще нет.
            try {
                $cacheKey = 'nav_links_' . ($schemaName ?? 'public');
                $navLinks = Cache::remember($cacheKey, now()->addMinutes(60), function () {
                    return NavLink::orderBy('position')->get();
                });
                $view->with('navLinks', $navLinks);
            } catch (\Exception $e) {
                // Игнорируем ошибку, если таблица nav_links не найдена (например, при миграции)
                $view->with('navLinks', collect());
            }
        });
    }
}