<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Проверка прав 'todos.index' выполняется middleware 'permission'
        
        $query = Todo::with(['assignee', 'creator']);

        // Логика фильтрации
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $todos = $query->orderBy('created_at', 'desc')->paginate(15);
        $users = User::orderBy('name')->get();

        return view('todos.index', [
            'todos' => $todos,
            'users' => $users,
            'statuses' => Todo::getStatuses(),
            'priorities' => Todo::getPriorities(),
            'filters' => $request->only(['status', 'priority', 'user_id']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Проверка прав 'todos.create'
        $users = User::orderBy('name')->get();
        return view('todos.create', [
            'users' => $users,
            'statuses' => Todo::getStatuses(),
            'priorities' => Todo::getPriorities(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Проверка прав 'todos.create'
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:' . implode(',', array_keys(Todo::getStatuses())),
            'priority' => 'required|in:' . implode(',', array_keys(Todo::getPriorities())),
            'user_id' => 'nullable|exists:user,id',
            'due_date' => 'nullable|date',
        ]);

        $validated['creator_id'] = Auth::id();

        Todo::create($validated);

        $schemaName = $request->route('schemaName');
        return redirect()->route('todos.index', ['schemaName' => $schemaName])
                         ->with('success', 'Задача успешно создана.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $schemaName, Todo $todo)
    {
        // Проверка прав 'todos.view'
        $todo->load(['assignee', 'creator', 'comments.user']);
        return view('todos.show', compact('todo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $schemaName, Todo $todo)
    {
        // Проверка прав 'todos.edit'
        $users = User::orderBy('name')->get();
        return view('todos.edit', [
            'todo' => $todo,
            'users' => $users,
            'statuses' => Todo::getStatuses(),
            'priorities' => Todo::getPriorities(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $schemaName, Todo $todo)
    {
        // Проверка прав 'todos.edit'
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:' . implode(',', array_keys(Todo::getStatuses())),
            'priority' => 'required|in:' . implode(',', array_keys(Todo::getPriorities())),
            'user_id' => 'nullable|exists:user,id',
            'due_date' => 'nullable|date',
        ]);

        $todo->update($validated);
        
        return redirect()->route('todos.index', ['schemaName' => $schemaName])
                         ->with('success', 'Задача успешно обновлена.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $schemaName, Todo $todo)
    {
        // Проверка прав 'todos.delete'
        $todo->delete();

        return redirect()->route('todos.index', ['schemaName' => $schemaName])
                         ->with('success', 'Задача успешно удалена.');
    }
}