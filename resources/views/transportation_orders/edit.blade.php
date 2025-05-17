@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Rediģēt transportēšanas pasūtījumu #{{ $order->id }}
                </h1>
                <p class="text-gray-700 dark:text-gray-300 mt-2">
                    Izveidots: {{ $order->created_at->format('F d, Y H:i') }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('transportation_orders.index') }}"
                    class="inline-flex items-center px-4 py-2 border-2 border-blue-600 rounded-md text-sm font-semibold text-blue-900 dark:text-blue-200 bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800">
                    <i class="fas fa-arrow-left mr-2"></i> Atpakaļ pie pasūtījumiem
                </a>
                <a href="{{ route('transportation_orders.show', $order) }}"
                    class="inline-flex items-center px-4 py-2 border-2 border-green-600 rounded-md text-sm font-semibold text-green-900 dark:text-green-200 bg-green-50 dark:bg-green-900 hover:bg-green-100 dark:hover:bg-green-800">
                    <i class="fas fa-eye mr-2"></i> Skatīt pasūtījumu
                </a>
            </div>
        </div>

        <form action="{{ route('transportation_orders.update', $order) }}" method="POST"
            class="bg-white dark:bg-gray-800 shadow-2xl rounded-lg overflow-hidden border-4 border-gray-200 dark:border-gray-700">
            @csrf
            @method('PUT')

            <div class="p-6 bg-gray-100 dark:bg-gray-900 border-b-4 border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Carrier Section -->
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-blue-500">Pārvadātāja detaļas</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Pārvadātāja nosaukums</label>
                                <input type="text" name="carrier_name"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="{{ old('carrier_name', $order->carrier_name) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Reģistrācijas numurs</label>
                                <input type="text" name="reg_number"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="{{ old('reg_number', $order->reg_number) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Adrese</label>
                                <input type="text" name="address"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="{{ old('address', $order->address) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Section -->
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-green-500">Transportlīdzekļa informācija</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Transportlīdzekļa numurs</label>
                                <input type="text" name="vehicle_number"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500"
                                    value="{{ old('vehicle_number', $order->vehicle_number) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Transportlīdzekļa tips</label>
                                <select name="vehicle_type"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">Izvēlieties transportlīdzekļa tipu</option>
                                    @foreach(['Truck', 'Van', 'Trailer', 'Specialized Vehicle'] as $type)
                                    <option value="{{ $type }}"
                                        {{ old('vehicle_type', $order->vehicle_type) == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Vadītāja vārds</label>
                                <input type="text" name="driver_name"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500"
                                    value="{{ old('driver_name', $order->driver_name) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Cargo Section -->
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-red-500">Kravas detaļas</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Kravas tips</label>
                                <select name="cargo_type"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option value="">Izvēlieties kravas tipu</option>
                                    @foreach($cargoTypes as $type)
                                    <option value="{{ $type }}"
                                        {{ old('cargo_type', $order->cargo_type) == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Maksimālais svars</label>
                                <input type="text" name="max_tonnage"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                                    value="{{ old('max_tonnage', $order->max_tonnage) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Apjoms (m³)</label>
                                <input type="text" name="volume_m3"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                                    value="{{ old('volume_m3', $order->volume_m3) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Sections (Transport Schedule, Financial Info, etc.) -->
            <div class="p-6 bg-white dark:bg-gray-800 border-b-4 border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Transport Schedule -->
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-purple-500">Transportēšanas grafiks</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Iekraušanas adrese</label>
                                <input type="text" name="load_address"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    value="{{ old('load_address', $order->load_address) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Iekraušanas datums/laiks</label>
                                <input type="datetime-local" name="load_datetime"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    value="{{ old('load_datetime', $order->load_datetime ? $order->load_datetime->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Izkraušanas adrese</label>
                                <input type="text" name="unload_address"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    value="{{ old('unload_address', $order->unload_address) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Izkraušanas datums/laiks</label>
                                <input type="datetime-local" name="unload_datetime"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    value="{{ old('unload_datetime', $order->unload_datetime ? $order->unload_datetime->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Financial Information -->
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-yellow-500">Finanšu informācija</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Pārvadāšanas summa</label>
                                <div class="flex">
                                    <input type="number" step="0.01" name="freight_amount"
                                        class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-l-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                        value="{{ old('freight_amount', $order->freight_amount) }}">
                                    <select name="currency"
                                        class="px-3 py-2 border-2 border-l-0 border-gray-300 dark:border-gray-600 rounded-r-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                        @foreach(['EUR', 'USD', 'LVL', 'RUR'] as $curr)
                                        <option value="{{ $curr }}"
                                            {{ old('currency', $order->currency) == $curr ? 'selected' : '' }}>
                                            {{ $curr }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">PVN statuss</label>
                                <select name="vat_status"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                    <option value="">Izvēlieties PVN statusu</option>
                                    <option value="+PVN" {{ old('vat_status', $order->vat_status) == '+PVN' ? 'selected' : '' }}>+PVN</option>
                                    <option value="PVN 0%" {{ old('vat_status', $order->vat_status) == 'PVN 0%' ? 'selected' : '' }}>PVN 0%</option>
                                    <option value="bez pvn" {{ old('vat_status', $order->vat_status) == 'bez pvn' ? 'selected' : '' }}>Bez PVN</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Apmaksas termiņš (dienas)</label>
                                <input type="number" name="payment_term_days"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                    value="{{ old('payment_term_days', $order->payment_term_days) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Soda summa</label>
                                <input type="text" name="penalty_amount"
                                    class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                    value="{{ old('penalty_amount', $order->penalty_amount) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="p-6 bg-gray-50 dark:bg-gray-900">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-indigo-500">Papildu dokumenti</h2>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Nepieciešamie dokumenti</label>
                            <textarea name="documents_required"
                                class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                rows="4">{{ old('documents_required', $order->documents_required) }}</textarea>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-teal-500">Īpašie nosacījumi</h2>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Īpašie nosacījumi vai piezīmes</label>
                            <textarea name="special_conditions"
                                class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500"
                                rows="4">{{ old('special_conditions', $order->special_conditions) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Partner Selection -->
            <div class="p-6 bg-white dark:bg-gray-800 border-t-4 border-gray-200 dark:border-gray-700">
                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-green-500">Partnera informācija</h2>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Izvēlieties partneri</label>
                        <select name="partner_id"
                            class="w-full px-3 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Izvēlēties partneri</option>
                            @foreach($partners as $partner)
                            <option value="{{ $partner->id }}"
                                {{ old('partner_id', $order->partner_id) == $partner->id ? 'selected' : '' }}>
                                {{ $partner->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit and Cancel Actions -->
            <div class="bg-gray-100 dark:bg-gray-900 px-6 py-4 flex justify-end border-t-4 border-gray-200 dark:border-gray-700">
                <div class="flex space-x-4">
                    <a href="{{ route('transportation_orders.show', $order) }}"
                        class="inline-flex items-center px-4 py-2 border-2 border-gray-600 rounded-md text-sm font-semibold text-gray-900 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Atcelt
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border-2 border-blue-600 text-sm font-bold rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Atjaunināt transportēšanas pasūtījumu
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Optional: Add any client-side validation or dynamic form behavior
    document.addEventListener('DOMContentLoaded', function() {
        // Example: Validate freight amount
        const freightAmountInput = document.querySelector('input[name="freight_amount"]');
        freightAmountInput.addEventListener('input', function() {
            if (this.value < 0) {
                this.value = 0;
            }
        });
    });
</script>
@endsection