@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <!-- Navigation Bar -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <!-- Back Button -->
                <a href="{{ url()->previous() }}" 
                   class="text-gray-600 hover:text-gray-900 flex items-center gap-2"
                   title="Назад">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Назад</span>
                </a>

                <!-- To List Button -->
                <a href="{{ route('todos.index', ['schemaName' => request()->route('schemaName')]) }}" 
                   class="text-gray-600 hover:text-gray-900 flex items-center gap-2"
                   title="К списку задач">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>Все задачи</span>
                </a>
            </div>

            <div class="flex items-center gap-4">
                @can('todos.edit')
                    <x-button :href="route('todos.edit', ['schemaName' => request()->route('schemaName'), 'todo' => $todo->id])" variant="warning">
                        <i class="fas fa-edit mr-2"></i>
                        Редактировать
                    </x-button>
                @endcan
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            {{-- Todo Header --}}
            <div class="bg-gray-100 p-4">
                <div class="mb-4">
                    <h1 class="text-2xl font-bold text-gray-800">
                        Задача #{{ $todo->id }}: {{ $todo->title }}
                    </h1>
                    <p class="text-sm text-gray-600">
                        Создана: {{ $todo->created_at->format('d.m.Y H:i') }}
                    </p>
                </div>
            </div>

            {{-- Todo Details --}}
            <div class="p-4 border-b">
                <h3 class="font-semibold text-gray-700 mb-4">Детали задачи</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Статус</label>
                        <p class="text-gray-900">{{ \App\Models\Todo::getStatuses()[$todo->status] ?? $todo->status }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Приоритет</label>
                        <p class="text-gray-900">{{ \App\Models\Todo::getPriorities()[$todo->priority] ?? $todo->priority }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Исполнитель</label>
                        <p class="text-gray-900">{{ $todo->assignee->name ?? 'Не назначен' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Автор</label>
                        <p class="text-gray-900">{{ $todo->creator->name ?? 'Неизвестен' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Срок выполнения</label>
                        <p class="text-gray-900">{{ $todo->due_date ? $todo->due_date->format('d.m.Y') : 'Не указан' }}</p>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="p-4">
                <h3 class="font-semibold text-gray-700 mb-4">Описание</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-800">{!! nl2br(e($todo->description)) !!}</p>
                </div>
            </div>
        </div>

        {{-- Comments Section --}}
        <div class="mt-8">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">Обсуждение</h2>

            {{-- Existing Comments --}}
            <div class="space-y-6">
                @forelse($todo->comments as $comment)
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            {{-- Placeholder for user avatar --}}
                            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center font-bold text-gray-600">
                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="bg-gray-100 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <p class="font-semibold text-gray-900">{{ $comment->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $comment->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                                <p class="text-gray-700 mt-2">{!! nl2br(e($comment->content)) !!}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-4">
                        Комментариев пока нет.
                    </div>
                @endforelse
            </div>

            {{-- Add Comment Form --}}
            <div class="mt-6">
                {{-- Право на комментирование совпадает с правом на просмотр задачи --}}
                @can('show', $todo)
                    <form action="{{ route('todos.comments.store', ['schemaName' => request()->route('schemaName'), 'todo' => $todo->id]) }}" method="POST">
                        @csrf
                        <div class="bg-white rounded-lg shadow-md p-4">
                            <label for="content" class="block text-gray-700 mb-2 font-semibold">Добавить комментарий</label>
                            <textarea name="content" id="content" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Напишите ваш комментарий..." required>{{ old('content') }}</textarea>
                            <div class="flex justify-end mt-4">
                                <x-button type="submit">Отправить</x-button>
                            </div>
                        </div>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection