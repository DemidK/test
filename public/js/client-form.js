// resources/js/partner-form.js
export function initPartnerForm(initialObjectCount) {
    window.objectCount = initialObjectCount;
    
    window.addDataObject = function() {
        const container = document.getElementById('data-objects-container');
        const newObject = document.createElement('div');
        newObject.className = 'data-object bg-gray-50 p-4 rounded-lg mb-4 relative';
        newObject.innerHTML = getDataObjectTemplate(objectCount);
        container.appendChild(newObject);
        objectCount++;
    }

    window.addKeyValuePair = function(button) {
        const dataObject = button.closest('.data-object');
        const keyValuePairs = dataObject.querySelector('.key-value-pairs');
        const pairCount = keyValuePairs.children.length;
        const objectIndex = Array.from(dataObject.parentNode.children).indexOf(dataObject);

        const newPair = document.createElement('div');
        newPair.className = 'key-value-pair mb-4 relative';
        newPair.innerHTML = getKeyValuePairTemplate(objectIndex, pairCount);
        keyValuePairs.appendChild(newPair);
    }

    window.deleteObject = function(button) {
        const dataObject = button.closest('.data-object');
        dataObject.remove();
        reindexObjects();
    }

    window.deleteKeyValuePair = function(button) {
        const keyValuePair = button.closest('.key-value-pair');
        keyValuePair.remove();
        reindexKeyValuePairs(keyValuePair.closest('.data-object'));
    }
}

function getDataObjectTemplate(index) {
    return `
        <button type="button" 
                onclick="deleteObject(this)" 
                class="absolute top-2 right-2 text-red-600 hover:text-red-800">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <h3 class="text-lg font-medium mb-4">Datu objekts ${index + 1}</h3>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Sadaļas nosaukums</label>
            <input type="text" name="data[${index}][object_name]" class="w-full px-3 py-2 border rounded-lg">
        </div>

        <div class="key-value-pairs">
            ${getKeyValuePairTemplate(index, 0)}
        </div>

        <button type="button" 
                onclick="addKeyValuePair(this)" 
                class="w-full sm:w-auto bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
            Pievienot parametru un saturu
        </button>
    `;
}

function getKeyValuePairTemplate(objectIndex, pairIndex) {
    return `
        <div class="key-value-pair mb-4 relative">
            <button type="button" 
                    onclick="deleteKeyValuePair(this)" 
                    class="absolute top-0 right-0 text-red-600 hover:text-red-800">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>

            <div class="grid grid-cols-4 gap-4">
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Key</label>
                    <input type="text" name="data[${objectIndex}][items][${pairIndex}][key]" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                    <input type="text" name="data[${objectIndex}][items][${pairIndex}][value]" class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
        </div>
    `;
}

function reindexObjects() {
    const container = document.getElementById('data-objects-container');
    const objects = container.querySelectorAll('.data-object');
    
    objects.forEach((object, objectIndex) => {
        const objectName = object.querySelector('input[name*="[object_name]"]');
        objectName.name = `data[${objectIndex}][object_name]`;
        
        const keyValuePairs = object.querySelectorAll('.key-value-pair');
        keyValuePairs.forEach((pair, pairIndex) => {
            const keyInput = pair.querySelector('input[name*="[key]"]');
            const valueInput = pair.querySelector('input[name*="[value]"]');
            
            keyInput.name = `data[${objectIndex}][items][${pairIndex}][key]`;
            valueInput.name = `data[${objectIndex}][items][${pairIndex}][value]`;
        });
    });
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