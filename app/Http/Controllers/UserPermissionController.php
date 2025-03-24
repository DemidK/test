<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\NavLink;
use App\Services\PermissionService;

class UserPermissionController extends Controller
{
    protected $permissionService;
    
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }
    
    /**
     * Show permission management form for a user
     */
    public function edit($userId)
    {
        $user = User::findOrFail($userId);
        $navLinks = NavLink::orderBy('position')->get();
        
        // Get all available roles
        $roles = Role::all();
        $userRoleIds = $user->roles()->pluck('roles.id')->toArray();
        
        // Get permissions grouped by resource
        $permissionsByResource = $this->permissionService->getPermissionsByResource();
        
        // Get user's direct permissions with granted status
        $userPermissions = $user->permissions()
            ->get()
            ->mapWithKeys(function ($permission) {
                return [$permission->id => [
                    'granted' => (bool) $permission->pivot->granted
                ]];
            })
            ->toArray();
        
        return view('users.permissions', compact(
            'user',
            'navLinks',
            'roles',
            'userRoleIds',
            'permissionsByResource',
            'userPermissions'
        ));
    }
    
    /**
     * Update user permissions and roles
     */
    public function update(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        try {
            // Update roles
            $roles = $request->has('roles') ? $request->roles : [];
            $user->syncRoles($roles);
            
            // Update direct permissions
            if ($request->has('permissions')) {
                // First, remove all direct permissions
                $user->permissions()->detach();
                
                // Then add each permission with its granted status
                foreach ($request->permissions as $permissionId => $status) {
                    $granted = isset($status['granted']) && $status['granted'] === '1';
                    $user->permissions()->attach($permissionId, ['granted' => $granted]);
                }
            }
            
            // Clear the permission cache for this user
            $this->permissionService->clearCache($user->id);
            
            return redirect()->route('users.show', $user->id)
                ->with('success', 'User permissions updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
    
    /**
     * Show a list of users with their roles
     */
    public function index()
    {
        $navLinks = NavLink::orderBy('position')->get();
        $users = User::with('roles')->paginate(10);
        
        return view('users.index', compact('navLinks', 'users'));
    }
    
    /**
     * Show a user's permissions and roles
     */
    public function show($userId)
    {
        $user = User::with('roles')->findOrFail($userId);
        $navLinks = NavLink::orderBy('position')->get();
        
        // Get direct permissions grouped by status (granted/denied)
        $directPermissions = [
            'granted' => [],
            'denied' => []
        ];
        
        foreach ($user->permissions as $permission) {
            $status = $permission->pivot->granted ? 'granted' : 'denied';
            $resource = $permission->resource ?: 'General';
            
            if (!isset($directPermissions[$status][$resource])) {
                $directPermissions[$status][$resource] = [];
            }
            
            $directPermissions[$status][$resource][] = $permission;
        }
        
        // Get inherited permissions from roles
        $inheritedPermissions = [];
        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                $resource = $permission->resource ?: 'General';
                
                if (!isset($inheritedPermissions[$resource])) {
                    $inheritedPermissions[$resource] = [];
                }
                
                // Check if this permission is already directly granted or denied
                $isDirectlySet = $user->permissions()
                    ->where('permissions.id', $permission->id)
                    ->exists();
                
                if (!$isDirectlySet) {
                    $inheritedPermissions[$resource][] = [
                        'permission' => $permission,
                        'role' => $role
                    ];
                }
            }
        }
        
        return view('users.show_permissions', compact(
            'user',
            'navLinks',
            'directPermissions',
            'inheritedPermissions'
        ));
    }
}