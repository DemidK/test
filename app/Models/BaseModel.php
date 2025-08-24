<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * Переопределяем метод для получения имени подключения к БД для этой модели.
     *
     * @return string|null
     */
    // public function getConnectionName()
    // {
    //     // Если в сессии есть имя временного подключения для клиента,
    //     // принудительно используем его для всех запросов этой модели.
    //     if ($tenantConnection = session('tenant_connection')) {
    //         return $tenantConnection;
    //     }

    //     // В противном случае используем подключение по умолчанию.
    //     return parent::getConnectionName();
    // }
}