<?php

namespace App\Http\Controllers;

use App\Models\CustomTable;
use App\Models\NavLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CustomTableController extends CrudController
{
    public function __construct()
    {
        $this->model = CustomTable::class;
        $this->viewPath = 'custom_tables';
        $this->routePrefix = 'custom-tables';
        $this->searchableFields = ['name', 'display_name', 'description'];
        $this->sortableFields = [
            'name' => 'name',
            'display_name' => 'display_name',
            'created_at' => 'created_at'
        ];
        $this->perPage = 15;
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
        $sortBy = $request->get('sort_by', $this->defaultSort[0] ?? 'created_at');
        $sortOrder = $request->get('sort_order', $this->defaultSort[1] ?? 'desc');
        
        if (isset($this->sortableFields[$sortBy])) {
            $query->orderBy($this->sortableFields[$sortBy], $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $items = $query->paginate($this->perPage);
        $navLinks = $this->navLinks();
        
        return view("{$this->viewPath}.index", compact('items'));
    }

    public function create()
    {
        $navLinks = $this->navLinks();
        return view("{$this->viewPath}.create");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:custom_tables', 'regex:/^[a-z][a-z0-9_]*$/'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'fields' => ['required', 'array'],
            'fields.*.name' => ['required', 'string', 'regex:/^[a-z][a-z0-9_]*$/'],
            'fields.*.type' => ['required', Rule::in(array_keys(CustomTable::$availableTypes))],
            'fields.*.label' => ['required', 'string'],
            'fields.*.required' => ['boolean'],
            'fields.*.default' => ['nullable'],
        ]);

        try {
            DB::beginTransaction();

            // Format fields array
            $fields = [];
            foreach ($validated['fields'] as $field) {
                $fields[$field['name']] = [
                    'type' => $field['type'],
                    'label' => $field['label'],
                    'required' => $field['required'] ?? false,
                    'default' => $field['default'] ?? null,
                ];
            }

            // Create table definition
            $customTable = CustomTable::create([
                'name' => $validated['name'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'],
                'fields' => $fields,
            ]);

            // Create actual database table
            $customTable->createDatabaseTable();

            DB::commit();

            return redirect()
                ->route('custom-tables.show', $customTable)
                ->with('success', 'Table created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Failed to create table: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $item = CustomTable::findOrFail($id);
        $navLinks = $this->navLinks();
        
        try {
            $recordCount = DB::table($item->name)->count();
            $lastRecord = DB::table($item->name)
                           ->orderBy('created_at', 'desc')
                           ->first();
        } catch (\Exception $e) {
            $recordCount = 0;
            $lastRecord = null;
        }

        return view("{$this->viewPath}.show", compact('item', 'recordCount', 'lastRecord'));
    }

    public function edit($id)
    {
        $item = CustomTable::findOrFail($id);
        $navLinks = $this->navLinks();
        return view("{$this->viewPath}.edit", compact('item'));
    }

    public function update(Request $request, $id)
    {
        $customTable = CustomTable::findOrFail($id);

        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        try {
            $customTable->update($validated);

            return redirect()
                ->route('custom-tables.show', $customTable)
                ->with('success', 'Table updated successfully');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to update table: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $customTable = CustomTable::findOrFail($id);

        try {
            DB::beginTransaction();

            // Drop the actual database table
            $customTable->dropDatabaseTable();

            // Delete the table definition
            $customTable->delete();

            DB::commit();

            return redirect()
                ->route('custom-tables.index')
                ->with('success', 'Table deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete table: ' . $e->getMessage()]);
        }
    }

    public function preview($id)
    {
        $customTable = CustomTable::findOrFail($id);
        $modelClass = $customTable->getDynamicModel();
        $items = $modelClass::paginate(10);
        $navLinks = $this->navLinks();

        return view("{$this->viewPath}.preview", compact('customTable', 'items'));
    }

    public function dataIndex($tableName)
    {
        $customTable = CustomTable::where('name', $tableName)->firstOrFail();
        $model = $customTable->getModel();
        $items = $model->newQuery()->paginate(10);
        $navLinks = $this->navLinks();

        return view('custom_tables.data.index', compact('customTable', 'items',));
    }

    public function dataStore(Request $request, $tableName)
    {
        $customTable = CustomTable::where('name', $tableName)->firstOrFail();
        $model = $customTable->getModel();

        // $validated = $request->validate($customTable->generateValidationRules());

        try {
            $item = $model->newQuery()->create($request->input());

            return redirect()
                ->route('custom-tables.data.show', [$tableName, $item->id])
                ->with('success', 'Record created successfully');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to create record: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function dataShow($tableName, $id)
    {
        $customTable = CustomTable::where('name', $tableName)->firstOrFail();
        $model = $customTable->getModel();
        $item = $model->newQuery()->findOrFail($id);
        $navLinks = $this->navLinks();

        return view('custom_tables.data.show', compact('customTable', 'item'));
    }

    public function dataCreate($tableName)
{
    $customTable = CustomTable::where('name', $tableName)->firstOrFail();
    $navLinks = $this->navLinks();
    
    return view('custom_tables.data.create', compact('customTable'));
}

    public function dataEdit($tableName, $id)
    {
        $customTable = CustomTable::where('name', $tableName)->firstOrFail();
        $model = $customTable->getModel();
        $item = $model->newQuery()->findOrFail($id);
        $navLinks = $this->navLinks();

        return view('custom_tables.data.edit', compact('customTable', 'item'));
    }

    public function dataUpdate(Request $request, $tableName, $id)
    {
        $customTable = CustomTable::where('name', $tableName)->firstOrFail();
        $model = $customTable->getModel();
        $item = $model->newQuery()->findOrFail($id);

        // $validated = $request->validate($customTable->generateValidationRules());

        try {
            $item->update($request->input());

            return redirect()
                ->route('custom-tables.data.show', [$tableName, $item->id])
                ->with('success', 'Record updated successfully');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to update record: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function dataDestroy($tableName, $id)
    {
        $customTable = CustomTable::where('name', $tableName)->firstOrFail();
        $model = $customTable->getModel();
        
        try {
            $model->newQuery()->findOrFail($id)->delete();

            return redirect()
                ->route('custom-tables.data.index', $tableName)
                ->with('success', 'Record deleted successfully');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete record: ' . $e->getMessage()]);
        }
    }
}