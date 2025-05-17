@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-0">Izveidot jaunu transportēšanas pasūtījumu</h1>
            <a href="{{ route('transportation_orders.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i> Atpakaļ pie pasūtījumiem
            </a>
        </div>
        <form action="{{ route('transportation_orders.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Carrier Section -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Pārvadātāja informācija</h2>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pārvadātāja nosaukums</label>
                    <input type="text" name="carrier_name" class="shadow appearance-none border rounded w-full py-2 px-3" 
                           value="{{ old('carrier_name') }}">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Reģistrācijas numurs</label>
                    <input type="text" name="reg_number" class="shadow appearance-none border rounded w-full py-2 px-3" 
                           value="{{ old('reg_number') }}">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Adrese</label>
                    <input type="text" name="address" class="shadow appearance-none border rounded w-full py-2 px-3" 
                           value="{{ old('address') }}">
                </div>
            </div>

            <!-- Vehicle Section -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Transportlīdzekļa detaļas</h2>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Transportlīdzekļa tips</label>
                    <select name="vehicle_type" class="shadow appearance-none border rounded w-full py-2 px-3">
                        <option value="">Izvēlieties transportlīdzekļa tipu</option>
                        <!-- Add vehicle type options -->
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Transportlīdzekļa numurs</label>
                    <input type="text" name="vehicle_number" class="shadow appearance-none border rounded w-full py-2 px-3" 
                           value="{{ old('vehicle_number') }}">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Vadītāja vārds</label>
                    <input type="text" name="driver_name" class="shadow appearance-none border rounded w-full py-2 px-3" 
                           value="{{ old('driver_name') }}">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between mt-6">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Izveidot transportēšanas pasūtījumu
            </button>
        </div>
    </form>
</div>
@endsection