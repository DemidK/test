@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-0">Rediģēt konfigurāciju</h1>
            <a href="{{ route('configs.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i> Atpakaļ
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md">
            <form action="{{ route('configs.update', $config->key) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Atslēga</label>
                    <div class="text-gray-900 text-lg">{{ $config->key }}</div>
                </div>

                @if($config->key === 'client_data_objects')
                    <div class="space-y-6">
                        <div class="border-t pt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Klienta izveides lauki
                            </label>
                            <div class="text-sm text-gray-500 mb-4">
                                Šie lauki tiks automātiski pievienoti jauniem klientiem
                            </div>
                            <div id="client-create-items" class="space-y-4">
                                @php 
                                $existingItems = json_decode($config->value, true)['client_create'] ?? [];
                                @endphp

                                @foreach($existingItems as $index => $item)
                                    <div class="client-create-item bg-gray-50 p-4 rounded-lg relative">
                                        <button type="button" 
                                                onclick="removeClientCreateItem(this)" 
                                                class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <input type="text" 
                                                   name="value[client_create][{{ $index }}][name]"
                                                   value="{{ $item['name'] }}"
                                                   class="px-3 py-2 border rounded-lg"
                                                   placeholder="Objekta nosaukums"
                                                   required>
                                            
                                            <select name="value[client_create][{{ $index }}][background_color]"
                                                    class="px-3 py-2 border rounded-lg">
                                                <option value="bg-gray-50" {{ ($item['background_color'] ?? '') === 'bg-gray-50' ? 'selected' : '' }}>Pelēks</option>
                                                <option value="bg-blue-50" {{ ($item['background_color'] ?? '') === 'bg-blue-50' ? 'selected' : '' }}>Zils</option>
                                                <option value="bg-green-50" {{ ($item['background_color'] ?? '') === 'bg-green-50' ? 'selected' : '' }}>Zaļš</option>
                                                <option value="bg-red-50" {{ ($item['background_color'] ?? '') === 'bg-red-50' ? 'selected' : '' }}>Sarkans</option>
                                            </select>
                                        </div>
                                        
                                        <div class="fields-container">
                                            @foreach($item['fields'] as $fieldIndex => $field)
                                                <div class="field-row grid grid-cols-2 gap-4 mb-2 relative">
                                                    <button type="button" 
                                                            onclick="removeField(this)" 
                                                            class="absolute top-1 right-0 text-red-500 hover:text-red-700">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <input type="text" 
                                                           name="value[client_create][{{ $index }}][fields][{{ $fieldIndex }}][key]"
                                                           value="{{ $field['key'] }}"
                                                           class="px-3 py-2 border rounded-lg"
                                                           placeholder="Atslēga"
                                                           required>
                                                    <input type="text" 
                                                           name="value[client_create][{{ $index }}][fields][{{ $fieldIndex }}][value]"
                                                           value="{{ $field['value'] }}"
                                                           class="px-3 py-2 border rounded-lg"
                                                           placeholder="Vērtība">
                                                </div>
                                            @endforeach
                                        </div>

                                        <button type="button" 
                                                onclick="addField(this)" 
                                                class="mt-2 bg-blue-500 text-white px-3 py-1 rounded-lg">
                                            Pievienot lauku
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" 
                                    onclick="addClientCreateItem()" 
                                    class="mt-4 bg-green-500 text-white px-4 py-2 rounded-lg">
                                Pievienot objektu
                            </button>
                        </div>
                    </div>
                @else
                    <div class="mb-6">
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-2">Vērtība</label>
                        <textarea name="value" 
                                  id="value" 
                                  rows="10" 
                                  class="w-full px-3 py-2 border rounded-lg font-mono"
                                  required>{{ json_encode(json_decode($config->value), JSON_PRETTY_PRINT) }}</textarea>
                    </div>
                @endif

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Saglabāt
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let clientCreateItemIndex = {{ count($existingItems) }};

function addClientCreateItem() {
    const container = document.getElementById('client-create-items');
    const newItem = document.createElement('div');
    newItem.className = 'client-create-item bg-gray-50 p-4 rounded-lg relative';
    newItem.innerHTML = `
        <button type="button" 
                onclick="removeClientCreateItem(this)" 
                class="absolute top-2 right-2 text-red-500 hover:text-red-700">
            <i class="fas fa-trash"></i>
        </button>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <input type="text" 
                   name="value[client_create][${clientCreateItemIndex}][name]"
                   class="px-3 py-2 border rounded-lg"
                   placeholder="Objekta nosaukums"
                   required>
            
            <select name="value[client_create][${clientCreateItemIndex}][background_color]"
                    class="px-3 py-2 border rounded-lg">
                <option value="bg-gray-50">Pelēks</option>
                <option value="bg-blue-50">Zils</option>
                <option value="bg-green-50">Zaļš</option>
                <option value="bg-red-50">Sarkans</option>
            </select>
        </div>
        
        <div class="fields-container">
            <div class="field-row grid grid-cols-2 gap-4 mb-2 relative">
                <button type="button" 
                        onclick="removeField(this)" 
                        class="absolute top-1 right-0 text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
                <input type="text" 
                       name="value[client_create][${clientCreateItemIndex}][fields][0][key]"
                       class="px-3 py-2 border rounded-lg"
                       placeholder="Atslēga"
                       required>
                <input type="text" 
                       name="value[client_create][${clientCreateItemIndex}][fields][0][value]"
                       class="px-3 py-2 border rounded-lg"
                       placeholder="Vērtība">
            </div>
        </div>

        <button type="button" 
                onclick="addField(this)" 
                class="mt-2 bg-blue-500 text-white px-3 py-1 rounded-lg">
            Pievienot lauku
        </button>
    `;
    container.appendChild(newItem);
    clientCreateItemIndex++;
}

function removeClientCreateItem(button) {
    const item = button.closest('.client-create-item');
    item.remove();
}

function addField(button) {
    const fieldsContainer = button.previousElementSibling;
    const item = button.closest('.client-create-item');
    const fieldIndex = fieldsContainer.children.length;
    const clientCreateIndex = Array.from(item.parentNode.children).indexOf(item);

    const newField = document.createElement('div');
    newField.className = 'field-row grid grid-cols-2 gap-4 mb-2 relative';
    newField.innerHTML = `
        <button type="button" 
                onclick="removeField(this)" 
                class="absolute top-1 right-0 text-red-500 hover:text-red-700">
            <i class="fas fa-times"></i>
        </button>
        <input type="text" 
               name="value[client_create][${clientCreateIndex}][fields][${fieldIndex}][key]"
               class="px-3 py-2 border rounded-lg"
               placeholder="Atslēga"
               required>
        <input type="text" 
               name="value[client_create][${clientCreateIndex}][fields][${fieldIndex}][value]"
               class="px-3 py-2 border rounded-lg"
               placeholder="Vērtība">
    `;
    fieldsContainer.appendChild(newField);
}

function removeField(button) {
    const fieldRow = button.closest('.field-row');
    fieldRow.remove();
}
</script>
@endsection