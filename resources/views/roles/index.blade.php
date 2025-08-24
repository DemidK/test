@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Lomas</h1>
            <x-button :href="route('roles.create')">
                Izveidot jaunu lomu
            </x-button>
        </div>

        <!-- Search Form -->
        <div class="mb-6">
            <form action="{{ route('roles.index') }}" method="GET" class="flex">
                <input type="text" name="search" placeholder="Meklēt lomas..." value="{{ $search ?? '' }}"
                    class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                <x-button type="submit" class="ml-2">
                    Meklēt
                </x-button>
                @if($search ?? false)
                    <x-button :href="route('roles.index')" variant="secondary" class="ml-2">
                        Notīrīt
                    </x-button>
                @endif
            </form>
        </div>

        <!-- Roles List -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full bg-white dark:bg-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="py-3 px-4 font-semibold text-left text-xs text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            <a href="{{ route('roles.index', ['sort_by' => 'name', 'sort_order' => $sortBy === 'name' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                Nosaukums
                                @if($sortBy === 'name')
                                    <i class="fas fa-chevron-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @endif
                            </a>
                        </th>
                        <th class="py-3 px-4 font-semibold text-left text-xs text-gray-600 dark:text-gray-300 uppercase tracking-wider">Apraksts</th>
                        <th class="py-3 px-4 font-semibold text-left text-xs text-gray-600 dark:text-gray-300 uppercase tracking-wider">Atļaujas</th>
                        <th class="py-3 px-4 font-semibold text-center text-xs text-gray-600 dark:text-gray-300 uppercase tracking-wider">Lietotāji</th>
                        <th class="py-3 px-4 font-semibold text-center text-xs text-gray-600 dark:text-gray-300 uppercase tracking-wider">Darbības</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
                    @foreach($items as $role)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="py-3 px-4">{{ $role->name }}</td>
                            <td class="py-3 px-4">{{ $role->description ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <span class="text-sm bg-blue-100 text-blue-800 py-1 px-2 rounded-full dark:bg-blue-900 dark:text-blue-200">
                                    {{ count($role->permissions ?? []) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="text-sm bg-green-100 text-green-800 py-1 px-2 rounded-full dark:bg-green-900 dark:text-green-200">
                                    {{ $role->users->count() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <x-icon-button :href="route('roles.show', $role->id)" variant="primary" title="Skatīt">
                                        <i class="fas fa-eye"></i>
                                    </x-icon-button>
                                    <x-icon-button :href="route('roles.edit', $role->id)" variant="warning" title="Rediģēt">
                                        <i class="fas fa-edit"></i>
                                    </x-icon-button>
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline" onsubmit="return confirm('Vai tiešām vēlaties dzēst šo lomu?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-icon-button type="submit" variant="danger" title="Dzēst">
                                            <i class="fas fa-trash"></i>
                                        </x-icon-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection