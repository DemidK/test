@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Список задач</h1>
            @can('todos.create')
                <x-button :href="route('todos.create', ['schemaName' => request()->route('schemaName')])">
                    Создать задачу
                </x-button>
            @endcan
        </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">Ошибка! Что-то пошло не так.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Фильтры --}}
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('todos.index', ['schemaName' => request()->route('schemaName')]) }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="status" class="block text-gray-700 mb-2">Статус:</label>
                <select name="status" id="status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Все</option>
                    @foreach($statuses as $key => $value)
                        <option value="{{ $key }}" {{ ($filters['status'] ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label for="priority" class="block text-gray-700 mb-2">Приоритет:</label>
                <select name="priority" id="priority" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Все</option>
                    @foreach($priorities as $key => $value)
                        <option value="{{ $key }}" {{ ($filters['priority'] ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label for="user_id" class="block text-gray-700 mb-2">Исполнитель:</label>
                <select name="user_id" id="user_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Все</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ ($filters['user_id'] ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 items-end">
                <x-button type="submit">Фильтровать</x-button>
                @if(request()->hasAny(['status', 'priority', 'user_id']))
                    <x-button :href="route('todos.index', ['schemaName' => request()->route('schemaName')])" variant="secondary">Сбросить</x-button>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Название</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Исполнитель</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Приоритет</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Срок</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-gray-700 dark:text-gray-300">
                @forelse($todos as $todo)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-4 whitespace-nowrap">{{ $todo->id }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <a href="{{ route('todos.show', ['schemaName' => request()->route('schemaName'), 'todo' => $todo->id]) }}" class="text-blue-600 hover:text-blue-900">
                                {{ $todo->title }}
                            </a>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ $todo->assignee->name ?? 'Не назначен' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($todo->status == \App\Models\Todo::STATUS_DONE) bg-green-100 text-green-800
                                @elseif($todo->status == \App\Models\Todo::STATUS_IN_PROGRESS) bg-blue-100 text-blue-800
                                @elseif($todo->status == \App\Models\Todo::STATUS_ON_HOLD) bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $statuses[$todo->status] ?? $todo->status }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($todo->priority == \App\Models\Todo::PRIORITY_CRITICAL) bg-red-100 text-red-800
                                @elseif($todo->priority == \App\Models\Todo::PRIORITY_HIGH) bg-orange-100 text-orange-800
                                @elseif($todo->priority == \App\Models\Todo::PRIORITY_MEDIUM) bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $priorities[$todo->priority] ?? $todo->priority }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ $todo->due_date ? $todo->due_date->format('d.m.Y') : 'Не указан' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-right">
                            <div class="flex justify-end space-x-2">
                                @can('todos.edit')
                                    <x-icon-button :href="route('todos.edit', ['schemaName' => request()->route('schemaName'), 'todo' => $todo->id])" variant="warning" title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </x-icon-button>
                                @endcan
                                @can('todos.delete')
                                    <form action="{{ route('todos.destroy', ['schemaName' => request()->route('schemaName'), 'todo' => $todo->id]) }}" method="POST" class="inline" onsubmit="return confirm('Вы уверены, что хотите удалить эту задачу?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-icon-button type="submit" variant="danger" title="Удалить">
                                            <i class="fas fa-trash"></i>
                                        </x-icon-button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">Задач не найдено.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $todos->appends($filters)->links() }}
        </div>
    </div>
</div>
@endsection