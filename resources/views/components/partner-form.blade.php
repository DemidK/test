<!-- partner-form.blade.php -->
@props(['partner' => null, 'action', 'method' => 'POST', 'config' => []])

<div x-data="{
    formData: {
        name: '{{ $partner?->name ?? '' }}',
        identification_number: '{{ $partner?->identification_number ?? '' }}',
        dataObjects: {{ json_encode($partner ? ($formattedData ?? []) : [
            [
                'name' => '',
                'items' => [
                    ['key' => '', 'value' => '']
                ]
            ]
        ]) }}
    },

    addDataObject() {
        this.formData.dataObjects.push({
            name: '',
            items: [{ key: '', value: '' }]
        });
    },

    deleteDataObject(index) {
        if (this.formData.dataObjects.length > 1) {
            this.formData.dataObjects.splice(index, 1);
        }
    },

    addKeyValuePair(objectIndex) {
        this.formData.dataObjects[objectIndex].items.push({
            key: '',
            value: ''
        });
    },

    deleteKeyValuePair(objectIndex, pairIndex) {
        const items = this.formData.dataObjects[objectIndex].items;
        if (items.length > 1) {
            items.splice(pairIndex, 1);
        }
    }
}">
    <form action="{{ $action }}" 
          method="POST" 
          class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @if($method === 'PUT')
            @method('PUT')
        @endif

        <!-- Basic Information -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Vārds / Nosaukums</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       x-model="formData.name"
                       class="w-full px-3 py-2 border rounded-lg" 
                       required>
            </div>
            <div>
                <label for="identification_number" class="block text-sm font-medium text-gray-700 mb-1">Reģistrācijas numurs</label>
                <input type="text" 
                       name="identification_number" 
                       id="identification_number" 
                       x-model="formData.identification_number"
                       class="w-full px-3 py-2 border rounded-lg" 
                       required>
            </div>
        </div>

        <!-- Data Objects Container -->
        <div id="data-objects-container">
            <template x-for="(dataObject, objectIndex) in formData.dataObjects" :key="objectIndex">
                <div class="data-object bg-gray-50 p-4 rounded-lg mb-4 relative">
                    <!-- Delete Object Button -->
                    <button type="button" 
                            @click="deleteDataObject(objectIndex)"
                            x-show="formData.dataObjects.length > 1"
                            class="absolute top-2 right-2 text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Object Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sadaļas nosaukums</label>
                        <input type="text" 
                               x-model="dataObject.name"
                               :name="'data[' + objectIndex + '][object_name]'"
                               class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <!-- Key-Value Pairs -->
                    <div class="space-y-4">
                        <template x-for="(pair, pairIndex) in dataObject.items" :key="pairIndex">
                            <div class="key-value-pair relative">
                                <!-- Delete Pair Button -->
                                <button type="button" 
                                        @click="deleteKeyValuePair(objectIndex, pairIndex)"
                                        class="absolute -top-2 right-0 text-red-600 hover:text-red-800"
                                        x-show="dataObject.items.length > 1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                                    <div class="sm:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Parametrs</label>
                                        <input type="text" 
                                               x-model="pair.key"
                                               :name="'data[' + objectIndex + '][items][' + pairIndex + '][key]'"
                                               class="w-full px-3 py-2 border rounded-lg"
                                               >
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Saturs</label>
                                        <input type="text" 
                                               x-model="pair.value"
                                               :name="'data[' + objectIndex + '][items][' + pairIndex + '][value]'"
                                               class="w-full px-3 py-2 border rounded-lg"
                                               >
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Add Key-Value Pair Button -->
                    <div class="mt-4">
                        <button type="button" 
                                @click="addKeyValuePair(objectIndex)"
                                class="w-full sm:w-auto bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                            Pievienot parametru un saturu
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Add Data Object Button -->
        <button type="button" 
                @click="addDataObject()"
                class="w-full bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors mb-6">
            Pievienot sadaļu
        </button>

        <!-- Submit Button -->
        <div class="border-t pt-4">
            <button type="submit" 
                    class="w-full sm:w-auto bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                {{ $partner ? 'Update Partner' : 'Create Partner' }}
            </button>
        </div>
    </form>
</div>