@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
        {{-- Invoice Header --}}
        <div class="bg-gray-100 p-4 flex flex-col sm:flex-row justify-between items-center">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl font-bold text-gray-800">
                    Invoice #{{ $invoice->invoice_number }}
                </h1>
                <p class="text-sm text-gray-600">
                    Issued on {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('F d, Y') }}
                </p>
            </div>
            <div class="flex justify-end gap-4 mb-6">
                <a href="{{ route('invoices.previewPdf', $invoice->id) }}" 
                target="_blank" 
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Priekšskatīt PDF
                </a>
                
                <a href="{{ route('invoices.exportPdf', $invoice->id) }}" 
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Lejupielādēt PDF
                </a>
                
                <a href="{{ route('invoices.edit', $invoice->id) }}" 
                class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            </div>
        </div>

        {{-- Customer Information --}}
        <div class="p-4 border-b">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Customer Details</h3>
                    <p class="text-gray-600">
                        <strong>Vārds / Nosaukums:</strong> {{ $invoice->customer_name }}<br>
                        <strong>E-pasta adrese:</strong> {{ $invoice->customer_email }}<br>
                        @if($invoice->customer_vat)
                            <strong>VAT No:</strong> {{ $invoice->customer_vat }}<br>
                        @endif
                    </p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Address</h3>
                    <p class="text-gray-600">
                        {{ $invoice->customer_address }}<br>
                        @if($invoice->customer_post_address)
                            <strong>Pasta adrese:</strong> {{ $invoice->customer_post_address }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Invoice Items --}}
        <div class="p-4">
            <h3 class="font-semibold text-gray-700 mb-4">Invoice Items</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 text-left">Apraksts</th>
                            <th class="p-2 text-right">Qty</th>
                            <th class="p-2 text-right">Cena</th>
                            <th class="p-2 text-right">VAT</th>
                            <th class="p-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalSubtotal = 0; $totalVat = 0; @endphp
                        @foreach($invoice->items as $item)
                            @php
                                $itemSubtotal = $item['quantity'] * $item['price'];
                                $itemVat = $item['vat'] > 0 ? ($itemSubtotal * $item['vat'] / 100) : 0;
                                $itemTotal = $itemSubtotal + $itemVat;
                                
                                $totalSubtotal += $itemSubtotal;
                                $totalVat += $itemVat;
                            @endphp
                            <tr class="border-b">
                                <td class="p-2">{{ $item['description'] }}</td>
                                <td class="p-2 text-right">{{ $item['quantity'] }}</td>
                                <td class="p-2 text-right">${{ number_format($item['price'], 2) }}</td>
                                <td class="p-2 text-right">{{ $item['vat'] }}%</td>
                                <td class="p-2 text-right">${{ number_format($itemTotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Invoice Totals --}}
        <div class="bg-gray-100 p-4 text-right">
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal (w/o VAT):</span>
                    <span class="font-semibold">${{ number_format($totalSubtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total VAT:</span>
                    <span class="font-semibold">${{ number_format($totalVat, 2) }}</span>
                </div>
                <div class="flex justify-between text-xl font-bold text-green-600">
                    <span>Kopējā summa:</span>
                    <span>${{ number_format($invoice->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Additional Invoice Details --}}
        <div class="p-4 bg-white border-t text-sm text-gray-600">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <strong>Created By:</strong> 
                    {{ $invoice->updater ?? 'System' }}
                </div>
                <div class="text-right">
                    <strong>Invoice ID:</strong> 
                    {{ $invoice->id }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection