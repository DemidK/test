<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    public static function createSlug($name)
    {
        return Str::slug($name, '_');
    }

    /**
     * Check if the role has a specific permission.
     * Supports wildcards, e.g., 'users.*'.
     */
    public function hasPermission(string $routeName): bool
    {
        $permissions = $this->permissions ?? [];
        if (in_array('*', $permissions) || in_array($routeName, $permissions)) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (Str::is($permission, $routeName)) {
                return true;
            }
        }

        return false;
    }

    public function syncPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
        $this->save();
    }
}