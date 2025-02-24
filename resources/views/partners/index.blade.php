@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-0">Partners</h1>
            <a href="{{ route('partners.create') }}" 
               class="inline-flex items-center justify-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <span class="mr-2">Pievienot jaunu klientu</span>
                <i class="fas fa-plus"></i>
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md">
            <!-- Search and Filter Form -->
            <form method="GET" class="p-4 border-b border-gray-200">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="flex-1">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search }}"
                            placeholder="Search by name or ID number" 
                            class="w-full px-4 py-2 border rounded-lg"
                        >
                    </div>
                    <div class="flex-none">
                        <select 
                            name="sort_by" 
                            class="px-4 py-2 border rounded-lg bg-white"
                        >
                            <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="date" {{ $sortBy == 'date' ? 'selected' : '' }}>Date</option>
                            <option value="id" {{ $sortBy == 'id' ? 'selected' : '' }}>ID Number</option>
                        </select>
                        <select 
                            name="sort_order" 
                            class="px-4 py-2 border rounded-lg bg-white ml-2"
                        >
                            <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Ascending</option>
                            <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Descending</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Search
                    </button>
                    @if(request()->hasAny(['search', 'sort_by', 'sort_order']))
                        <a href="{{ route('partners.index') }}"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Clear
                        </a>
                    @endif
                </div>
            </form>

            <!-- partners List -->
            <div class="sm:hidden divide-y divide-gray-200">
                @foreach($items as $partner)
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $partner->name }}</h3>
                                <p class="text-sm text-gray-500">ID: {{ $partner->identification_number }}</p>
                                <p class="text-sm text-gray-500">Added: {{ $partner->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('partners.show', $partner) }}"
                                    class="text-blue-600 hover:text-blue-800 p-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('partners.edit', $partner) }}"
                                    class="text-blue-600 hover:text-blue-800 p-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop View -->
            <div class="hidden sm:block">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($items as $partner)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $partner->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $partner->identification_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $partner->created_at->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('partners.show', $partner) }}" class="text-blue-600 hover:text-blue-800 p-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('partners.edit', $partner) }}" class="text-blue-600 hover:text-blue-800 p-2">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $items->links() }}
            </div>
        </div>
</div>
@endsection