@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Lietotāji</h1>
            {{-- No create button as per routes, users are registered --}}
        </div>

        <!-- Search Form -->
        <div class="mb-6">
            <form action="{{ route('users.index') }}" method="GET" class="flex">
                <input type="text" name="search" placeholder="Meklēt lietotājus..." value="{{ $search ?? '' }}"
                    class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                <x-button type="submit" class="ml-2">
                    Meklēt
                </x-button>
                @if($search ?? false)
                    <x-button :href="route('users.index')" variant="secondary" class="ml-2">
                        Notīrīt
                    </x-button>
                @endif
            </form>
        </div>

        <!-- Users List -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full bg-white dark:bg-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="py-3 px-4 font-semibold text-left text-xs text-gray-600 dark:text-gray-300 uppercase tracking-wider">Vārds</th>
                        <th class="py-3 px-4 font-semibold text-left text-xs text-gray-600 dark:text-gray-300 uppercase tracking-wider">E-pasts</th>
                        <th class="py-3 px-4 font-semibold text-left text-xs text-gray-600 dark:text-gray-300 uppercase tracking-wider">Lomas</th>
                        <th class="py-3 px-4 font-semibold text-center text-xs text-gray-600 dark:text-gray-300 uppercase tracking-wider">Darbības</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="py-3 px-4">{{ $item->name }}</td>
                            <td class="py-3 px-4">{{ $item->email }}</td>
                            <td class="py-3 px-4">
                                @foreach($item->roles as $role)
                                    <span class="text-sm bg-indigo-100 text-indigo-800 py-1 px-2 rounded-full dark:bg-indigo-900 dark:text-indigo-200 mr-1">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    @can('users.show')
                                    <x-icon-button :href="route('users.show', $item->id)" variant="primary" title="Skatīt"><i class="fas fa-eye"></i></x-icon-button>
                                    @endcan
                                    @can('users.edit')
                                    <x-icon-button :href="route('users.edit', $item->id)" variant="warning" title="Rediģēt lomas/atļaujas"><i class="fas fa-user-shield"></i></x-icon-button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 px-4 text-center text-gray-500 dark:text-gray-400">Lietotāji nav atrasti.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">{{ $items->links() }}</div>
    </div>
</div>
@endsection