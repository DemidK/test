@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-0">Clients</h1>
        <a href="{{ route('clients.create') }}" 
           class="inline-flex items-center justify-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            <span class="mr-2">Add New Client</span>
            <i class="fas fa-plus"></i>
        </a>
    </div>

    <!-- Mobile-friendly search and filter -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" 
                       placeholder="Search clients..." 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex flex-wrap gap-2">
                <select class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Sort by</option>
                    <option value="name">Name</option>
                    <option value="date">Date Added</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Clients List -->
    <div class="bg-white rounded-lg shadow-md">
        <!-- Mobile View (Card Layout) -->
        <div class="sm:hidden divide-y divide-gray-200">
            @foreach($clients as $client)
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-medium text-gray-900">{{ $client->name }}</h3>
                        <p class="text-sm text-gray-500">ID: {{ $client->identification_number }}</p>
                        <p class="text-sm text-gray-500">Added: {{ $client->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('clients.show', $client) }}" 
                        class="text-blue-600 hover:text-blue-800 p-2">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('clients.edit', $client) }}" 
                        class="text-blue-600 hover:text-blue-800 p-2">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('clients.destroy', $client) }}" 
                            method="POST" 
                            class="inline-block"
                            onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Desktop View (Table Layout) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID Number
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Added Date
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($clients as $client)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-900">{{ $client->name }}</div>
                        </td>
                        <td class="px-4 py-4">{{ $client->identification_number }}</td>
                        <td class="px-4 py-4">{{ $client->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('clients.show', $client) }}" 
                                class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('clients.edit', $client) }}" 
                                class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('clients.destroy', $client) }}" 
                                    method="POST" 
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-800"
                                            onclick="return confirm('Are you sure?')">
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

        <div class="px-4 py-3 border-t border-gray-200">
            {{ $clients->links() }}
        </div>
    </div>
</div>
@endsection