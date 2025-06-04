<!-- resources/views/custom_tables/data/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('custom-tables.data.index', $customTable->name) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-left"></i> Back to {{ $customTable->display_name }}
                    </a>
                    <span class="text-gray-400">/</span>
                    <h1 class="text-2xl font-bold">Record #{{ $item->id }}</h1>
                </div>
                @if($customTable->description)
                    <p class="text-gray-600">{{ $customTable->description }}</p>
                @endif
            </div>
            <div class="flex gap-2 mt-4 sm:mt-0">
                <a href="{{ route('custom-tables.data.edit', [$customTable->name, $item->id]) }}" 
                   class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <form action="{{ route('custom-tables.data.destroy', [$customTable->name, $item->id]) }}" 
                      method="POST"
                      class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this record?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Record Details -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                    @foreach($customTable->fields as $fieldName => $field)
                        <div class="field-group">
                            <dt class="text-sm font-medium text-gray-500 mb-1">{{ $field['label'] }}</dt>
                            <dd class="text-base text-gray-900">
                                @switch($field['type'])
                                    @case('boolean')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $item->$fieldName ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $item->$fieldName ? 'Yes' : 'No' }}
                                        </span>
                                        @break

                                    @case('json')
                                        <pre class="bg-gray-50 p-3 rounded-md text-sm overflow-x-auto">{{ json_encode($item->$fieldName, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        @break

                                    @case('date')
                                        {{ $item->$fieldName ? date('M d, Y', strtotime($item->$fieldName)) : '-' }}
                                        @break

                                    @case('datetime')
                                        {{ $item->$fieldName ? date('M d, Y H:i', strtotime($item->$fieldName)) : '-' }}
                                        @break

                                    @case('email')
                                        <a href="mailto:{{ $item->$fieldName }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $item->$fieldName ?: '-' }}
                                        </a>
                                        @break

                                    @case('url')
                                        <a href="{{ $item->$fieldName }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                            {{ $item->$fieldName ?: '-' }}
                                            <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                        </a>
                                        @break

                                    @case('password')
                                        <em class="text-gray-500">Hidden</em>
                                        @break

                                    @default
                                        {{ $item->$fieldName ?: '-' }}
                                @endswitch
                            </dd>
                        </div>
                    @endforeach

                    <!-- System Fields -->
                    <div class="field-group md:col-span-2 border-t pt-4 mt-2">
                        <dt class="text-sm font-medium text-gray-500 mb-1">System Information</dt>
                        <dd class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                            <div>
                                <span class="font-medium">Created:</span>
                                {{ $item->created_at->format('M d, Y H:i') }}
                            </div>
                            <div>
                                <span class="font-medium">Last Updated:</span>
                                {{ $item->updated_at->format('M d, Y H:i') }}
                            </div>
                            <div>
                                <span class="font-medium">Record ID:</span>
                                {{ $item->id }}
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg" 
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 3000)">
        {{ session('success') }}
    </div>
@endif

@endsection