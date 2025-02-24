@props(['item', 'index', 'isEdit'])

<div x-data>
    <button type="button" 
            @click="$parent.deleteObject($el)" 
            class="absolute top-2 right-2 text-red-600 hover:text-red-800">
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
    </svg>
</button>

<h3 class="text-lg font-medium mb-4">
    {{ $isEdit ? "Data Object " . ($index + 1) : $item['name'] }}
</h3>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Object Name</label>
    <input type="text" 
           name="data[{{ $index }}][object_name]" 
           value="{{ $isEdit ? $item['object_name'] : $item['name'] }}" 
           class="w-full px-3 py-2 border rounded-lg">
</div>

<div class="key-value-pairs">
    @foreach (($isEdit ? $item['items'] : $item['fields']) as $j => $field)
        <div class="key-value-pair mb-4 relative">
            <button type="button" 
                    @click="deleteKeyValuePair($el)" 
                    class="absolute top-0 right-0 text-red-600 hover:text-red-800">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>

            <div class="grid grid-cols-4 gap-4">
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Key</label>
                    <input type="text" 
                           name="data[{{ $index }}][items][{{ $j }}][key]" 
                           value="{{ $field['key'] }}"
                           class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                    <input type="text" 
                           name="data[{{ $index }}][items][{{ $j }}][value]"
                           value="{{ $field['value'] ?? '' }}"
                           class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
        </div>
    @endforeach
</div>

    <button type="button" 
            @click="$parent.addKeyValuePair($el)" 
            class="w-full sm:w-auto bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
        Add Key-Value Pair
    </button>
</div>