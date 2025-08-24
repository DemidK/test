@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Sistēmas Konfigurācija</h1>
            {{-- No create button for configs --}}
        </div>

        <!-- Configs List -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full bg-white dark:bg-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="py-3 px-4 font-semibold text-left text-xs text-gray-600 dark:text-gray-300 uppercase tracking-wider">Atslēga</th>
                        <th class="py-3 px-4 font-semibold text-left text-xs text-gray-600 dark:text-gray-300 uppercase tracking-wider">Apraksts</th>
                        <th class="py-3 px-4 font-semibold text-center text-xs text-gray-600 dark:text-gray-300 uppercase tracking-wider">Darbības</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
                    @forelse($items as $config)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="py-3 px-4 font-mono text-sm">{{ $config->route }}</td>
                            <td class="py-3 px-4">{{ $config->description }}</td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex justify-center">
                                    @can('configs.edit')
                                    <x-button :href="route('configs.edit', $config->route)" variant="secondary">Rediģēt</x-button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 px-4 text-center text-gray-500 dark:text-gray-400">Konfigurācijas nav atrastas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection