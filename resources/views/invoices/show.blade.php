@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Invoice Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold mb-2">Invoice #{{ $invoice->invoice_number }}</h1>
                <p class="text-gray-600">Date: {{ $invoice->invoice_date }}</p>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-3">Bill To:</h2>
            <p class="text-gray-700">{{ $invoice->customer_name }}</p>
            <p class="text-gray-700">{{ $invoice->customer_email }}</p>
            <p class="text-gray-700 whitespace-pre-line">{{ $invoice->customer_address }}</p>
        </div>

        <!-- Items Table -->
        <div class="mb-8">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-gray-300">
                        <th class="text-left py-2">Description</th>
                        <th class="text-right py-2">Quantity</th>
                        <th class="text-right py-2">Price</th>
                        <th class="text-right py-2">VAT %</th>
                        <th class="text-right py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr class="border-b border-gray-200">
                        <td class="py-2">{{ $item['description'] }}</td>
                        <td class="text-right py-2">{{ $item['quantity'] }}</td>
                        <td class="text-right py-2">${{ number_format($item['price'], 2) }}</td>
                        <td class="text-right py-2">{{ $item['vat'] }}%</td>
                        <td class="text-right py-2">${{ number_format($item['total'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="flex justify-end">
            <div class="w-64">
                @php
                    $items = $invoice->items;
                    $subtotal = array_sum(array_map(function($item) {
                        return $item['quantity'] * $item['price'];
                    }, $items));
                    $vatTotal = array_sum(array_map(function($item) {
                        $itemSubtotal = $item['quantity'] * $item['price'];
                        return $item['vat'] > 0 ? ($itemSubtotal * $item['vat'] / 100) : 0;
                    }, $items));
                @endphp
                <div class="flex justify-between py-2">
                    <span class="font-medium">Subtotal:</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="font-medium">VAT Total:</span>
                    <span>${{ number_format($vatTotal, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-t border-gray-300 font-bold">
                    <span>Total:</span>
                    <span>${{ number_format($invoice->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('invoices.index') }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to List
            </a>
            <a href="{{ route('invoices.pdf', $invoice->id) }}" 
               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Download PDF
            </a>
        </div>
    </div>
</div>
@endsection