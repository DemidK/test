@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Izveidot rēķinu</h1>
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Oops! Something went wrong.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
            @csrf
            
            <!-- Basic Invoice Info -->
            <div class="space-y-4 mb-6">
                <div>
                    <label for="invoice_number" class="block text-gray-700 mb-2">Rēķina numurs</label>
                    <input type="text" 
                           name="invoice_number" 
                           id="invoice_number" 
                           class="w-full px-4 py-2 border rounded-lg" 
                           value="{{ old('invoice_number') }}" 
                           required>
                </div>
                <div>
                    <label for="invoice_date" class="block text-gray-700 mb-2">Rēķina datums</label>
                    <input type="date" 
                           name="invoice_date" 
                           id="invoice_date" 
                           class="w-full px-4 py-2 border rounded-lg" 
                           value="{{ old('invoice_date', date('Y-m-d')) }}" 
                           required>
                </div>
            </div>

            <!-- Customer Information Component -->
            <x-customer-info :customer="null" />

            <!-- Items Header -->
            <div class="grid grid-cols-6 gap-4 mb-2 font-bold">
                <div class="col-span-2">Apraksts</div>
                <div>Qty</div>
                <div>Cena</div>
                <div>VAT %</div>
                <div>Total</div>
            </div>

            <!-- Items Container -->
            <div x-data="{
                items: [{ description: '', quantity: 1, price: 0, vat: 0 }],
                addItem() {
                    this.items.push({ description: '', quantity: 1, price: 0, vat: 0 });
                }
            }">
                <div id="items-container">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="item grid grid-cols-6 gap-4 mb-2 p-3 bg-gray-50 rounded-lg">
                            <div class="col-span-2">
                                <input type="text" 
                                    x-bind:name="'items[' + index + '][description]'"
                                    x-model="item.description"
                                    placeholder="Apraksts" 
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-200" 
                                    required>
                            </div>
                            <div>
                                <input type="number" 
                                    x-bind:name="'items[' + index + '][quantity]'"
                                    x-model.number="item.quantity"
                                    placeholder="Qty" 
                                    min="1" 
                                    class="w-full px-3 py-2 border rounded-lg text-center focus:ring-2 focus:ring-blue-200" 
                                    required>
                            </div>
                            <div>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                                    <input type="number" 
                                        x-bind:name="'items[' + index + '][price]'"
                                        x-model.number="item.price"
                                        placeholder="0.00" 
                                        step="0.01" 
                                        min="0" 
                                        class="w-full px-3 py-2 pl-7 border rounded-lg text-right focus:ring-2 focus:ring-blue-200" 
                                        required>
                                </div>
                            </div>
                            <div>
                                <div class="relative">
                                    <input type="number" 
                                        x-bind:name="'items[' + index + '][vat]'"
                                        x-model.number="item.vat"
                                        placeholder="0" 
                                        min="0" 
                                        max="100" 
                                        class="w-full px-3 py-2 pr-7 border rounded-lg text-right focus:ring-2 focus:ring-blue-200" 
                                        required>
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-medium" x-text="'$' + ((item.quantity || 0) * (item.price || 0) * (1 + (item.vat || 0)/100)).toFixed(2)"></span>
                                <button type="button" 
                                        @click="items.splice(index, 1)" 
                                        class="text-red-500 hover:text-red-700"
                                        x-show="items.length > 1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <button type="button" 
                        @click="addItem"
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg mt-4 hover:bg-blue-700 
                            inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Pievienot preci
                </button>

                <x-invoice-totals :editable="true" />
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end mt-6">
                <button type="submit" 
                        class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors duration-200">
                    Izveidot rēķinu
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/invoice-calculations.js') }}"></script>
@endpush

@endsection