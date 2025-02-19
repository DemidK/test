@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold mb-8">Create Invoice</h1>
    
    <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
        @csrf
        <!-- Basic Invoice Info -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label for="invoice_number" class="block text-gray-700 mb-2">Invoice Number</label>
                <input type="text" name="invoice_number" id="invoice_number" 
                       class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div>
                <label for="invoice_date" class="block text-gray-700 mb-2">Invoice Date</label>
                <input type="date" name="invoice_date" id="invoice_date" 
                       class="w-full px-4 py-2 border rounded-lg" 
                       value="{{ date('Y-m-d') }}" required>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label for="customer_name" class="block text-gray-700 mb-2">Customer Name</label>
                <input type="text" name="customer_name" id="customer_name" 
                       class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div>
                <label for="customer_email" class="block text-gray-700 mb-2">Customer Email</label>
                <input type="email" name="customer_email" id="customer_email" 
                       class="w-full px-4 py-2 border rounded-lg" required>
            </div>
        </div>
        
        <div class="mb-6">
            <label for="customer_address" class="block text-gray-700 mb-2">Customer Address</label>
            <textarea name="customer_address" id="customer_address" 
                      class="w-full px-4 py-2 border rounded-lg" required></textarea>
        </div>

        <!-- Items Header -->
        <div class="grid grid-cols-6 gap-4 mb-2 font-bold">
            <div class="col-span-2">Description</div>
            <div>Qty</div>
            <div>Price</div>
            <div>VAT %</div>
            <div>Total</div>
        </div>

        <!-- Items Container -->
        <div id="items-container">
            <div class="item grid grid-cols-6 gap-4 mb-2">
                <div class="col-span-2">
                    <input type="text" name="items[0][description]" placeholder="Description" 
                           class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                <div>
                    <input type="number" name="items[0][quantity]" placeholder="Qty" min="1" 
                           class="w-full px-4 py-2 border rounded-lg item-qty" required>
                </div>
                <div>
                    <input type="number" name="items[0][price]" placeholder="Price" step="0.01" min="0" 
                           class="w-full px-4 py-2 border rounded-lg item-price" required>
                </div>
                <div>
                    <input type="number" name="items[0][vat]" placeholder="VAT" min="0" max="100" 
                           class="w-full px-4 py-2 border rounded-lg item-vat" value="0" required>
                </div>
                <div class="flex items-center justify-between">
                    <span class="item-total">0.00</span>
                    <button type="button" class="remove-item text-red-500 hover:text-red-700">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <button type="button" id="add-item" 
                class="bg-blue-600 text-white px-4 py-2 rounded-lg mt-4 hover:bg-blue-700">
            Add Item
        </button>

        <!-- Total Amount -->
        <div class="flex justify-end mt-6">
            <div class="text-xl font-bold">
                Total Amount: $<span id="total-display">0.00</span>
                <input type="hidden" name="total_amount" id="total_amount" value="0">
            </div>
        </div>

        <div class="flex justify-end mt-6">
            <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                Create Invoice
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('items-container');
    const addButton = document.getElementById('add-item');
    
    function calculateItemTotal(item) {
        const qty = parseFloat(item.querySelector('.item-qty').value) || 0;
        const price = parseFloat(item.querySelector('.item-price').value) || 0;
        const vat = parseFloat(item.querySelector('.item-vat').value) || 0;
        
        const subtotal = qty * price;
        const vatAmount = vat > 0 ? (subtotal * vat / 100) : 0;
        const total = subtotal + vatAmount;
        
        item.querySelector('.item-total').textContent = total.toFixed(2);
        return total;
    }

    function updateTotals() {
        let grandTotal = 0;
        container.querySelectorAll('.item').forEach(item => {
            grandTotal += calculateItemTotal(item);
        });
        
        document.getElementById('total-display').textContent = grandTotal.toFixed(2);
        document.getElementById('total_amount').value = grandTotal;
    }

    function addItemListeners(item) {
        item.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', updateTotals);
        });
        
        item.querySelector('.remove-item').addEventListener('click', function() {
            if (container.querySelectorAll('.item').length > 1) {
                item.remove();
                updateTotals();
            }
        });
    }

    addButton.addEventListener('click', function() {
        const itemCount = container.querySelectorAll('.item').length;
        const newItem = document.createElement('div');
        newItem.className = 'item grid grid-cols-6 gap-4 mb-2';
        newItem.innerHTML = `
            <div class="col-span-2">
                <input type="text" name="items[${itemCount}][description]" placeholder="Description" 
                       class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div>
                <input type="number" name="items[${itemCount}][quantity]" placeholder="Qty" min="1" 
                       class="w-full px-4 py-2 border rounded-lg item-qty" required>
            </div>
            <div>
                <input type="number" name="items[${itemCount}][price]" placeholder="Price" step="0.01" min="0" 
                       class="w-full px-4 py-2 border rounded-lg item-price" required>
            </div>
            <div>
                <input type="number" name="items[${itemCount}][vat]" placeholder="VAT" min="0" max="100" 
                       class="w-full px-4 py-2 border rounded-lg item-vat" value="0" required>
            </div>
            <div class="flex items-center justify-between">
                <span class="item-total">0.00</span>
                <button type="button" class="remove-item text-red-500 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newItem);
        addItemListeners(newItem);
    });

    // Initialize listeners for the first item
    container.querySelectorAll('.item').forEach(addItemListeners);
});
</script>
@endsection