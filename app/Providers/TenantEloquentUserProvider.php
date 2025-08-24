<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TenantEloquentUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by the given credentials.
     */
public function retrieveByCredentials(array $credentials)
{
    if (empty($credentials) || (count($credentials) === 1 && array_key_exists('password', $credentials))) {
        return;
    }

    // Получаем имя нужного соединения из конфигурации
    $connectionName = config('auth.providers.users.connection');

    // Создаем экземпляр модели (например, new User)
    $model = $this->createModel();

    // Устанавливаем соединение НА ЭКЗЕМПЛЯРЕ МОДЕЛИ
    if ($connectionName) {
        $model->setConnection($connectionName);
    }

    // Теперь создаем построитель запросов ИЗ УЖЕ НАСТРОЕННОЙ МОДЕЛИ.
    // Он автоматически будет использовать правильное соединение.
    $query = $model->newQuery();

    foreach ($credentials as $key => $value) {
        if (str_contains($key, 'password')) {
            continue;
        }

        if (is_array($value) || $value instanceof \Illuminate\Contracts\Database\Query\Expression) {
            $query->whereIn($key, $value);
        } else {
            $query->where($key, $value);
        }
    }

    return $query->first();
}

    /**
     * Validate a user against the given credentials.
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        Log::info("TenantUserProvider: Validating credentials for user ID: " . $user->getAuthIdentifier());
        
        return $this->hasher->check(
            $credentials['password'],
            $user->getAuthPassword()
        );
    }

    /**
     * Create a new instance of the model.
     */
    public function createModel()
    {
        $class = '\\' . ltrim($this->model, '\\');
        $model = new $class;

        // Устанавливаем правильное соединение для модели
        $connectionName = config('auth.providers.users.connection');
        if ($connectionName) {
            $model->setConnection($connectionName);
            Log::info("TenantUserProvider: Model connection set to: {$connectionName}");
        }

        return $model;
    }

    /**
     * Gets the name of the Eloquent user model.
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets the name of the Eloquent user model.
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }
}