<!-- resources/views/custom_tables/show.blade.php -->
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
                    <h1 class="text-2xl font-bold">{{ $item->display_name }}</h1>
                </div>
                @if($item->description)
                    <p class="text-gray-600">{{ $item->description }}</p>
                @endif
            </div>
            
            <div class="flex gap-2 mt-4 sm:mt-0">
                <a href="{{ route('custom-tables.data.index', $item->name) }}" 
                   class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">
                    <i class="fas fa-table mr-2"></i>View Records
                </a>
                <a href="{{ route('custom-tables.edit', $item->id) }}" 
                   class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Table
                </a>
                <form action="{{ route('custom-tables.destroy', $item->id) }}" 
                      method="POST"
                      class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this table? This will delete all data in the table and cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Table Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold">Table Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">System Name</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $item->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Display Name</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $item->display_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $item->description ?: 'No description provided' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $item->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $item->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $item->updated_at->format('M d, Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Field Definitions -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold">Fields</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Field Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Required
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($item->fields as $fieldName => $field)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $field['label'] }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $fieldName }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($field['type']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($field['required'] ?? false)
                                            <span class="text-green-600">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        @else
                                            <span class="text-gray-400">
                                                <i class="fas fa-minus"></i>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold">Statistics</h2>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div class="px-4 py-5 bg-gray-50 shadow rounded-lg overflow-hidden sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Records</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ $recordCount }}
                        </dd>
                    </div>
                    <div class="px-4 py-5 bg-gray-50 shadow rounded-lg overflow-hidden sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">Fields Count</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ count($item->fields) }}
                        </dd>
                    </div>
                    <div class="px-4 py-5 bg-gray-50 shadow rounded-lg overflow-hidden sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">Last Record Added</dt>
                        <dd class="mt-1 text-xl font-semibold text-gray-900">
                            {{ $lastRecord ? $lastRecord->created_at->format('M d, Y') : 'No records yet' }}
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