@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold">Rediģēt klientu</h1>
            <a href="{{ route('clients.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('clients.update', $client->id) }}" method="POST" class="bg-white rounded-lg shadow-md">
            @csrf
            @method('PUT')
            <div class="p-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Vārds / Nosaukums</label>
                        <input type="text" name="name" id="name" value="{{ $client->name }}" class="w-full px-3 py-2 border rounded-lg" required>
                    </div>
                    <div>
                        <label for="identification_number" class="block text-sm font-medium text-gray-700 mb-1">Identification Number</label>
                        <input type="text" name="identification_number" id="identification_number" value="{{ $client->identification_number }}" class="w-full px-3 py-2 border rounded-lg" required>
                    </div>
                </div>

                <!-- Data Objects Section -->
                <div id="data-objects-container">
                    @if($formattedData)
                        @foreach($formattedData as $index => $dataObject)
                            <div class="data-object bg-gray-50 p-4 rounded-lg mb-4 relative">
                                <!-- Delete object button -->
                                <button type="button" 
                                        onclick="deleteObject(this)" 
                                        class="absolute top-2 right-2 text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <h3 class="text-lg font-medium mb-4">Data Object {{ $index + 1 }}</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Object Name</label>
                                    <input type="text" name="data[{{ $index }}][object_name]" value="{{ $dataObject['object_name'] }}" class="w-full px-3 py-2 border rounded-lg" required>
                                </div>

                                <div class="key-value-pairs">
                                    @foreach($dataObject['items'] as $itemIndex => $item)
                                        <div class="key-value-pair mb-4 relative">
                                            <!-- Delete key-value pair button -->
                                            <button type="button" 
                                                    onclick="deleteKeyValuePair(this)" 
                                                    class="absolute top-0 right-0 text-red-600 hover:text-red-800">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Key</label>
                                                    <input type="text" name="data[{{ $index }}][items][{{ $itemIndex }}][key]" value="{{ $item['key'] }}" class="w-full px-3 py-2 border rounded-lg" required>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                                                    <input type="text" name="data[{{ $index }}][items][{{ $itemIndex }}][value]" value="{{ $item['value'] }}" class="w-full px-3 py-2 border rounded-lg" required>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" onclick="addKeyValuePair(this)" class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-2">
                                    Add Key-Value Pair
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="data-object bg-gray-50 p-4 rounded-lg mb-4 relative">
                            <!-- Delete object button -->
                            <button type="button" 
                                    onclick="deleteObject(this)" 
                                    class="absolute top-2 right-2 text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <h3 class="text-lg font-medium mb-4">Data Object 1</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Object Name</label>
                                <input type="text" name="data[0][object_name]" class="w-full px-3 py-2 border rounded-lg" required>
                            </div>

                            <div class="key-value-pairs">
                                <div class="key-value-pair mb-4 relative">
                                    <!-- Delete key-value pair button -->
                                    <button type="button" 
                                            onclick="deleteKeyValuePair(this)" 
                                            class="absolute top-0 right-0 text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Key</label>
                                            <input type="text" name="data[0][items][0][key]" class="w-full px-3 py-2 border rounded-lg" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                                            <input type="text" name="data[0][items][0][value]" class="w-full px-3 py-2 border rounded-lg" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="addKeyValuePair(this)" class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-2">
                                Add Key-Value Pair
                            </button>
                        </div>
                    @endif
                </div>

                <button type="button" onclick="addDataObject()" class="bg-green-500 text-white px-4 py-2 rounded-lg">
                    Add Data Object
                </button>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t">
                <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Update Client
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let objectCount = {{ $formattedData ? count($formattedData) : 1 }};

