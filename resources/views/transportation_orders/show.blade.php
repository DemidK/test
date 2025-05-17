@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Transportēšanas pasūtījums #{{ $order->id }}</h1>
                <p class="text-gray-700 dark:text-gray-300 mt-2">
                    Izveidots: {{ $order->created_at->format('F d, Y H:i') }} 
                    @if($order->partner)
                        | Partneris: {{ $order->partner->name }}
                    @endif
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('transportation_orders.index') }}" 
                   class="inline-flex items-center px-4 py-2 border-2 border-blue-600 rounded-md text-sm font-semibold text-blue-900 dark:text-blue-200 bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800">
                    <i class="fas fa-arrow-left mr-2"></i> Atpakaļ pie pasūtījumiem
                </a>
                <a href="{{ route('transportation_orders.edit', $order) }}" 
                   class="inline-flex items-center px-4 py-2 border-2 border-green-600 rounded-md text-sm font-semibold text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-edit mr-2"></i> Rediģēt pasūtījumu
                </a>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-lg overflow-hidden border-4 border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b-4 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Carrier Section -->
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-blue-500">Pārvadātāja detaļas</h2>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Pārvadātāja nosaukums</span>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $order->carrier_name ?? 'Nav norādīts' }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Reģistrācijas numurs</span>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $order->reg_number ?? 'Nav norādīts' }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Adrese</span>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $order->address ?? 'Nav adreses' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Section -->
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-green-500">Transportlīdzekļa informācija</h2>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Transportlīdzekļa numurs</span>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $order->vehicle_number ?? 'Nav piešķirts' }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Transportlīdzekļa tips</span>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $order->vehicle_type ?? 'Nav norādīts' }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Vadītājs</span>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $order->driver_name ?? 'Nav piešķirts' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Cargo Section -->
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-red-500">Kravas detaļas</h2>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Kravas tips</span>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $order->cargo_type ?? 'Nav norādīts' }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Maksimālais svars</span>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $order->max_tonnage ?? 'Nav norādīts' }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Apjoms</span>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $order->volume_m3 ? $order->volume_m3 . ' m³' : 'Nav norādīts' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-gray-50 dark:bg-gray-900 border-b-4 border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Transport Details -->
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-purple-500">Transportēšanas grafiks</h2>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Iekraušanas adrese</span>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $order->load_address ?? 'Nav norādīts' }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Iekraušanas datums/laiks</span>
                                <p class="text-gray-900 dark:text-white font-bold">
                                    {{ $order->load_datetime ? $order->load_datetime->format('F d, Y H:i') : 'Nav ieplānots' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Izkraušanas adrese</span>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $order->unload_address ?? 'Nav norādīts' }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Izkraušanas datums/laiks</span>
                                <p class="text-gray-900 dark:text-white font-bold">
                                    {{ $order->unload_datetime ? $order->unload_datetime->format('F d, Y H:i') : 'Nav ieplānots' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Details -->
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-2 border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-yellow-500">Finanšu informācija</h2>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Pārvadāšanas summa</span>
                                <p class="text-gray-900 dark:text-white font-bold">
                                    {{ $order->freight_amount ? number_format($order->freight_amount, 2) . ' ' . $order->currency : 'Nav norādīts' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">PVN statuss</span>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $order->vat_status ?? 'Nav norādīts' }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Apmaksas termiņš</span>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $order->payment_term_days }} dienas</p>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Soda summa</span>
                                <p class="text-gray-900 dark:text-white font-bold">{{ $order->penalty_amount ?? 'Nav norādīts' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="p-6 bg-white dark:bg-gray-800">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-2 border-b-2 border-indigo-500">Papildu piezīmes</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Nepieciešamie dokumenti</span>
                        <p class="text-gray-900 dark:text-white font-bold">{{ $order->documents_required ?? 'Nav nepieciešami specifiski dokumenti' }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Īpašie nosacījumi</span>
                        <p class="text-gray-900 dark:text-white font-bold">{{ $order->special_conditions ?? 'Nav īpašu nosacījumu' }}</p>
                    </div>
                </div>
            </div>

            <!-- Delete Action -->
            <div class="bg-gray-100 dark:bg-gray-900 px-6 py-4 flex justify-end border-t-4 border-gray-200 dark:border-gray-700">
                <form action="{{ route('transportation_orders.destroy', $order) }}" 
                    method="POST" 
                    onsubmit="return confirm('Vai tiešām vēlaties dzēst šo transportēšanas pasūtījumu?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border-2 border-red-600 text-sm font-bold rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-trash mr-2"></i> Dzēst pasūtījumu
                    </button>
                </form>
                    <a href="{{ route('transportation_orders.exportPdf', $order) }}" 
                    class="inline-flex items-center px-4 py-2 border-2 border-green-600 rounded-md text-sm font-semibold text-green-900 dark:text-green-200 bg-green-50 dark:bg-green-900 hover:bg-green-100 dark:hover:bg-green-800">
                        <i class="fas fa-file-pdf mr-2"></i> Eksportēt PDF
                    </a>
            </div>
        </div>
    </div>
</div>
@endsection