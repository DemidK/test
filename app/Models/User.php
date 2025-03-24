<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'schema_name',
        'pg_username',  // We'll keep the username for reference
    ];

    protected $table = 'user';
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    /**
     * Get the schema route associated with the user.
     */
    public function schemaRoute()
    {
        return $this->hasOne(SchemaRoute::class);
    }

    /**
     * Grant direct permissions to the user
     */
    public function givePermissionsTo(... $permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        
        if (empty($permissions)) {
            return $this;
        }
        
        foreach ($permissions as $permission) {
            $this->permissions()->syncWithoutDetaching([
                $permission->id => ['granted' => true]
            ]);
        }
        
        return $this;
    }

    /**
     * Revoke permissions from the user
     */
    public function revokePermissionsTo(... $permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        
        if (empty($permissions)) {
            return $this;
        }
        
        $this->permissions()->detach($permissions);
        
        return $this;
    }

    /**
     * Deny specific permissions to the user
     * This overrides any permissions granted via roles
     */
    public function denyPermissionsTo(... $permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        
        if (empty($permissions)) {
            return $this;
        }
        
        foreach ($permissions as $permission) {
            $this->permissions()->syncWithoutDetaching([
                $permission->id => ['granted' => false]
            ]);
        }
        
        return $this;
    }

    /**
     * Assign roles to the user
     */
    public function assignRoles(... $roles)
    {
        $roles = $this->getRoles($roles);
        
        if (empty($roles)) {
            return $this;
        }
        
        $this->roles()->syncWithoutDetaching($roles);
        
        return $this;
    }

    /**
     * Remove roles from the user
     */
    public function removeRoles(... $roles)
    {
        $roles = $this->getRoles($roles);
        
        if (empty($roles)) {
            return $this;
        }
        
        $this->roles()->detach($roles);
        
        return $this;
    }

    /**
     * Sync roles for the user
     */
    public function syncRoles(... $roles)
    {
        $roles = $this->getRoles($roles);
        
        if (empty($roles)) {
            return $this;
        }
        
        $this->roles()->sync($roles);
        
        return $this;
    }

    /**
     * Check if the user has any of the given roles
     */
    public function hasAnyRole(... $roles)
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if the user has all of the given roles
     */
    public function hasAllRoles(... $roles)
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get all permissions from array of permission names/slugs/IDs
     */
    protected function getAllPermissions(array $permissions)
    {
        if (empty($permissions)) {
            return [];
        }
        
        // If array contains permission objects, return it
        if (isset($permissions[0]) && is_object($permissions[0])) {
            return $permissions;
        }
        
        // Get permission objects from slugs or IDs
        return Permission::whereIn('slug', $permissions)
            ->orWhereIn('id', $permissions)
            ->get();
    }

    /**
     * Get all roles from array of role names/slugs/IDs
     */
    protected function getRoles(array $roles)
    {
        if (empty($roles)) {
            return [];
        }
        
        // If array contains role objects, return it
        if (isset($roles[0]) && is_object($roles[0])) {
            return $roles;
        }
        
        // Get role objects from slugs or IDs
        return Role::whereIn('slug', $roles)
            ->orWhereIn('id', $roles)
            ->get();
    }


        public function isSuperuser()
    {
        // Cache the result to avoid repeated database queries
        return cache()->remember("user_{$this->id}_is_superuser", now()->addMinutes(60), function () {
            return $this->hasRole('superuser');
        });
    }

    /**
     * Check if the user has a specific role by name or ID
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles()->where('slug', $role)->exists();
        }
        
        return $this->roles->contains($role);
    }

    /**
     * Get the roles that belong to the user
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * Get the direct permissions for the user
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission')
            ->withPivot('granted')
            ->withTimestamps();
    }

    /**
     * Check if the user has a specific permission
     * This checks both direct permissions and permissions from roles
     */
    public function hasPermissionTo($permission)
    {
        // Superusers bypass all permission checks
        if ($this->isSuperuser()) {
            return true;
        }
        
        // First check for direct permissions (they override role permissions)
        $directPermission = $this->getDirectPermission($permission);
        
        if ($directPermission) {
            // If we have a direct permission, use its granted status
            return (bool) $directPermission->pivot->granted;
        }
        
        // If no direct permission, check role permissions
        return $this->hasPermissionViaRole($permission);
    }

    /**
     * Check if the user has a permission through any of their roles
     */
    public function hasPermissionViaRole($permission)
    {
        $permissionSlug = is_string($permission) ? $permission : $permission->slug;
        
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permissionSlug)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get a direct permission for this user (if it exists)
     */
    public function getDirectPermission($permission)
    {
        $permissionSlug = is_string($permission) ? $permission : $permission->slug;
        
        return $this->permissions()
            ->where('slug', $permissionSlug)
            ->first();
    }
}