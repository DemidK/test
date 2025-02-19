@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold mb-8">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Card 1: Total Users -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Total Users</h2>
            <p class="text-2xl font-bold text-blue-600">{{ $totalUsers }}</p>
            <p class="text-gray-600">Registered users</p>
        </div>

        <!-- Card 2: Total Orders -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Total Orders</h2>
            <p class="text-2xl font-bold text-green-600">{{ $totalOrders }}</p>
            <p class="text-gray-600">Completed orders</p>
        </div>

        <!-- Card 3: Revenue -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Revenue</h2>
            <p class="text-2xl font-bold text-purple-600">${{ number_format($totalRevenue, 2) }}</p>
            <p class="text-gray-600">Total revenue</p>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="mt-8">
        <h2 class="text-2xl font-bold mb-4">Recent Activity</h2>
        <div class="bg-white shadow-lg rounded-lg p-6">
            <ul class="space-y-4">
                @foreach ($recentActivities as $activity)
                    <li class="text-gray-700">{{ $activity }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection