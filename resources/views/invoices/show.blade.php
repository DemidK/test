@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            {{-- Invoice Header --}}
            <div class="bg-gray-100 p-4 flex flex-col sm:flex-row justify-between items-center">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl font-bold text-gray-800">
                        Invoice #{{ $item->invoice_number }}
                    </h1>
                    <p class="text-sm text-gray-600">
                        Issued on {{ \Carbon\Carbon::parse($item->invoice_date)->format('F d, Y') }}
                    </p>
                </div>
                <x-invoice-header-actions :invoice="$item" />
            </div>

            {{-- Customer Information --}}
            <div class="p-4 border-b">
                <x-customer-info :customer="$item" readonly="true" />
            </div>

            {{-- Invoice Items and Totals --}}
            <div class="p-4">
                <h3 class="font-semibold text-gray-700 mb-4">Invoice Items</h3>
                <x-invoice-items-table :items="$item->items" />
            </div>

            {{-- Additional Invoice Details --}}
            <div class="p-4 bg-white border-t text-sm text-gray-600">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <strong>Created By:</strong> 
                        {{ $item->updater ?? 'System' }}
                    </div>
                    <div class="text-right">
                        <strong>Invoice ID:</strong> 
                        {{ $item->id }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection