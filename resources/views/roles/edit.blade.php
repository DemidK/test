@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Rediģēt lomu: {{ $item->name }}</h1>
            <a href="{{ route('roles.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Atpakaļ pie lomām
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('roles.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                        Nosaukums <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">
                        Apraksts
                    </label>
                    <textarea name="description" id="description" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description', $item->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Permissions Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-4">Atļaujas</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($permissionsByResource as $resource => $permissions)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="font-bold text-lg mb-2">{{ $resource }}</h3>
                                
                                <div class="space-y-2">
                                    @foreach($permissions as $permission)
                                        <div class="flex items-center">
                                            <input type="checkbox" id="permission_{{ $permission->id }}" 
                                                name="permissions[]" value="{{ $permission->id }}"
                                                {{ in_array($permission->id, $rolePermissionIds) ? 'checked' : '' }}
                                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <label for="permission_{{ $permission->id }}" class="ml-2 block text-sm">
                                                {{ $permission->name }}
                                                <span class="text-xs text-gray-500 block">{{ $permission->description }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Atjaunināt lomu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection