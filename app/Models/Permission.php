<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'resource',
        'action'
    ];

    /**
     * Get the roles that have this permission
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * Get the users that have this permission directly
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permission')
            ->withPivot('granted')
            ->withTimestamps();
    }
    
    /**
     * Generate a slug from the name
     */
    public static function createSlug($name)
    {
        return strtolower(str_replace(' ', '_', $name));
    }
}