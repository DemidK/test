<!-- resources/views/custom_tables/data/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('custom-tables.index') }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-left"></i> Back to Tables
                    </a>
                    <span class="text-gray-400">/</span>
                    <h1 class="text-2xl sm:text-3xl font-bold">{{ $customTable->display_name }}</h1>
                </div>
                @if($customTable->description)
                    <p class="text-gray-600">{{ $customTable->description }}</p>
                @endif
            </div>
            <a href="{{ route('custom-tables.data.create', $customTable->name) }}" 
               class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors mt-4 sm:mt-0">
                <i class="fas fa-plus mr-2"></i>Add New Record
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <form action="{{ route('custom-tables.data.index', $customTable->name) }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           placeholder="Search records..." 
                           value="{{ request('search') }}"
                           class="form-input w-full rounded-md border-gray-300">
                </div>
                <button type="submit" 
                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                    Search
                </button>
            </form>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if($items->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    @if(request('search'))
                        No records found matching your search criteria.
                        <a href="{{ route('custom-tables.data.index', $customTable->name) }}" class="text-blue-500 hover:underline ml-2">
                            Clear search
                        </a>
                    @else
                        No records have been added yet.
                        <a href="{{ route('custom-tables.data.create', $customTable->name) }}" class="text-blue-500 hover:underline ml-2">
                            Add your first record
                        </a>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                @foreach($customTable->fields as $fieldName => $field)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $field['label'] }}
                                    </th>
                                @endforeach
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item->id }}
                                    </td>
                                    @foreach($customTable->fields as $fieldName => $field)
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @switch($field['type'])
                                                    @case('boolean')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                            {{ $item->$fieldName ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $item->$fieldName ? 'Yes' : 'No' }}
                                                        </span>
                                                        @break
                                                    @case('json')
                                                        <pre class="text-xs">{{ json_encode($item->$fieldName, JSON_PRETTY_PRINT) }}</pre>
                                                        @break
                                                    @case('date')
                                                        {{ $item->$fieldName ? date('M d, Y', strtotime($item->$fieldName)) : '' }}
                                                        @break
                                                    @case('datetime')
                                                        {{ $item->$fieldName ? date('M d, Y H:i', strtotime($item->$fieldName)) : '' }}
                                                        @break
                                                    @default
                                                        {{ Str::limit($item->$fieldName, 50) }}
                                                @endswitch
                                            </div>
                                        </td>
                                    @endforeach
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('custom-tables.data.show', [$customTable->name, $item->id]) }}" 
                                               class="text-blue-600 hover:text-blue-900"
                                               title="View Record">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('custom-tables.data.edit', [$customTable->name, $item->id]) }}" 
                                               class="text-yellow-600 hover:text-yellow-900"
                                               title="Edit Record">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('custom-tables.data.destroy', [$customTable->name, $item->id]) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Delete Record">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $items->links() }}
                </div>
            @endif
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