<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
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
        $roles = Role::orderBy('name')->get();
        $userRoleIds = $user->roles()->pluck('roles.id')->toArray();
        
        // The view name is changed to a more standard 'users.edit'
        return view('users.edit', compact(
            'user',
            'navLinks',
            'roles',
            'userRoleIds'
        ));
    }
    
    /**
     * Update user permissions and roles
     */
    public function update(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        try {
            // Sync roles. Direct permissions are no longer managed here.
            $roles = $request->input('roles', []);
            $user->syncRoles($roles);
            
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
        
        // Get all unique permissions (route names) from the user's roles.
        $permissions = [];
        foreach ($user->roles as $role) {
            $permissions = array_merge($permissions, $role->permissions ?? []);
        }
        $permissions = array_unique($permissions);
        sort($permissions);

        // Group permissions by the first part of the route name for cleaner display.
        $permissionsByGroup = [];
        foreach ($permissions as $permission) {
            $group = explode('.', $permission)[0] ?? 'General';
            $groupName = \Illuminate\Support\Str::title(str_replace(['_', '-'], ' ', $group));
            if (!isset($permissionsByGroup[$groupName])) {
                $permissionsByGroup[$groupName] = [];
            }
            $permissionsByGroup[$groupName][] = $permission;
        }
        
        // The view name is changed to a more standard 'users.show'
        return view('users.show', compact(
            'user',
            'navLinks',
            'permissionsByGroup'
        ));
    }
}