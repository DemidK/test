<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\NavLink;
use App\Services\PermissionService;

class PermissionController extends CrudController
{
    public function __construct(PermissionService $permissionService)
    {
        $this->model = Permission::class;
        $this->viewPath = 'permissions';
        $this->routePrefix = 'permissions';
        $this->permissionService = $permissionService;
        
        $this->searchableFields = ['name', 'slug', 'description'];
        $this->sortableFields = [
            'name' => 'name',
            'slug' => 'slug',
            'type' => 'type',
            'resource' => 'resource',
            'created_at' => 'created_at'
        ];
        
        $this->validationRules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:route,button,ui',
            'resource' => 'nullable|string|max:255',
            'action' => 'nullable|string|max:255',
        ];
    }
    
    /**
     * Display a listing of the permissions.
     */
    public function index(Request $request)
    {
        // Override parent method to group permissions by resource
        $query = $this->model::query();

        // Search functionality
        if ($request->has('search') && !empty($this->searchableFields)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                foreach ($this->searchableFields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$searchTerm}%");
                }
            });
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', $this->defaultSort[0]);
        $sortOrder = $request->get('sort_order', $this->defaultSort[1]);
        
        if (in_array($sortBy, array_keys($this->sortableFields))) {
            $query->orderBy($this->sortableFields[$sortBy], $sortOrder);
        } else {
            $query->orderBy($this->defaultSort[0], $this->defaultSort[1]);
        }

        $items = $query->get();
        
        // Group permissions by resource
        $groupedPermissions = [];
        foreach ($items as $permission) {
            $resource = $permission->resource ?: 'General';
            if (!isset($groupedPermissions[$resource])) {
                $groupedPermissions[$resource] = [];
            }
            $groupedPermissions[$resource][] = $permission;
        }
        
        $navLinks = NavLink::orderBy('position')->get();
        
        return view("{$this->viewPath}.index", [
            'navLinks' => $navLinks,
            'groupedPermissions' => $groupedPermissions,
            'search' => $request->search,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder
        ]);
    }
    
    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules);
        
        // Generate slug from name
        $validated['slug'] = Permission::createSlug($validated['name']);
        
        try {
            $item = $this->storeItem($validated);
            return redirect()->route("{$this->routePrefix}.show", $item)
                ->with('success', 'Permission created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
    
    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, $id)
    {
        $item = $this->model::findOrFail($id);
        $validated = $request->validate($this->validationRules);
        
        // Generate slug from name
        $validated['slug'] = Permission::createSlug($validated['name']);
        
        try {
            $this->updateItem($item, $validated);
            
            // Clear permission cache as it has changed
            $this->permissionService->clearCache('all');
            
            return redirect()->route("{$this->routePrefix}.show", $item)
                ->with('success', 'Permission updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
    
    /**
     * Generate CRUD permissions for a resource
     */
    public function generateCrud(Request $request)
    {
        $request->validate([
            'resource' => 'required|string|max:255'
        ]);
        
        try {
            $resource = $request->resource;
            $permissions = $this->permissionService->createCrudPermissions($resource);
            
            return redirect()->route('permissions.index')
                ->with('success', count($permissions) . ' CRUD permissions created for ' . $resource);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}