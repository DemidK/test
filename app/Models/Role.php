<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    /**
     * Get the permissions for the role
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Get the users that have this role
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }
    
    /**
     * Check if the role has a specific permission
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions()->where('slug', $permission)->exists();
        }
        
        return $this->permissions->contains($permission);
    }
    
    /**
     * Give permissions to this role
     */
    public function givePermissionsTo(... $permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        
        if ($permissions === null) {
            return $this;
        }
        
        $this->permissions()->saveMany($permissions);
        
        return $this;
    }
    
    /**
     * Remove permissions from this role
     */
    public function revokePermissionsTo(... $permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        
        if ($permissions === null) {
            return $this;
        }
        
        $this->permissions()->detach($permissions);
        
        return $this;
    }
    
    /**
     * Sync the permissions of this role
     */
    public function syncPermissions(... $permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        
        if ($permissions === null) {
            return $this;
        }
        
        $this->permissions()->sync($permissions);
        
        return $this;
    }
    
    /**
     * Get all permissions from array of permission names/slugs
     */
    protected function getAllPermissions(array $permissions)
    {
        if (empty($permissions)) {
            return null;
        }
        
        // If array contains permission objects, return it
        if (is_object($permissions[0])) {
            return $permissions;
        }
        
        // Get permission objects from slugs or IDs
        return Permission::whereIn('slug', $permissions)
            ->orWhereIn('id', $permissions)
            ->get();
    }
    
    /**
     * Generate a slug from the name
     */
    public static function createSlug($name)
    {
        return strtolower(str_replace(' ', '_', $name));
    }
}