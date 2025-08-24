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

<div class="mb-4">
    <label for="title" class="block text-gray-700 mb-2">Название</label>
    <input type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" id="title" name="title" value="{{ old('title', $todo->title) }}" required>
</div>

<div class="mb-4">
    <label for="description" class="block text-gray-700 mb-2">Описание</label>
    <textarea class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" id="description" name="description" rows="5">{{ old('description', $todo->description) }}</textarea>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
    <div>
        <label for="user_id" class="block text-gray-700 mb-2">Исполнитель</label>
        <select class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" id="user_id" name="user_id">
            <option value="">Не назначен</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ old('user_id', $todo->user_id) == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="due_date" class="block text-gray-700 mb-2">Срок выполнения</label>
        <input type="date" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" id="due_date" name="due_date" value="{{ old('due_date', $todo->due_date ? $todo->due_date->format('Y-m-d') : '') }}">
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
    <div>
        <label for="status" class="block text-gray-700 mb-2">Статус</label>
        <select class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" id="status" name="status" required>
            @foreach($statuses as $key => $value)
                <option value="{{ $key }}" {{ old('status', $todo->status ?? 'new') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="priority" class="block text-gray-700 mb-2">Приоритет</label>
        <select class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" id="priority" name="priority" required>
            @foreach($priorities as $key => $value)
                <option value="{{ $key }}" {{ old('priority', $todo->priority ?? 'medium') == $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>
</div>