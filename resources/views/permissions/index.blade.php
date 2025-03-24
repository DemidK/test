@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Permissions</h1>
            <a href="{{ route('permissions.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Permission
            </a>
        </div>

        <div class="mb-6 bg-white rounded-lg shadow p-4">
            <h2 class="text-xl font-semibold mb-4">Generate CRUD Permissions</h2>
            <form action="{{ route('permissions.generate-crud') }}" method="POST" class="flex items-end">
                @csrf
                <div class="mr-4 flex-grow">
                    <label for="resource" class="block text-gray-700 text-sm font-bold mb-2">
                        Resource Name (e.g., users, roles, invoices)
                    </label>
                    <input type="text" name="resource" id="resource" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Generate Permissions
                </button>
            </form>
        </div>

        <!-- Search Form -->
        <div class="mb-6">
            <form action="{{ route('permissions.index') }}" method="GET" class="flex">
                <input type="text" name="search" placeholder="Search permissions..." value="{{ $search ?? '' }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 ml-2 rounded">
                    Search
                </button>
                @if($search ?? false)
                    <a href="{{ route('permissions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 ml-2 rounded">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Permissions by Resource -->
        @foreach($groupedPermissions as $resource => $permissions)
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-4">{{ $resource }}</h2>
                
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-3 px-4 bg-gray-100 font-semibold text-left">Name</th>
                                <th class="py-3 px-4 bg-gray-100 font-semibold text-left">Slug</th>
                                <th class="py-3 px-4 bg-gray-100 font-semibold text-left">Type</th>
                                <th class="py-3 px-4 bg-gray-100 font-semibold text-left">Description</th>
                                <th class="py-3 px-4 bg-gray-100 font-semibold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($permissions as $permission)
                                <tr>
                                    <td class="py-3 px-4">{{ $permission->name }}</td>
                                    <td class="py-3 px-4 font-mono text-sm">{{ $permission->slug }}</td>
                                    <td class="py-3 px-4">{{ $permission->type }}</td>
                                    <td class="py-3 px-4">{{ $permission->description ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('permissions.show', $permission->id) }}" class="text-blue-500 hover:text-blue-700">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('permissions.edit', $permission->id) }}" class="text-yellow-500 hover:text-yellow-700">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this permission?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700">
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
            </div>
        @endforeach

        @if(count($groupedPermissions) === 0)
            <div class="bg-white shadow-md rounded-lg p-6 text-center">
                <p class="text-gray-500">No permissions found.</p>
            </div>
        @endif
    </div>
</div>
@endsection