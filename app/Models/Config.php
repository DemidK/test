<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = ['key', 'value'];
    
    // Get config value with default fallback
    public static function getConfig($key, $default = null)
    {
        $config = self::where('key', $key)->first();
        return $config ? json_decode($config->value, true) : $default;
    }
}