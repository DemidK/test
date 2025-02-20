@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Vadības panelis</h1>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Kopējais lietotāju skaits Card -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold mb-2">Kopējais lietotāju skaits</h3>
                <p class="text-2xl font-bold text-blue-600">{{ $totalUsers }}</p>
            </div>

            <!-- Kopējais klientu skaits Card -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold mb-2">Kopējais klientu skaits</h3>
                <p class="text-2xl font-bold text-green-600">{{ $totalClients }}</p>
            </div>

            <!-- Revenue Card -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="text-lg font-semibold mb-2">Ieņēmumi</h3>
                <p class="text-2xl font-bold text-purple-600">$12,345.00</p>
                <p class="text-gray-600 text-sm">Total revenue</p>
            </div>
        </div>

        <!-- Recent Activity Section -->
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