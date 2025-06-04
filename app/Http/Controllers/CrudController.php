<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\NavLink;

abstract class CrudController extends Controller
{
    protected $model;
    protected $viewPath;
    protected $routePrefix;
    protected $searchableFields = [];
    protected $sortableFields = [];
    protected $defaultSort = ['created_at', 'desc'];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $perPage = 10;

    public function navLinks()
    {
        return NavLink::orderBy('position')->get();
    }

    public function index(Request $request)
    {
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

        $items = $query->paginate($this->perPage);
        $items->appends($request->except('page'));
        
        $navLinks = NavLink::orderBy('position')->get();
        
        return view("{$this->viewPath}.index", [
            'navLinks' => $navLinks,
            'items' => $items,
            'search' => $request->search,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder
        ]);
    }

    public function create()
    {
        $navLinks = NavLink::orderBy('position')->get();
        return view("{$this->viewPath}.create", compact('navLinks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules, $this->validationMessages);
        
        try {
            $item = $this->storeItem($validated);
            return redirect()->route("{$this->routePrefix}.show", $item)
                ->with('success', 'Item created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $item = $this->model::findOrFail($id);
        $navLinks = NavLink::orderBy('position')->get();
        return view("{$this->viewPath}.show", compact('item', 'navLinks'));
    }

    public function edit($id)
    {
        $item = $this->model::findOrFail($id);
        $navLinks = NavLink::orderBy('position')->get();
        return view("{$this->viewPath}.edit", compact('item', 'navLinks'));
    }

    public function update(Request $request, $id)
    {
        $item = $this->model::findOrFail($id);
        $validated = $request->validate($this->getValidationRules($id));
        
        try {
            $this->updateItem($item, $validated);
            return redirect()->route("{$this->routePrefix}.show", $item)
                ->with('success', 'Item updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $item = $this->model::findOrFail($id);
        $item->delete();
        
        return redirect()->route("{$this->routePrefix}.index")
            ->with('success', 'Item deleted successfully');
    }

    protected function storeItem(array $validated)
    {
        return $this->model::create($validated);
    }

    protected function updateItem($item, array $validated)
    {
        return $item->update($validated);
    }

    protected function getValidationRules($id = null): array
    {
        return $this->validationRules;
    }
}