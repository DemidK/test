<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * Get the table associated with the model.
     *
     * Наша мульти-арендная система теперь полностью управляется через
     * SetSchemaMiddleware, который устанавливает search_path.
     * Поэтому модели больше не нужно вручную добавлять префикс схемы.
     * Она просто должна возвращать имя таблицы как есть.
     * PostgreSQL сам найдет нужную таблицу в нужной схеме благодаря search_path.
     *
     * @return string
     */
    public function getTable()
    {
        // Просто возвращаем оригинальное имя таблицы из модели (например, 'users', 'partners').
        return parent::getTable();
    }
}