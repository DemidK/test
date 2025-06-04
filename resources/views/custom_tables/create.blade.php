<!-- resources/views/custom_tables/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Izveidot jaunu tabulu</h1>
            <a href="{{ route('custom-tables.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i> Atpakaļ pie tabulām
            </a>
        </div>

        <form action="{{ route('custom-tables.store') }}" method="POST" id="createTableForm" class="bg-white shadow-md rounded-lg p-6">
            @csrf

            <!-- Basic Information -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4">Tabulas informācija</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Tabulas nosaukums*
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               class="form-input w-full rounded-md border-gray-300 @error('name') border-red-500 @enderror"
                               pattern="[a-z][a-z0-9_]*"
                               required>
                        <p class="text-sm text-gray-500 mt-1">
                            Izmantojiet tikai mazos burtus, ciparus un pasvītrojumus. Jāsākas ar burtu.
                        </p>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Attēlojamais nosaukums*
                        </label>
                        <input type="text" 
                               name="display_name" 
                               id="display_name" 
                               value="{{ old('display_name') }}"
                               class="form-input w-full rounded-md border-gray-300 @error('display_name') border-red-500 @enderror"
                               required>
                        @error('display_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Apraksts
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="3" 
                              class="form-textarea w-full rounded-md border-gray-300 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Fields Section -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Tabulas lauki</h2>
                    <button type="button" 
                            id="addField"
                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">
                        Pievienot lauku
                    </button>
                </div>

                <div id="fieldsContainer" class="space-y-4">
                    <!-- Fields will be added here -->
                </div>

                @error('fields')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('custom-tables.index') }}" 
                   class="text-gray-600 hover:text-gray-800">
                    Atcelt
                </a>
                <button type="submit" 
                        class="bg-green-500 text-white px-6 py-2 rounded-md hover:bg-green-600 transition-colors">
                    Izveidot tabulu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Field Template -->
<template id="fieldTemplate">
    <div class="field-row bg-gray-50 p-4 rounded-md">
        <div class="flex justify-between mb-2">
            <h3 class="font-medium">Lauks #<span class="field-number"></span></h3>
            <button type="button" class="delete-field text-red-500 hover:text-red-700">
                <i class="fas fa-trash"></i>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Lauka nosaukums*
                </label>
                <input type="text" 
                       name="fields[0][name]" 
                       class="field-name form-input w-full rounded-md border-gray-300"
                       pattern="[a-z][a-z0-9_]*"
                       required>
                <p class="text-sm text-gray-500 mt-1">
                    Mazie burti, cipari un pasvītrojumi
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Attēlojamais nosaukums*
                </label>
                <input type="text" 
                       name="fields[0][label]" 
                       class="field-label form-input w-full rounded-md border-gray-300"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Lauka tips*
                </label>
                <select name="fields[0][type]" 
                        class="field-type form-select w-full rounded-md border-gray-300"
                        required>
                    <option value="">Izvēlieties tipu</option>
                    <option value="text">Text</option>
                    <option value="integer">Integer Number</option>
                    <option value="float">Decimal Number</option>
                    <option value="boolean">Yes/No</option>
                    <option value="date">Date</option>
                    <option value="datetime">Date and Time</option>
                    <option value="email">Email</option>
                    <option value="url">URL</option>
                    <option value="json">JSON</option>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <label class="inline-flex items-center">
                <input type="checkbox" 
                       name="fields[0][required]" 
                       class="field-required form-checkbox rounded border-gray-300"
                       value="1">
                <span class="ml-2">Obligāts lauks</span>
            </label>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fieldsContainer = document.getElementById('fieldsContainer');
    const fieldTemplate = document.getElementById('fieldTemplate');
    const addFieldButton = document.getElementById('addField');
    let fieldCount = 0;

    function addField() {
        fieldCount++;
        const field = fieldTemplate.content.cloneNode(true);
        
        // Update field number
        field.querySelector('.field-number').textContent = fieldCount;
        
        // Update field names to use array index
        const nameInput = field.querySelector('.field-name');
        const labelInput = field.querySelector('.field-label');
        const typeSelect = field.querySelector('.field-type');
        const requiredCheckbox = field.querySelector('.field-required');
        
        nameInput.name = `fields[${fieldCount-1}][name]`;
        labelInput.name = `fields[${fieldCount-1}][label]`;
        typeSelect.name = `fields[${fieldCount-1}][type]`;
        requiredCheckbox.name = `fields[${fieldCount-1}][required]`;

        // Add delete handler
        const deleteButton = field.querySelector('.delete-field');
        deleteButton.addEventListener('click', function() {
            this.closest('.field-row').remove();
            updateFieldNumbers();
        });

        fieldsContainer.appendChild(field);
    }

    function updateFieldNumbers() {
        const fields = fieldsContainer.querySelectorAll('.field-row');
        fields.forEach((field, index) => {
            field.querySelector('.field-number').textContent = index + 1;
            
            // Update field names
            const nameInput = field.querySelector('.field-name');
            const labelInput = field.querySelector('.field-label');
            const typeSelect = field.querySelector('.field-type');
            const requiredCheckbox = field.querySelector('.field-required');
            
            nameInput.name = `fields[${index}][name]`;
            labelInput.name = `fields[${index}][label]`;
            typeSelect.name = `fields[${index}][type]`;
            requiredCheckbox.name = `fields[${index}][required]`;
        });
    }

    // Add field button handler
    addFieldButton.addEventListener('click', addField);

    // Form validation
    document.getElementById('createTableForm').addEventListener('submit', function(e) {
        const fields = fieldsContainer.querySelectorAll('.field-row');
        if (fields.length === 0) {
            e.preventDefault();
            alert('Lūdzu, pievienojiet tabulai vismaz vienu lauku.');
            return false;
        }
        return true;
    });

    // Add first field by default
    addField();
});
</script>

@endsection