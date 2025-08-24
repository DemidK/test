<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoCommentController extends Controller
{
    public function store(Request $request, $schemaName, Todo $todo)
    {
        // Право 'todos.show' используется как допущение, что если пользователь
        // может видеть задачу, он может ее и комментировать.
        // Вы можете создать отдельное право 'todos.comment' при необходимости.
        $this->authorize('show', $todo);

        $request->validate([
            'content' => 'required|string',
        ]);

        $todo->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        return redirect()->route('todos.show', ['schemaName' => $schemaName, 'todo' => $todo->id])
                         ->with('success', 'Комментарий успешно добавлен.');
    }
}