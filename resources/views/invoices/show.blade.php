<!-- show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <!-- Navigation Bar -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <!-- Back Button -->
                <a href="{{ url()->previous() }}" 
                   class="text-gray-600 hover:text-gray-900 flex items-center gap-2"
                   title="Atpakaļ">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Atpakaļ</span>
                </a>

                <!-- To List Button -->
                <a href="{{ route('invoices.index') }}" 
                   class="text-gray-600 hover:text-gray-900 flex items-center gap-2"
                   title="Rēķinu saraksts">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>Visi rēķini</span>
                </a>
            </div>

            <div class="flex items-center gap-4">
                <!-- Edit Button -->
                <a href="{{ route('invoices.edit', $items->id) }}" 
                   class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 inline-flex items-center gap-2"
                   title="Rediģēt rēķinu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Rediģēt</span>
                </a>

                <!-- Preview PDF -->
                <a href="{{ route('invoices.previewPdf', $items->id) }}" 
                   target="_blank"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 inline-flex items-center gap-2"
                   title="Priekšskatīt PDF">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>Priekšskatīt</span>
                </a>

                <!-- Download PDF -->
                <a href="{{ route('invoices.exportPdf', $items->id) }}" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 inline-flex items-center gap-2"
                   title="Lejupielādēt PDF">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Lejupielādēt</span>
                </a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            {{-- Invoice Header --}}
            <div class="bg-gray-100 p-4">
                <div class="mb-4">
                    <h1 class="text-2xl font-bold text-gray-800">
                        Rēķins Nr. {{ $items->invoice_number }}
                    </h1>
                    <p class="text-sm text-gray-600">
                        Izsniegts: {{ \Carbon\Carbon::parse($items->invoice_date)->format('d.m.Y') }}
                    </p>
                </div>
            </div>

            {{-- Customer Information --}}
            <div class="p-4 border-b">
                <x-customer-info :customer="$items" readonly="true" />
            </div>

            {{-- Invoice Items Section --}}
            <div class="p-4">
                <h3 class="font-semibold text-gray-700 mb-4">Rēķina pozīcijas</h3>
                
                @if(empty($items->items))
                    <p class="text-gray-500">Šim rēķinam nav atrasta neviena pozīcija.</p>
                @else
                    <!-- Mobile Items List -->
                    <div class="block sm:hidden">
                        @foreach($items->items as $invoiceItem)
                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                <div class="mb-3">
                                    <div class="font-medium text-gray-900">{{ $invoiceItem['description'] }}</div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Daudzums</span>
                                        <span class="font-medium">{{ $invoiceItem['quantity'] }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Cena</span>
                                        <span class="font-medium">€{{ number_format($invoiceItem['price'], 2) }}</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">PVN</span>
                                        <span class="font-medium">{{ $invoiceItem['vat'] }}%</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500">Kopā</span>
                                        <span class="font-medium">€{{ number_format($invoiceItem['quantity'] * $invoiceItem['price'] * (1 + $invoiceItem['vat']/100), 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden sm:block">
                        <x-invoice-items-table :items="$items->items" />
                    </div>
                @endif
            </div>

            {{-- Additional Invoice Details --}}
            <div class="p-4 bg-white border-t text-sm text-gray-600">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <strong>Izveidoja:</strong> 
                        {{ $items->updater ?? 'Sistēma' }}
                    </div>
                    <div class="text-right">
                        <strong>Rēķina ID:</strong> 
                        {{ $items->id }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection