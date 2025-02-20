@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Rediģēt rēķinu</h1>
    
    <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoiceForm">
        @csrf
        @method('PUT')
        
        <!-- Basic Invoice Info -->
        <div class="space-y-4 md:space-y-0 md:grid md:grid-cols-2 md:gap-4 mb-6">
            <div>
                <label for="invoice_number" class="block text-gray-700 mb-2">Rēķina numurs</label>
                <input type="text" name="invoice_number" id="invoice_number" 
                    class="w-full px-4 py-2 border rounded-lg" 
                    value="{{ old('invoice_number', $invoice->invoice_number) }}" required>
            </div>
            <div>
                <label for="invoice_date" class="block text-gray-700 mb-2">Rēķina datums</label>
                <input type="date" name="invoice_date" id="invoice_date" 
                    class="w-full px-4 py-2 border rounded-lg" 
                    value="{{ old('invoice_date', $invoice->invoice_date) }}" required>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="space-y-4 md:space-y-0 md:grid md:grid-cols-2 md:gap-4 mb-6">
            <div class="md:col-span-1">
                <label for="customer_id" class="block text-gray-700 mb-2">Klienta ID</label>
                <input type="number" name="customer_id" id="customer_id" 
                    class="w-full px-4 py-2 border rounded-lg"
                    value="{{ old('customer_id', $invoice->customer_id) }}">
            </div>
            <div class="md:col-span-1">
                <label for="customer_vat" class="block text-gray-700 mb-2">Customer VAT Number</label>
                <input type="text" name="customer_vat" id="customer_vat" 
                    class="w-full px-4 py-2 border rounded-lg"
                    value="{{ old('customer_vat', $invoice->customer_vat) }}">
            </div>
            <div class="md:col-span-1">
                <label for="customer_name" class="block text-gray-700 mb-2">Vārds / Nosaukums</label>
                <input type="text" name="customer_name" id="customer_name" 
                    class="w-full px-4 py-2 border rounded-lg" 
                    value="{{ old('customer_name', $invoice->customer_name) }}" required>
            </div>
            <div class="md:col-span-1">
                <label for="customer_email" class="block text-gray-700 mb-2">Klienta e-pasts</label>
                <input type="email" name="customer_email" id="customer_email" 
                    class="w-full px-4 py-2 border rounded-lg" 
                    value="{{ old('customer_email', $invoice->customer_email) }}" required>
            </div>
            <div class="md:col-span-1">
                <label for="customer_address" class="block text-gray-700 mb-2">Juridisko adrese</label>
                <textarea name="customer_address" id="customer_address" 
                        class="w-full px-4 py-2 border rounded-lg" 
                        rows="3" required>{{ old('customer_address', $invoice->customer_address) }}</textarea>
            </div>
            <div class="md:col-span-1">
                <label for="customer_post_address" class="block text-gray-700 mb-2">Pasta adrese (if different)</label>
                <textarea name="customer_post_address" id="customer_post_address" 
                        class="w-full px-4 py-2 border rounded-lg" 
                        rows="3">{{ old('customer_post_address', $invoice->customer_post_address) }}</textarea>
            </div>
        </div>

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
            @foreach(old('items', $invoice->items) as $index => $item)
            <div class="item grid grid-cols-6 gap-4 mb-2 p-3 bg-gray-50 rounded-lg">
                <div class="col-span-2">
                    <input type="text" 
                        name="items[{{ $index }}][description]" 
                        placeholder="Apraksts" 
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-200" 
                        value="{{ $item['description'] }}" 
                        required>
                </div>
                <div>
                    <input type="number" 
                        name="items[{{ $index }}][quantity]" 
                        placeholder="Qty" 
                        min="1" 
                        class="w-full px-3 py-2 border rounded-lg text-center item-qty focus:ring-2 focus:ring-blue-200" 
                        value="{{ $item['quantity'] }}" 
                        required>
                </div>
                <div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" 
                            name="items[{{ $index }}][price]" 
                            placeholder="0.00" 
                            step="0.01" 
                            min="0" 
                            class="w-full px-3 py-2 pl-7 border rounded-lg text-right item-price focus:ring-2 focus:ring-blue-200" 
                            value="{{ $item['price'] }}" 
                            required>
                    </div>
                </div>
                <div>
                    <div class="relative">
                        <input type="number" 
                            name="items[{{ $index }}][vat]" 
                            placeholder="0" 
                            min="0" 
                            max="100" 
                            class="w-full px-3 py-2 pr-7 border rounded-lg text-right item-vat focus:ring-2 focus:ring-blue-200" 
                            value="{{ $item['vat'] }}" 
                            required>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="item-total font-medium">{{ number_format($item['quantity'] * $item['price'] * (1 + $item['vat']/100), 2) }}</span>
                    <button type="button" class="remove-item text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
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

<!-- Totals Section -->
<div class="flex justify-end mt-6">
    <div class="text-xl font-bold text-right space-y-2">
        <div class="text-gray-600">Total (w/o VAT): $<span id="total-wo-vat-display">{{ number_format($invoice->total_wo_vat, 2) }}</span></div>
        <div class="text-gray-600">Total VAT: $<span id="total-vat-display">{{ number_format($invoice->total_vat, 2) }}</span></div>
        <div class="text-gray-900">Kopējā summa: $<span id="total-display">{{ number_format($invoice->total_amount, 2) }}</span></div>
        
        <input type="hidden" name="total_amount" id="total_amount" value="{{ $invoice->total_amount }}">
        <input type="hidden" name="total_vat" id="total_vat" value="{{ $invoice->total_vat }}">
        <input type="hidden" name="total_wo_vat" id="total_wo_vat" value="{{ $invoice->total_wo_vat }}">
    </div>
