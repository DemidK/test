<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoCommentController extends Controller
{
    public function store(Request $request, Todo $todo)
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

        // Параметр 'schemaName' будет подставлен автоматически благодаря URL::defaults.
        return redirect()->route('todos.show', ['todo' => $todo->id])
                         ->with('success', 'Комментарий успешно добавлен.');
    }
}