function addDataObject() {
    const container = document.getElementById('data-objects-container');
    const newObject = document.createElement('div');
    newObject.className = 'data-object bg-gray-50 p-4 rounded-lg mb-4 relative';
    newObject.innerHTML = `
        <!-- Delete object button -->
        <button type="button" 
                onclick="deleteObject(this)" 
                class="absolute top-2 right-2 text-red-600 hover:text-red-800">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <h3 class="text-lg font-medium mb-4">Data Object ${objectCount + 1}</h3>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Object Name</label>
            <input type="text" name="data[${objectCount}][object_name]" class="w-full px-3 py-2 border rounded-lg" required>
        </div>

        <div class="key-value-pairs">
            <div class="key-value-pair mb-4 relative">
                <!-- Delete key-value pair button -->
                <button type="button" 
                        onclick="deleteKeyValuePair(this)" 
                        class="absolute top-0 right-0 text-red-600 hover:text-red-800">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Key</label>
                        <input type="text" name="data[${objectCount}][items][0][key]" class="w-full px-3 py-2 border rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                        <input type="text" name="data[${objectCount}][items][0][value]" class="w-full px-3 py-2 border rounded-lg" required>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" onclick="addKeyValuePair(this)" class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-2">
            Add Key-Value Pair
        </button>
    `;
    container.appendChild(newObject);
    objectCount++;
}

function addKeyValuePair(button) {
    const dataObject = button.closest('.data-object');
    const keyValuePairs = dataObject.querySelector('.key-value-pairs');
    const pairCount = keyValuePairs.children.length;
    const objectIndex = Array.from(dataObject.parentNode.children).indexOf(dataObject);

    const newPair = document.createElement('div');
    newPair.className = 'key-value-pair mb-4 relative';
    newPair.innerHTML = `
        <!-- Delete key-value pair button -->
        <button type="button" 
                onclick="deleteKeyValuePair(this)" 
                class="absolute top-0 right-0 text-red-600 hover:text-red-800">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Key</label>
                <input type="text" name="data[${objectIndex}][items][${pairCount}][key]" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                <input type="text" name="data[${objectIndex}][items][${pairCount}][value]" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
        </div>
    `;
    keyValuePairs.appendChild(newPair);
}

function deleteObject(button) {
    const dataObject = button.closest('.data-object');
    dataObject.remove();
    reindexObjects();
}

function deleteKeyValuePair(button) {
    const keyValuePair = button.closest('.key-value-pair');
    keyValuePair.remove();
    reindexKeyValuePairs(keyValuePair.closest('.data-object'));
}

function reindexObjects() {
    const container = document.getElementById('data-objects-container');
    const objects = container.querySelectorAll('.data-object');
    
    objects.forEach((object, objectIndex) => {
        // Update object name
        const objectName = object.querySelector('input[name*="[object_name]"]');
        objectName.name = `data[${objectIndex}][object_name]`;
        
        // Update object title
        const title = object.querySelector('h3');
        title.textContent = `Data Object ${objectIndex + 1}`;
        
        // Update all key-value pairs within this object
        const keyValuePairs = object.querySelectorAll('.key-value-pair');
        keyValuePairs.forEach((pair, pairIndex) => {
            const keyInput = pair.querySelector('input[name*="[key]"]');
            const valueInput = pair.querySelector('input[name*="[value]"]');
            
            keyInput.name = `data[${objectIndex}][items][${pairIndex}][key]`;
            valueInput.name = `data[${objectIndex}][items][${pairIndex}][value]`;
        });
    });
    
    objectCount = objects.length;
}

function reindexKeyValuePairs(dataObject) {
    const keyValuePairs = dataObject.querySelectorAll('.key-value-pair');
    const objectIndex = Array.from(dataObject.parentNode.children).indexOf(dataObject);
    
    keyValuePairs.forEach((pair, pairIndex) => {
        const keyInput = pair.querySelector('input[name*="[key]"]');
        const valueInput = pair.querySelector('input[name*="[value]"]');
        
        keyInput.name = `data[${objectIndex}][items][${pairIndex}][key]`;
        valueInput.name = `data[${objectIndex}][items][${pairIndex}][value]`;
    });
}
</script>
@endsection