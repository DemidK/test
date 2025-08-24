<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lietotāja tiesību apskate: {{ $user->name }}
            </h2>
            <a href="{{ route('users.edit', $user->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                Rediģēt lomas
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Informācija par lietotāju</h3>
                        <dl class="mt-2 border-t border-b border-gray-200 divide-y divide-gray-200">
                            <div class="py-3 flex justify-between text-sm font-medium">
                                <dt class="text-gray-500">Vārds</dt>
                                <dd class="text-gray-900">{{ $user->name }}</dd>
                            </div>
                            <div class="py-3 flex justify-between text-sm font-medium">
                                <dt class="text-gray-500">Email</dt>
                                <dd class="text-gray-900">{{ $user->email }}</dd>
                            </div>
                            <div class="py-3 flex justify-between text-sm font-medium">
                                <dt class="text-gray-500">Lomas</dt>
                                <dd class="text-gray-900">
                                    @forelse ($user->roles as $role)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Nav lomu</span>
                                    @endforelse
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Kopējās atļaujas (mantotas no lomām)</h3>
                        <p class="text-sm text-gray-500">Šis ir pilns maršrutu nosaukumu saraksts, kuriem lietotājam ir piekļuve.</p>
                        
                        @if (empty($permissionsByGroup))
                            <p class="mt-4 text-gray-500">Lietotājam nav piešķirtu atļauju.</p>
                        @else
                            <div class="mt-4 space-y-4">
                                @foreach($permissionsByGroup as $group => $permissions)
                                    <div class="border-t border-gray-200 pt-4">
                                        <h4 class="text-md font-semibold text-gray-800">{{ $group }}</h4>
                                        <ul class="mt-2 list-disc list-inside space-y-1">
                                            @foreach($permissions as $permission)
                                                <li class="text-gray-700"><code>{{ $permission }}</code></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>