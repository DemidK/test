@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Lomas</h1>
            <a href="{{ route('roles.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Izveidot jaunu lomu
            </a>
        </div>

        <!-- Search Form -->
        <div class="mb-6">
            <form action="{{ route('roles.index') }}" method="GET" class="flex">
                <input type="text" name="search" placeholder="Meklēt lomas..." value="{{ $search ?? '' }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 ml-2 rounded">
                    Meklēt
                </button>
                @if($search ?? false)
                    <a href="{{ route('roles.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 ml-2 rounded">
                        Notīrīt
                    </a>
                @endif
            </form>
        </div>

        <!-- Roles List -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-100 font-semibold text-left">
                            <a href="{{ route('roles.index', ['sort_by' => 'name', 'sort_order' => $sortBy === 'name' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                Nosaukums
                                @if($sortBy === 'name')
                                    <i class="fas fa-chevron-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @endif
                            </a>
                        </th>
                        <th class="py-3 px-4 bg-gray-100 font-semibold text-left">Apraksts</th>
                        <th class="py-3 px-4 bg-gray-100 font-semibold text-left">Atļaujas</th>
                        <th class="py-3 px-4 bg-gray-100 font-semibold text-center">Lietotāji</th>
                        <th class="py-3 px-4 bg-gray-100 font-semibold text-center">Darbības</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($items as $role)
                        <tr>
                            <td class="py-3 px-4">{{ $role->name }}</td>
                            <td class="py-3 px-4">{{ $role->description ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <span class="text-sm bg-blue-100 text-blue-800 py-1 px-2 rounded-full">
                                    {{ $role->permissions->count() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="text-sm bg-green-100 text-green-800 py-1 px-2 rounded-full">
                                    {{ $role->users->count() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('roles.show', $role->id) }}" class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('roles.edit', $role->id) }}" class="text-yellow-500 hover:text-yellow-700">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline" onsubmit="return confirm('Vai tiešām vēlaties dzēst šo lomu?');">
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

        <!-- Pagination -->
        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection