<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends BaseModel
{
    protected $fillable = ['route', 'data'];
    
    // Get config value with default fallback
    public static function getConfig($route, $default = [])
    {
        $config = self::where('route', $route)->first();
        return $config ? json_decode($config->data, true) : $default;
    }
}