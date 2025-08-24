@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-gray-800 dark:text-gray-200">Редактировать задачу #{{ $todo->id }}</h1>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('todos.update', ['schemaName' => request()->route('schemaName'), 'todo' => $todo->id]) }}" method="POST">
                @csrf
                @method('PUT')
                @include('todos.partials.form')
                
                <div class="flex items-center justify-end mt-6">
                    <x-button type="submit">
                        Сохранить
                    </x-button>
                    <x-button :href="route('todos.index', ['schemaName' => request()->route('schemaName')])" variant="secondary" class="ml-2">
                        Отмена
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection