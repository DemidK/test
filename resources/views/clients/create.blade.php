@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold">Create New Client</h1>
            <a href="{{ route('clients.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form action="{{ route('clients.store') }}" method="POST" class="bg-white rounded-lg shadow-md">
            @csrf
            <div class="p-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" id="name" class="w-full px-3 py-2 border rounded-lg" required>
                    </div>
                    <div>
                        <label for="surname" class="block text-sm font-medium text-gray-700 mb-1">Surname</label>
                        <input type="text" name="surname" id="surname" class="w-full px-3 py-2 border rounded-lg" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="identification_number" class="block text-sm font-medium text-gray-700 mb-1">Identification Number</label>
                    <input type="text" name="identification_number" id="identification_number" class="w-full px-3 py-2 border rounded-lg" required>
                </div>

                <!-- Data Objects Section -->
                <div id="data-objects-container">
                    <div class="data-object bg-gray-50 p-4 rounded-lg mb-4">
                        <h3 class="text-lg font-medium mb-4">Data Object 1</h3>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Object Name</label>
                            <input type="text" name="data[0][object_name]" class="w-full px-3 py-2 border rounded-lg" required>
                        </div>

                        <div class="key-value-pairs">
                            <div class="key-value-pair mb-4">
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
                </div>

                <button type="button" onclick="addDataObject()" class="bg-green-500 text-white px-4 py-2 rounded-lg">
                    Add Data Object
                </button>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t">
                <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Create Client
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let objectCount = 1;

function addDataObject() {
    const container = document.getElementById('data-objects-container');
    const newObject = document.createElement('div');
    newObject.className = 'data-object bg-gray-50 p-4 rounded-lg mb-4';
    newObject.innerHTML = `
        <h3 class="text-lg font-medium mb-4">Data Object ${objectCount + 1}</h3>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Object Name</label>
            <input type="text" name="data[${objectCount}][object_name]" class="w-full px-3 py-2 border rounded-lg" required>
        </div>

        <div class="key-value-pairs">
            <div class="key-value-pair mb-4">
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
    newPair.className = 'key-value-pair mb-4';
    newPair.innerHTML = `
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
</script>
@endsection