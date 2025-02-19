@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold mb-8">Create New Client</h1>
    <form action="{{ route('clients.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Name</label>
            <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label for="surname" class="block text-gray-700">Surname</label>
            <input type="text" name="surname" id="surname" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label for="identification_number" class="block text-gray-700">Identification Number</label>
            <input type="text" name="identification_number" id="identification_number" class="w-full px-4 py-2 border rounded-lg" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Data Objects</label>
            <div id="data-objects-container">
                <div class="data-object-group mb-4 p-4 border rounded-lg">
                    <div class="mb-2">
                        <label class="block text-gray-700">Object Name</label>
                        <input type="text" name="data[0][object_name]" placeholder="Object Name" class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                    <div class="object-data-container">
                        <div class="object-data-item mb-2">
                            <div class="flex space-x-2">
                                <input type="text" name="data[0][items][0][key]" placeholder="Key" class="w-1/4 px-4 py-2 border rounded-lg" required>
                                <input type="text" name="data[0][items][0][value]" placeholder="Value" class="w-3/4 px-4 py-2 border rounded-lg" required>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-2">
                        <button type="button" class="add-object-data bg-blue-500 text-white px-2 py-1 rounded">Add Key-Value</button>
                        <button type="button" class="remove-data-object bg-red-500 text-white px-2 py-1 rounded">Remove Object</button>
                    </div>
                </div>
            </div>
            <button type="button" id="add-data-object" class="bg-green-600 text-white px-4 py-2 rounded-lg mt-2">Add Data Object</button>
        </div>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg">Create Client</button>
    </form>
</div>

<script>
    // Add new data object
    document.getElementById('add-data-object').addEventListener('click', function () {
        const container = document.getElementById('data-objects-container');
        const objectCount = container.querySelectorAll('.data-object-group').length;
        const newObject = document.createElement('div');
        newObject.classList.add('data-object-group', 'mb-4', 'p-4', 'border', 'rounded-lg');
        newObject.innerHTML = `
            <div class="mb-2">
                <label class="block text-gray-700">Object Name</label>
                <input type="text" name="data[${objectCount}][object_name]" placeholder="Object Name" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="object-data-container">
                <div class="object-data-item mb-2">
                    <div class="flex space-x-2">
                        <input type="text" name="data[${objectCount}][items][0][key]" placeholder="Key" class="w-1/4 px-4 py-2 border rounded-lg" required>
                        <input type="text" name="data[${objectCount}][items][0][value]" placeholder="Value" class="w-3/4 px-4 py-2 border rounded-lg" required>
                    </div>
                </div>
            </div>
            <div class="flex justify-between mt-2">
                <button type="button" class="add-object-data bg-blue-500 text-white px-2 py-1 rounded">Add Key-Value</button>
                <button type="button" class="remove-data-object bg-red-500 text-white px-2 py-1 rounded">Remove Object</button>
            </div>
        `;
        container.appendChild(newObject);
        attachEventListeners(newObject);
    });

    // Attach event listeners to initial and dynamically added elements
    function attachEventListeners(container) {
        // Add key-value pair to object
        container.querySelector('.add-object-data').addEventListener('click', function () {
            const objectDataContainer = this.closest('.data-object-group').querySelector('.object-data-container');
            const itemCount = objectDataContainer.querySelectorAll('.object-data-item').length;
            const objectIndex = Array.from(document.querySelectorAll('.data-object-group')).indexOf(this.closest('.data-object-group'));
            
            const newItem = document.createElement('div');
            newItem.classList.add('object-data-item', 'mb-2');
            newItem.innerHTML = `
                <div class="flex space-x-2">
                    <input type="text" name="data[${objectIndex}][items][${itemCount}][key]" placeholder="Key" class="w-1/4 px-4 py-2 border rounded-lg" required>
                    <input type="text" name="data[${objectIndex}][items][${itemCount}][value]" placeholder="Value" class="w-3/4 px-4 py-2 border rounded-lg" required>
                </div>
            `;
            objectDataContainer.appendChild(newItem);
        });

        // Remove data object
        container.querySelector('.remove-data-object').addEventListener('click', function () {
            this.closest('.data-object-group').remove();
        });
    }

    // Attach listeners to initial elements
    document.querySelectorAll('.data-object-group').forEach(attachEventListeners);
</script>
@endsection