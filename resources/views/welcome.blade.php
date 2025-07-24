@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Welcome to {{ config('app.name', 'Laravel') }}</h1>

        <!-- Featured Content Section -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
            <p class="text-gray-700 text-lg">{{ $featuredContent }}</p>
        </div>

        <!-- Recent Updates Section -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">Recent Updates</h2>
            <div class="bg-white shadow-lg rounded-lg p-6">
                <ul class="space-y-4">
                    @foreach ($recentUpdates as $update)
                        <li class="text-gray-700">{{ $update }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Call to Action Buttons -->
        <div class="mt-8 flex justify-center space-x-4">
            @guest
                <a href="{{ route('login') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Pieslēgties</a>
                <a href="{{ route('register.tenant') }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">Reģistrēties</a>
            @else
                <a href="{{ route('dashboard') }}" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">Go to Vadības panelis</a>
            @endguest
        </div>
    </div>
</div>
@endsection