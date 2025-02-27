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
            <form action="{{ route('configs.update', $config->route) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Atslēga</label>
                    <div class="text-gray-900 text-lg">{{ $config->route }}</div>
                </div>

                @php
                    $configData = json_decode($config->data, true);
                    $useDynamicEditor = false;
                    $configSections = [];
                    
                    // Check if this config should use the dynamic editor
                    foreach ($configData as $sectionKey => $sectionItems) {
                        if (is_array($sectionItems) && !empty($sectionItems) && isset($sectionItems[0]['name']) && isset($sectionItems[0]['fields'])) {
                            $useDynamicEditor = true;
                            $configSections[$sectionKey] = $sectionItems;
                        }
                    }
                @endphp

                @if($useDynamicEditor)
                    @foreach($configSections as $sectionKey => $sectionItems)
                    <div class="space-y-6">
                        <div class="border-t pt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ ucfirst(str_replace('_', ' ', $sectionKey)) }} lauki
                            </label>
                            <div class="text-sm text-gray-500 mb-4">
                                Šie lauki tiks automātiski pievienoti jauniem ierakstiem
                            </div>
                            <div id="{{ $sectionKey }}-items" class="space-y-4" data-section-key="{{ $sectionKey }}">
                                @foreach($sectionItems as $index => $item)
                                    <div class="config-item bg-gray-50 p-4 rounded-lg relative" data-item-index="{{ $index }}">
                                        <button type="button" 
                                                onclick="removeConfigItem(this)" 
                                                class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <input type="text" 
                                                   name="data[{{ $sectionKey }}][{{ $index }}][name]"
                                                   value="{{ $item['name'] }}"
                                                   class="px-3 py-2 border rounded-lg"
                                                   placeholder="Objekta nosaukums"
                                                   required>
                                            
                                            <select name="data[{{ $sectionKey }}][{{ $index }}][background_color]"
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
                                                           name="data[{{ $sectionKey }}][{{ $index }}][fields][{{ $fieldIndex }}][key]"
                                                           value="{{ $field['key'] }}"
                                                           class="px-3 py-2 border rounded-lg"
                                                           placeholder="Atslēga"
                                                           required>
                                                    <input type="text" 
                                                           name="data[{{ $sectionKey }}][{{ $index }}][fields][{{ $fieldIndex }}][value]"
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
                                    onclick="addConfigItem('{{ $sectionKey }}')" 
                                    class="mt-4 bg-green-500 text-white px-4 py-2 rounded-lg">
                                Pievienot objektu
                            </button>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="mb-6">
                        <label for="data" class="block text-sm font-medium text-gray-700 mb-2">Vērtība</label>
                        <textarea name="data" 
                                  id="data" 
                                  rows="10" 
                                  class="w-full px-3 py-2 border rounded-lg font-mono"
                                  required>{{ json_encode(json_decode($config->data), JSON_PRETTY_PRINT) }}</textarea>
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
// Store the item counts for each section
const itemCounts = {};

@if($useDynamicEditor)
    @foreach($configSections as $sectionKey => $sectionItems)
        itemCounts['{{ $sectionKey }}'] = {{ count($sectionItems) }};
    @endforeach
@endif

function addConfigItem(sectionKey) {
    const container = document.getElementById(`${sectionKey}-items`);
    const index = itemCounts[sectionKey];
    
    const newItem = document.createElement('div');
    newItem.className = 'config-item bg-gray-50 p-4 rounded-lg relative';
    newItem.setAttribute('data-item-index', index);
    
    newItem.innerHTML = `
        <button type="button" 
                onclick="removeConfigItem(this)" 
                class="absolute top-2 right-2 text-red-500 hover:text-red-700">
            <i class="fas fa-trash"></i>
        </button>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <input type="text" 
                   name="data[${sectionKey}][${index}][name]"
                   class="px-3 py-2 border rounded-lg"
                   placeholder="Objekta nosaukums"
                   required>
            
            <select name="data[${sectionKey}][${index}][background_color]"
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
                       name="data[${sectionKey}][${index}][fields][0][key]"
                       class="px-3 py-2 border rounded-lg"
                       placeholder="Atslēga"
                       required>
                <input type="text" 
                       name="data[${sectionKey}][${index}][fields][0][value]"
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
    itemCounts[sectionKey]++;
}

function removeConfigItem(button) {
    const item = button.closest('.config-item');
    item.remove();
    
    // Re-index remaining items in this section
    reindexItems(item.closest('[id$="-items"]'));
}

function reindexItems(container) {
    if (!container) return;
    
    const sectionKey = container.getAttribute('data-section-key');
    const items = container.querySelectorAll('.config-item');
    
    items.forEach((item, index) => {
        item.setAttribute('data-item-index', index);
        
        // Update name attributes for all inputs within this item
        const inputs = item.querySelectorAll('input, select');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const newName = name.replace(
                    new RegExp(`data\\[${sectionKey}\\]\\[\\d+\\]`), 
                    `data[${sectionKey}][${index}]`
                );
                input.setAttribute('name', newName);
            }
        });
        
        // Update field indexes within this item
        reindexFields(item, sectionKey, index);
    });
    
    // Update the count for this section
    itemCounts[sectionKey] = items.length;
}

function addField(button) {
    const fieldsContainer = button.previousElementSibling;
    const item = button.closest('.config-item');
    const container = item.closest('[id$="-items"]');
    const sectionKey = container.getAttribute('data-section-key');
    const itemIndex = item.getAttribute('data-item-index');
    const fieldIndex = fieldsContainer.children.length;

    const newField = document.createElement('div');
    newField.className = 'field-row grid grid-cols-2 gap-4 mb-2 relative';
    newField.innerHTML = `
        <button type="button" 
                onclick="removeField(this)" 
                class="absolute top-1 right-0 text-red-500 hover:text-red-700">
            <i class="fas fa-times"></i>
        </button>
        <input type="text" 
               name="data[${sectionKey}][${itemIndex}][fields][${fieldIndex}][key]"
               class="px-3 py-2 border rounded-lg"
               placeholder="Atslēga"
               required>
        <input type="text" 
               name="data[${sectionKey}][${itemIndex}][fields][${fieldIndex}][value]"
               class="px-3 py-2 border rounded-lg"
               placeholder="Vērtība">
    `;
    fieldsContainer.appendChild(newField);
}

function removeField(button) {
    const fieldRow = button.closest('.field-row');
    const fieldsContainer = fieldRow.parentNode;
    const item = button.closest('.config-item');
    const container = item.closest('[id$="-items"]');
    const sectionKey = container.getAttribute('data-section-key');
    const itemIndex = item.getAttribute('data-item-index');
    
    fieldRow.remove();
    
    // Re-index remaining fields in this item
    reindexFields(item, sectionKey, itemIndex);
}

function reindexFields(item, sectionKey, itemIndex) {
    const fieldsContainer = item.querySelector('.fields-container');
    const fields = fieldsContainer.querySelectorAll('.field-row');
    
    fields.forEach((field, fieldIndex) => {
        const inputs = field.querySelectorAll('input');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const newName = name.replace(
                    new RegExp(`data\\[${sectionKey}\\]\\[${itemIndex}\\]\\[fields\\]\\[\\d+\\]`), 
                    `data[${sectionKey}][${itemIndex}][fields][${fieldIndex}]`
                );
                input.setAttribute('name', newName);
            }
        });
    });
}
</script>
@endsection