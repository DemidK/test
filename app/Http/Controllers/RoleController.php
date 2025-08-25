<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
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
        $routesByGroup = $this->permissionService->getGroupedRoutes();
        
        return view("{$this->viewPath}.create", compact('routesByGroup'));
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
            $permissions = $request->input('permissions', []);
            if (!empty($permissions)) {
                $role->syncPermissions($permissions);
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
        $routesByGroup = $this->permissionService->getGroupedRoutes();
        
        // Get currently assigned permission IDs
        $rolePermissions = $item->permissions ?? [];
        
        return view("{$this->viewPath}.edit", compact('item','routesByGroup', 'rolePermissions'));
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
            $permissions = $request->input('permissions', []);
            $role->syncPermissions($permissions);
            
            // Clear permission cache for all users with this role
            if ($role->wasChanged('permissions')) {
                $role->users->each(fn($user) => $this->permissionService->clearCache($user->id));
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
        
        // Get permissions grouped by resource
        // Get permissions grouped by resource for display
        $routesByGroup = [];
        if (!empty($item->permissions)) {
            foreach ($item->permissions as $routeName) {
                $resource = explode('.', $routeName)[0] ?? 'General';
                $resourceName = \Illuminate\Support\Str::title(str_replace(['_', '-'], ' ', $resource));
                if (!isset($routesByGroup[$resourceName])) {
                    $routesByGroup[$resourceName] = [];
                }
                $routesByGroup[$resourceName][] = $routeName;
            }
        }
        // Get users with this role
        $users = $item->users()->paginate(10);
        
        return view("{$this->viewPath}.show", compact('item', 'routesByGroup', 'users'));
    }
}