@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Create Permission</h1>
            <a href="{{ route('permissions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Permissions
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('permissions.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="type" class="block text-gray-700 text-sm font-bold mb-2">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="route" {{ old('type') == 'route' ? 'selected' : '' }}>Route</option>
                        <option value="button" {{ old('type') == 'button' ? 'selected' : '' }}>Button</option>
                        <option value="ui" {{ old('type') == 'ui' ? 'selected' : '' }}>UI Element</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="resource" class="block text-gray-700 text-sm font-bold mb-2">
                        Resource
                    </label>
                    <input type="text" name="resource" id="resource" value="{{ old('resource') }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p class="text-gray-600 text-xs mt-1">What resource this permission applies to (invoices, users, etc.)</p>
                    @error('resource')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="action" class="block text-gray-700 text-sm font-bold mb-2">
                        Action
                    </label>
                    <input type="text" name="action" id="action" value="{{ old('action') }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p class="text-gray-600 text-xs mt-1">What action is permitted (view, edit, delete, etc.)</p>
                    @error('action')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Create Permission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection