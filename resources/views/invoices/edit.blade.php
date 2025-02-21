@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Rediģēt rēķinu</h1>

        <form action="{{ route('invoices.update', ['invoice' => $item]) }}" method="POST" id="invoiceForm">
            @csrf
            @method('PUT')
            
            <!-- Basic Invoice Info -->
            <div class="space-y-4 md:space-y-0 md:grid md:grid-cols-2 md:gap-4 mb-6">
                <div>
                    <label for="invoice_number" class="block text-gray-700 mb-2">Rēķina numurs</label>
                    <input type="text" 
                           name="invoice_number" 
                           id="invoice_number" 
                           class="w-full px-4 py-2 border rounded-lg" 
                           value="{{ old('invoice_number', $item->invoice_number) }}" 
                           required>
                </div>
                <div>
                    <label for="invoice_date" class="block text-gray-700 mb-2">Rēķina datums</label>
                    <input type="date" 
                           name="invoice_date" 
                           id="invoice_date" 
                           class="w-full px-4 py-2 border rounded-lg" 
                           value="{{ old('invoice_date', $item->invoice_date) }}" 
                           required>
                </div>
            </div>

            <!-- Customer Info Component -->
            <x-customer-info :customer="$item" />

            <!-- Items Header -->
            <div class="grid grid-cols-6 gap-4 mb-2 font-bold text-gray-700">
                <div class="col-span-2">Apraksts</div>
                <div class="text-center">Qty</div>
                <div class="text-center">Cena</div>
                <div class="text-center">VAT %</div>
                <div class="text-right">Total</div>
            </div>

            <!-- Items Container -->
            <div id="items-container">
                @foreach(old('items', $item->items) as $index => $invoiceItem)
                    <x-invoice-items :item="$invoiceItem" :index="$index" />
                @endforeach
            </div>

            <!-- Add Item Button -->
            <button type="button" 
                    id="add-item" 
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg mt-4 hover:bg-blue-700 
                           inline-flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Pievienot preci
            </button>

            <!-- Totals Component -->
            <x-invoice-totals 
                :totalWoVat="$item->total_wo_vat" 
                :totalVat="$item->total_vat" 
                :totalAmount="$item->total_amount" 
                :editable="true" />

            <!-- Form Actions -->
            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors duration-200">
                    Rediģet rēķinu
                </button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/invoice-calculations.js') }}"></script>

@endsection