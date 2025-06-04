<!-- resources/views/custom_tables/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('custom-tables.show', $item->id) }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left"></i> Atpakaļ uz detaļām
                </a>
                <span class="text-gray-400">/</span>
                <h1 class="text-2xl font-bold">Rediģēt {{ $item->display_name }}</h1>
            </div>
        </div>

        <form action="{{ route('custom-tables.update', $item->id) }}" 
              method="POST" 
              class="bg-white shadow-md rounded-lg p-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4">Tabulas informācija</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Sistēmas nosaukums
                        </label>
                        <input type="text" 
                               value="{{ $item->name }}"
                               class="form-input w-full rounded-md bg-gray-100"
                               disabled
                               readonly>
                        <p class="text-sm text-gray-500 mt-1">
                            Sistēmas nosaukumu nevar mainīt pēc izveides
                        </p>
                    </div>

                    <div>
                        <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Attēlojamais nosaukums*
                        </label>
                        <input type="text" 
                               name="display_name" 
                               id="display_name" 
                               value="{{ old('display_name', $item->display_name) }}"
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
                              class="form-textarea w-full rounded-md border-gray-300 @error('description') border-red-500 @enderror">{{ old('description', $item->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', $item->is_active) ? 'checked' : '' }}
                               class="form-checkbox rounded border-gray-300">
                        <span class="ml-2">Aktīvs</span>
                    </label>
                    <p class="text-sm text-gray-500 mt-1">
                        Neaktīvās tabulas būs paslēptas no navigācijas un datu ievades
                    </p>
                </div>
            </div>

            <!-- Field Management -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Lauki</h2>
                    <button type="button" 
                            id="addField"
                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            title="Jaunus laukus var pievienot tikai tabulas izveides laikā">
                        Pievienot lauku
                    </button>
                </div>

                <div id="fieldsContainer">
                    @foreach($item->fields as $fieldName => $field)
                        <div class="field-row bg-gray-50 p-4 rounded-md mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Lauka nosaukums
                                    </label>
                                    <input type="text" 
                                           value="{{ $fieldName }}"
                                           class="form-input w-full rounded-md bg-gray-100"
                                           disabled
                                           readonly>
                                </div>

                                <div>
                                    <label for="fields[{{ $fieldName }}][label]" class="block text-sm font-medium text-gray-700 mb-1">
                                        Attēlojamais nosaukums*
                                    </label>
                                    <input type="text" 
                                           name="fields[{{ $fieldName }}][label]" 
                                           value="{{ old("fields.{$fieldName}.label", $field['label']) }}"
                                           class="form-input w-full rounded-md border-gray-300"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Lauka tips
                                    </label>
                                    <input type="text" 
                                           value="{{ ucfirst($field['type']) }}"
                                           class="form-input w-full rounded-md bg-gray-100"
                                           disabled
                                           readonly>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" 
                                           name="fields[{{ $fieldName }}][required]" 
                                           value="1"
                                           {{ old("fields.{$fieldName}.required", $field['required'] ?? false) ? 'checked' : '' }}
                                           class="form-checkbox rounded border-gray-300">
                                    <span class="ml-2">Obligāts lauks</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-between">
                <a href="{{ route('custom-tables.show', $item->id) }}" 
                   class="text-gray-600 hover:text-gray-800">
                    Atcelt
                </a>
                <button type="submit" 
                        class="bg-green-500 text-white px-6 py-2 rounded-md hover:bg-green-600 transition-colors">
                    Saglabāt izmaiņas
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Disable add field button (fields can only be added during creation)
    const addFieldButton = document.getElementById('addField');
    addFieldButton.disabled = true;
    
    // Warn about structural changes
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const requiredFields = document.querySelectorAll('input[name$="[required]"]:checked');
        const originalRequired = {{ count(array_filter($item->fields, fn($f) => $f['required'] ?? false)) }};
        
        if (requiredFields.length !== originalRequired) {
            if (!confirm('Obligāto lauku iestatījumu maiņa var ietekmēt esošos datus. Vai tiešām vēlaties turpināt?')) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>

@endsection