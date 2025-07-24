{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">

        {{-- Блок для отображения сообщений об успехе или ошибке --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Заголовок и кнопка обновления --}}
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Vadības panelis</h1>

            {{-- Условное отображение кнопки --}}
            @if(isset($updateUrl) && $updateUrl)
                <a href="{{ $updateUrl }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                   onclick="return confirm('Вы уверены, что хотите обновить приложение? Это действие получит последние изменения из Git и очистит кэш.');">
                   <i class="fas fa-sync-alt mr-2"></i>Atjaunināt Aplikāciju
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold mb-2">Kopējais lietotāju skaits</h3>
                <p class="text-2xl font-bold text-blue-600">{{ $totalUsers }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold mb-2">Kopējais klientu skaits</h3>
                <p class="text-2xl font-bold text-green-600">{{ $totalPartners }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold mb-2">Ieņēmumi</h3>
                <p class="text-2xl font-bold text-purple-600">$12,345.00</p>
                <p class="text-gray-600 text-sm">Total revenue</p>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">Pēdējās darbības</h2>
            <div class="bg-white rounded-lg shadow-md p-4">
                <ul class="space-y-3">
                    <li class="text-gray-700 text-sm md:text-base">User "John Doe" placed an order.</li>
                    <li class="text-gray-700 text-sm md:text-base">User "Jane Smith" registered.</li>
                    <li class="text-gray-700 text-sm md:text-base">Order #123 was completed.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection