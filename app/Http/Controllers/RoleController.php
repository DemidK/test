<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\NavLink;
use App\Services\PermissionService;

class RoleController extends CrudController
{
    protected $permissionService;
    
    public function __construct(PermissionService $permissionService)
    {
        $this->model = Role::class;
        $this->viewPath = 'roles';
        $this->routePrefix = 'roles';
        $this->permissionService = $permissionService;
        
        $this->searchableFields = ['name', 'slug', 'description'];
        $this->sortableFields = [
            'name' => 'name',
            'slug' => 'slug',
            'created_at' => 'created_at'
        ];
        
        $this->validationRules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
    
    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $navLinks = NavLink::orderBy('position')->get();
        $permissionsByResource = $this->permissionService->getPermissionsByResource();
        
        return view("{$this->viewPath}.create", compact('navLinks', 'permissionsByResource'));
    }
    
    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules);
        
        // Generate slug from name
        $validated['slug'] = Role::createSlug($validated['name']);
        
        try {
            $role = $this->storeItem($validated);
            
            // Sync permissions if any were selected
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }
            
            return redirect()->route("{$this->routePrefix}.show", $role)
                ->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
    
    /**
     * Show the form for editing the specified role.
     */
    public function edit($id)
    {
        $item = $this->model::findOrFail($id);
        $navLinks = NavLink::orderBy('position')->get();
        $permissionsByResource = $this->permissionService->getPermissionsByResource();
        
        // Get currently assigned permission IDs
        $rolePermissionIds = $item->permissions()->pluck('permissions.id')->toArray();
        
        return view("{$this->viewPath}.edit", compact('item', 'navLinks', 'permissionsByResource', 'rolePermissionIds'));
    }
    
    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, $id)
    {
        $role = $this->model::findOrFail($id);
        $validated = $request->validate($this->validationRules);
        
        // Generate slug from name
        $validated['slug'] = Role::createSlug($validated['name']);
        
        try {
            $this->updateItem($role, $validated);
            
            // Sync permissions
            $permissions = $request->has('permissions') ? $request->permissions : [];
            $role->syncPermissions($permissions);
            
            // Clear permission cache for all users with this role
            $userIds = $role->users()->pluck('user.id')->toArray();
            foreach ($userIds as $userId) {
                $this->permissionService->clearCache($userId);
            }
            
            return redirect()->route("{$this->routePrefix}.show", $role)
                ->with('success', 'Role updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
    
    /**
     * Display the specified role.
     */
    public function show($id)
    {
        $item = $this->model::findOrFail($id);
        $navLinks = NavLink::orderBy('position')->get();
        
        // Get permissions grouped by resource
        $permissionsByResource = [];
        foreach ($item->permissions as $permission) {
            $resource = $permission->resource ?: 'General';
            if (!isset($permissionsByResource[$resource])) {
                $permissionsByResource[$resource] = [];
            }
            $permissionsByResource[$resource][] = $permission;
        }
        
        // Get users with this role
        $users = $item->users()->paginate(10);
        
        return view("{$this->viewPath}.show", compact('item', 'navLinks', 'permissionsByResource', 'users'));
    }
}