<!-- resources/views/components/custom-table/data-form.blade.php -->
@props(['customTable', 'item' => null, 'action'])

<form action="{{ $action }}" method="POST" class="bg-white shadow-md rounded-lg p-6">
    @csrf
    @if($item)
        @method('PUT')
    @endif

    <div class="space-y-6">
        @foreach($customTable->fields as $fieldName => $field)
            <div class="field-group">
                <label for="{{ $fieldName }}" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $field['label'] }}
                    @if($field['required'] ?? false)
                        <span class="text-red-500">*</span>
                    @endif
                </label>

                @switch($field['type'])
                    @case('text')
                        <input type="text" 
                               id="{{ $fieldName }}"
                               name="{{ $fieldName }}"
                               value="{{ old($fieldName, $item?->$fieldName) }}"
                               class="form-input w-full rounded-md border-gray-300 @error($fieldName) border-red-500 @enderror"
                               {{ ($field['required'] ?? false) ? 'required' : '' }}>
                        @break

                    @case('integer')
                        <input type="number" 
                               id="{{ $fieldName }}"
                               name="{{ $fieldName }}"
                               value="{{ old($fieldName, $item?->$fieldName) }}"
                               step="1"
                               class="form-input w-full rounded-md border-gray-300 @error($fieldName) border-red-500 @enderror"
                               {{ ($field['required'] ?? false) ? 'required' : '' }}>
                        @break

                    @case('float')
                        <input type="number" 
                               id="{{ $fieldName }}"
                               name="{{ $fieldName }}"
                               value="{{ old($fieldName, $item?->$fieldName) }}"
                               step="0.01"
                               class="form-input w-full rounded-md border-gray-300 @error($fieldName) border-red-500 @enderror"
                               {{ ($field['required'] ?? false) ? 'required' : '' }}>
                        @break

                    @case('boolean')
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="{{ $fieldName }}"
                                   name="{{ $fieldName }}"
                                   value="1"
                                   {{ old($fieldName, $item?->$fieldName) ? 'checked' : '' }}
                                   class="form-checkbox rounded border-gray-300 @error($fieldName) border-red-500 @enderror">
                            <label for="{{ $fieldName }}" class="ml-2 text-sm text-gray-700">Yes</label>
                        </div>
                        @break

                    @case('date')
                        <input type="date" 
                               id="{{ $fieldName }}"
                               name="{{ $fieldName }}"
                               value="{{ old($fieldName, $item?->$fieldName ? date('Y-m-d', strtotime($item->$fieldName)) : '') }}"
                               class="form-input w-full rounded-md border-gray-300 @error($fieldName) border-red-500 @enderror"
                               {{ ($field['required'] ?? false) ? 'required' : '' }}>
                        @break

                    @case('datetime')
                        <input type="datetime-local" 
                               id="{{ $fieldName }}"
                               name="{{ $fieldName }}"
                               value="{{ old($fieldName, $item?->$fieldName ? date('Y-m-d\TH:i', strtotime($item->$fieldName)) : '') }}"
                               class="form-input w-full rounded-md border-gray-300 @error($fieldName) border-red-500 @enderror"
                               {{ ($field['required'] ?? false) ? 'required' : '' }}>
                        @break

                    @case('email')
                        <input type="email" 
                               id="{{ $fieldName }}"
                               name="{{ $fieldName }}"
                               value="{{ old($fieldName, $item?->$fieldName) }}"
                               class="form-input w-full rounded-md border-gray-300 @error($fieldName) border-red-500 @enderror"
                               {{ ($field['required'] ?? false) ? 'required' : '' }}>
                        @break

                    @case('url')
                        <input type="url" 
                               id="{{ $fieldName }}"
                               name="{{ $fieldName }}"
                               value="{{ old($fieldName, $item?->$fieldName) }}"
                               class="form-input w-full rounded-md border-gray-300 @error($fieldName) border-red-500 @enderror"
                               {{ ($field['required'] ?? false) ? 'required' : '' }}>
                        @break

                    @case('password')
                        <input type="password" 
                               id="{{ $fieldName }}"
                               name="{{ $fieldName }}"
                               class="form-input w-full rounded-md border-gray-300 @error($fieldName) border-red-500 @enderror"
                               {{ ($field['required'] ?? false) ? 'required' : '' }}>
                        @break

                    @case('json')
                        <textarea id="{{ $fieldName }}"
                                  name="{{ $fieldName }}"
                                  rows="3"
                                  class="form-textarea w-full rounded-md border-gray-300 @error($fieldName) border-red-500 @enderror"
                                  {{ ($field['required'] ?? false) ? 'required' : '' }}>{{ old($fieldName, $item?->$fieldName) }}</textarea>
                        @break
                @endswitch

                @error($fieldName)
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

                @if($field['help'] ?? false)
                    <p class="text-sm text-gray-500 mt-1">{{ $field['help'] }}</p>
                @endif
            </div>
        @endforeach
    </div>

    <div class="flex justify-between mt-6">
        <a href="{{ route('custom-tables.data.index', $customTable->name) }}" 
           class="text-gray-600 hover:text-gray-800">
            Cancel
        </a>
        <button type="submit" 
                class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 transition-colors">
            {{ $item ? 'Update' : 'Create' }} Record
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize JSON editors
    document.querySelectorAll('textarea[name$="json"]').forEach(function(textarea) {
        textarea.addEventListener('input', function() {
            try {
                const parsed = JSON.parse(this.value);
                this.value = JSON.stringify(parsed, null, 2);
                this.classList.remove('border-red-500');
            } catch (e) {
                this.classList.add('border-red-500');
            }
        });
    });
});
</script>
