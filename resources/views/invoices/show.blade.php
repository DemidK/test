@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
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
            <div class="flex space-x-2">
                <a href="{{ route('invoices.edit', $invoice) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg text-sm">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('invoices.exportPdf', $invoice->id) }}" 
                   class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg text-sm">
                    <i class="fas fa-file-pdf mr-2"></i>PDF
                </a>
            </div>
        </div>

        {{-- Customer Information --}}
        <div class="p-4 border-b">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Customer Details</h3>
                    <p class="text-gray-600">
                        <strong>Name:</strong> {{ $invoice->customer_name }}<br>
                        <strong>Email:</strong> {{ $invoice->customer_email }}<br>
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
                            <strong>Postal Address:</strong> {{ $invoice->customer_post_address }}
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
                            <th class="p-2 text-left">Description</th>
                            <th class="p-2 text-right">Qty</th>
                            <th class="p-2 text-right">Price</th>
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
                    <span>Total Amount:</span>
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