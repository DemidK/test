{{-- Предполагается, что у вас есть основной layout-файл, например, layouts.app --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Rediģēt lomu: {{ $item->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('roles.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Название роли -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">Lomas nosaukums</label>
                            <input id="name" name="name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('name', $item->name) }}" required>
                             @error('name')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Описание -->
                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-gray-700">Apraksts</label>
                            <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $item->description) }}</textarea>
                             @error('description')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        @include('roles._permissions_form', ['routesByGroup' => $routesByGroup, 'rolePermissions' => old('permissions', $rolePermissions)])

                        <div class="flex items-center justify-end mt-6">
                             <a href="{{ route('roles.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Atcelt
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Atjaunināt lomu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>