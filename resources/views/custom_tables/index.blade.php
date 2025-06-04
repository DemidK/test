<!-- resources/views/custom_tables/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-0">Custom Tables</h1>
            <a href="{{ route('custom-tables.create') }}" 
               class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">
                <i class="fas fa-plus mr-2"></i>Create New Table
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <form action="{{ route('custom-tables.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           placeholder="Search tables..." 
                           value="{{ request('search') }}"
                           class="form-input w-full rounded-md border-gray-300">
                </div>
                <div class="flex gap-2">
                    <select name="sort_by" 
                            class="form-select rounded-md border-gray-300">
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="display_name" {{ request('sort_by') == 'display_name' ? 'selected' : '' }}>Display Name</option>
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                    </select>
                    <select name="sort_order" 
                            class="form-select rounded-md border-gray-300">
                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    </select>
                    <button type="submit" 
                            class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Tables List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if($items->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    @if(request('search'))
                        No tables found matching your search criteria.
                        <a href="{{ route('custom-tables.index') }}" class="text-blue-500 hover:underline ml-2">
                            Clear search
                        </a>
                    @else
                        No custom tables have been created yet.
                        <a href="{{ route('custom-tables.create') }}" class="text-blue-500 hover:underline ml-2">
                            Create your first table
                        </a>
                    @endif
                </div>
            @else
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Table Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Display Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fields
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($items as $table)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $table->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $table->display_name }}
                                    </div>
                                    @if($table->description)
                                        <div class="text-xs text-gray-500">
                                            {{ Str::limit($table->description, 50) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ count($table->fields) }} fields
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $table->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $table->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $table->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('custom-tables.data.index', $table->name) }}" 
                                           class="text-indigo-600 hover:text-indigo-900"
                                           title="Manage Records">
                                            <i class="fas fa-table"></i>
                                        </a>
                                        <a href="{{ route('custom-tables.show', $table->id) }}" 
                                           class="text-blue-600 hover:text-blue-900"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('custom-tables.edit', $table->id) }}" 
                                           class="text-yellow-600 hover:text-yellow-900"
                                           title="Edit Table">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('custom-tables.destroy', $table->id) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this table? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900"
                                                    title="Delete Table">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

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