</div>
<!-- Form Actions -->
<div class="flex justify-end mt-6">
    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors duration-200">
        Rediģet rēķinu
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('items-container');
    const addButton = document.getElementById('add-item');
    
    // Handle numeric input to prevent leading zeros
    function handleNumericInput(input) {
        let value = input.value;
        
        // Handle decimal numbers
        if (value.includes('.')) {
            let [whole, decimal] = value.split('.');
            // Remove leading zeros from whole part
            if (whole.length > 1 && whole.startsWith('0')) {
                whole = parseFloat(whole) || '0';
            }
            value = whole + '.' + decimal;
        } 
        // Handle whole numbers
        else if (value.length > 1 && value.startsWith('0')) {
            value = parseFloat(value) || '0';
        }

        // Update input value
        input.value = value;
        return value;
    }

    // Calculate totals for a single item
    function calculateItemTotal(item) {
        const qty = parseFloat(item.querySelector('.item-qty').value) || 0;
        const price = parseFloat(item.querySelector('.item-price').value) || 0;
        const vat = parseFloat(item.querySelector('.item-vat').value) || 0;
        
        const subtotal = qty * price;
        const vatAmount = vat > 0 ? (subtotal * vat / 100) : 0;
        const total = subtotal + vatAmount;
        
        // Update total displays in both mobile and desktop views
        item.querySelectorAll('.item-total').forEach(el => {
            el.textContent = '$' + formatCurrency(total);
        });
        
        return { subtotal, vatAmount, total };
    }

    // Format currency
    function formatCurrency(number) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(number);
    }

    // Update all totals
    function updateTotals() {
        let grandTotal = 0;
        let totalVat = 0;
        let subtotal = 0;

        container.querySelectorAll('.item').forEach(item => {
            const itemTotals = calculateItemTotal(item);
            grandTotal += itemTotals.total;
            totalVat += itemTotals.vatAmount;
            subtotal += itemTotals.subtotal;
        });
        
        // Update display totals
        document.getElementById('total-display').textContent = formatCurrency(grandTotal);
        document.getElementById('total-vat-display').textContent = formatCurrency(totalVat);
        document.getElementById('total-wo-vat-display').textContent = formatCurrency(subtotal);
        
        // Update hidden inputs
        document.getElementById('total_amount').value = grandTotal.toFixed(2);
        document.getElementById('total_vat').value = totalVat.toFixed(2);
        document.getElementById('total_wo_vat').value = subtotal.toFixed(2);
    }

    // Add event listeners to an item
    function addItemListeners(item) {
        // Input change listeners
        item.querySelectorAll('input').forEach(input => {
            if (input.type === 'number') {
                // Handle input event for numeric fields
                input.addEventListener('input', () => {
                    handleNumericInput(input);
                    updateTotals();
                });

                // Handle blur event for numeric fields
                input.addEventListener('blur', () => {
                    if (input.value === '') {
                        if (input.classList.contains('item-qty')) {
                            input.value = '1';
                        } else {
                            input.value = '0';
                        }
                    }
                    // Format decimal numbers
                    if (input.classList.contains('item-price')) {
                        input.value = parseFloat(input.value).toFixed(2);
                    }
                    updateTotals();
                });
            } else {
                // Regular input update for non-numeric fields
                input.addEventListener('input', updateTotals);
            }
        });
        
        // Remove item button
        const removeButton = item.querySelector('.remove-item');
        if (removeButton) {
            removeButton.addEventListener('click', function() {
                if (container.querySelectorAll('.item').length > 1) {
                    item.remove();
                    updateTotals();
                } else {
                    alert('At least one item is required.');
                }
            });
        }
    }

    // Add new item
    addButton.addEventListener('click', function() {
        const itemCount = container.querySelectorAll('.item').length;
        const newItem = document.createElement('div');
        newItem.className = 'item grid grid-cols-6 gap-4 mb-2 p-3 bg-gray-50 rounded-lg';  // Updated to match existing items
        newItem.innerHTML = `
            <div class="col-span-2">
                <input type="text" 
                    name="items[${itemCount}][description]" 
                    placeholder="Apraksts" 
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-200" 
                    required>
            </div>
            <div>
                <input type="number" 
                    name="items[${itemCount}][quantity]" 
                    placeholder="Qty" 
                    min="1" 
                    class="w-full px-3 py-2 border rounded-lg text-center item-qty focus:ring-2 focus:ring-blue-200" 
                    value="1" 
                    required>
            </div>
            <div>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                    <input type="number" 
                        name="items[${itemCount}][price]" 
                        placeholder="0.00" 
                        step="0.01" 
                        min="0" 
                        class="w-full px-3 py-2 pl-7 border rounded-lg text-right item-price focus:ring-2 focus:ring-blue-200" 
                        value="0" 
                        required>
                </div>
            </div>
            <div>
                <div class="relative">
                    <input type="number" 
                        name="items[${itemCount}][vat]" 
                        placeholder="0" 
                        min="0" 
                        max="100" 
                        class="w-full px-3 py-2 pr-7 border rounded-lg text-right item-vat focus:ring-2 focus:ring-blue-200" 
                        value="0" 
                        required>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="item-total font-medium">$0.00</span>
                <button type="button" class="remove-item text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        `;
        
        container.appendChild(newItem);
        addItemListeners(newItem);
        updateTotals();
        
        // Focus on the description field of the new item
        newItem.querySelector('input[type="text"]').focus();
    });

    // Initialize listeners for existing items
    container.querySelectorAll('.item').forEach(addItemListeners);
    updateTotals();
});
</script>
@endsection