<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;

class PermissionService
{
    /**
     * Check if the current user has a specific permission
     */
    public function userCan($permission)
    {
        if (!Auth::check()) {
            return false;
        }
        
        $user = Auth::user();
        
        // Superusers bypass all permission checks
        if (method_exists($user, 'isSuperuser') && $user->isSuperuser()) {
            return true;
        }
        
        // For non-superusers, use cache for better performance
        $cacheKey = "user_{$user->id}_permission_{$permission}";
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user, $permission) {
            return $this->checkUserPermission($user, $permission);
        });
    }
    
    /**
     * Check if a user has a specific permission
     */
    protected function checkUserPermission($user, $permission)
    {
        // First check for direct permissions
        if (method_exists($user, 'hasPermissionTo')) {
            return $user->hasPermissionTo($permission);
        }
        
        // Basic fallback if user model doesn't have permission methods
        // Check direct user permissions
        if (method_exists($user, 'permissions')) {
            $directPermission = $user->permissions()
                ->where('slug', $permission)
                ->first();
                
            if ($directPermission) {
                return (bool) $directPermission->pivot->granted;
            }
        }
        
        // Check role permissions
        if (method_exists($user, 'roles')) {
            foreach ($user->roles as $role) {
                if (method_exists($role, 'permissions')) {
                    $hasPermission = $role->permissions()
                        ->where('slug', $permission)
                        ->exists();
                        
                    if ($hasPermission) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Check if the current user has any of the specified permissions
     */
    public function userCanAny(array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->userCan($permission)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if the current user has all of the specified permissions
     */
    public function userCanAll(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->userCan($permission)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if a specific action is allowed based on resource and action
     * 
     * @param string $action The action (view, create, edit, delete)
     * @param string $resource The resource (users, roles, permissions, etc.)
     * @return bool
     */
    public function can($action, $resource)
    {
        $permissionSlug = "{$action}_{$resource}";
        return $this->userCan($permissionSlug);
    }
    
    /**
     * Clear permission cache for a user
     */
    public function clearCache($userId)
    {
        // If userId is 'all', clear all permission caches
        if ($userId === 'all') {
            // Get all users
            $userIds = User::pluck('id')->toArray();
            foreach ($userIds as $id) {
                $this->clearUserPermissionCache($id);
            }
            return;
        }
        
        $this->clearUserPermissionCache($userId);
    }
    
    /**
     * Clear permission cache for a specific user
     */
    protected function clearUserPermissionCache($userId)
    {
        // Clear all permission caches for this user
        try {
            // Get all permission slugs
            $permissions = Permission::pluck('slug')->toArray();
            
            foreach ($permissions as $permission) {
                $cacheKey = "user_{$userId}_permission_{$permission}";
                Cache::forget($cacheKey);
            }
            
            // Also clear superuser cache if that method exists
            Cache::forget("user_{$userId}_is_superuser");
        } catch (\Exception $e) {
            Log::error("Error clearing permission cache: " . $e->getMessage());
        }
    }
    
    /**
     * Register a new permission
     */
    public function registerPermission($name, $description = null, $type = 'route', $resource = null, $action = null)
    {
        $slug = Str::slug(str_replace(' ', '_', strtolower($name)), '_');
        
        try {
            return Permission::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => $description,
                    'type' => $type,
                    'resource' => $resource,
                    'action' => $action
                ]
            );
        } catch (\Exception $e) {
            Log::error("Error registering permission: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create basic CRUD permissions for a resource
     */
    public function createCrudPermissions($resource)
    {
        $resource = strtolower($resource);
        $permissions = [];
        
        $actions = ['view', 'create', 'edit', 'delete'];
        
        foreach ($actions as $action) {
            $name = ucfirst($action) . ' ' . ucfirst($resource);
            $permissions[] = $this->registerPermission(
                $name,
                "Can {$action} {$resource}",
                'route',
                $resource,
                $action
            );
        }
        
        return array_filter($permissions);
    }
}