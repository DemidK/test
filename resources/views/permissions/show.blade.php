@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Manage Permissions: {{ $user->name }}</h1>
            <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Users
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('users.permissions.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Roles Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-4">Assigned Roles</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                            <div class="flex items-center p-3 border rounded-lg {{ in_array($role->id, $userRoleIds) ? 'bg-blue-50 border-blue-200' : 'bg-gray-50' }}">
                                <input type="checkbox" id="role_{{ $role->id }}" 
                                    name="roles[]" value="{{ $role->id }}"
                                    {{ in_array($role->id, $userRoleIds) ? 'checked' : '' }}
                                    class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="role_{{ $role->id }}" class="ml-2 block">
                                    <span class="font-medium">{{ $role->name }}</span>
                                    @if($role->description)
                                        <span class="text-sm text-gray-500 block">{{ $role->description }}</span>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Direct Permissions Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-4">Direct Permissions</h2>
                    <p class="text-sm text-gray-600 mb-4">
                        These permissions are assigned directly to the user and override permissions from roles.
                        Setting a permission to "Deny" will override any "Grant" permissions from roles.
                    </p>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @foreach($permissionsByResource as $resource => $permissions)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="font-bold text-lg mb-2">{{ $resource }}</h3>
                                
                                <div class="space-y-3">
                                    @foreach($permissions as $permission)
                                        <div class="p-2 {{ isset($userPermissions[$permission->id]) ? 'bg-gray-100 rounded' : '' }}">
                                            <div class="font-medium">{{ $permission->name }}</div>
                                            @if($permission->description)
                                                <div class="text-xs text-gray-500 mb-1">{{ $permission->description }}</div>
                                            @endif
                                            
                                            <div class="flex items-center space-x-4 mt-1">
                                                <div class="flex items-center">
                                                    <input type="radio" id="permission_{{ $permission->id }}_none" 
                                                        name="permissions[{{ $permission->id }}][granted]" value=""
                                                        {{ !isset($userPermissions[$permission->id]) ? 'checked' : '' }}
                                                        class="h-4 w-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                                    <label for="permission_{{ $permission->id }}_none" class="ml-2 text-sm">
                                                        Not Set
                                                    </label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="radio" id="permission_{{ $permission->id }}_grant" 
                                                        name="permissions[{{ $permission->id }}][granted]" value="1"
                                                        {{ isset($userPermissions[$permission->id]) && $userPermissions[$permission->id]['granted'] ? 'checked' : '' }}
                                                        class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                                    <label for="permission_{{ $permission->id }}_grant" class="ml-2 text-sm text-green-600 font-medium">
                                                        Grant
                                                    </label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="radio" id="permission_{{ $permission->id }}_deny" 
                                                        name="permissions[{{ $permission->id }}][granted]" value="0"
                                                        {{ isset($userPermissions[$permission->id]) && !$userPermissions[$permission->id]['granted'] ? 'checked' : '' }}
                                                        class="h-4 w-4 text-red-600 border-gray-300 focus:ring-red-500">
                                                    <label for="permission_{{ $permission->id }}_deny" class="ml-2 text-sm text-red-600 font-medium">
                                                        Deny
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Save Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection