<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Services\PermissionService;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Этот код будет выполняться перед всеми остальными проверками прав.
        // Если у пользователя есть роль 'superuser', ему будет предоставлен доступ ко всему.
        // Это стандартный и самый эффективный способ реализации "супер-админа" в Laravel.
        Gate::before(function (User $user, $ability) {
            // Это самая надежная проверка для суперпользователя.
            // Она напрямую запрашивает у БД, есть ли у пользователя хотя бы одна роль,
            // у которой в JSON-массиве 'permissions' есть значение '*'.
            if ($user->roles()->whereJsonContains('permissions', '*')->exists()) {
                return true;
            }
            return null; // Важно возвращать null, чтобы проверки продолжились для других прав
        });

        // Этот код будет вызываться для ЛЮБОЙ проверки прав (например, @can('users.index')),
        // для которой не определена специальная политика (Policy).
        // Он делегирует проверку вашему PermissionService, который уже умеет работать с ролями и кэшем.
        Gate::after(function (User $user, string $ability) {
            return app(PermissionService::class)->userCan($ability);
        });
    }
}