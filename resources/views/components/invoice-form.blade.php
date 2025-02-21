<!-- resources/views/components/invoice-form.blade.php -->
@props(['invoice' => null, 'action', 'method' => 'POST'])

<form action="{{ $action }}" method="POST" id="invoiceForm">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    @php
        $defaultItem = ['description' => '', 'quantity' => 1, 'price' => 0, 'vat' => 0];
        $items = $invoice ? old('items', $invoice->items) : [$defaultItem];
    @endphp
    
    <!-- Basic Invoice Info -->
    <div class="space-y-4 md:space-y-0 md:grid md:grid-cols-2 md:gap-4 mb-6">
        <div>
            <label for="invoice_number" class="block text-gray-700 mb-2">Rēķina numurs</label>
            <input type="text" 
                   name="invoice_number" 
                   id="invoice_number" 
                   class="w-full px-4 py-2 border rounded-lg" 
                   value="{{ old('invoice_number', $invoice?->invoice_number) }}" 
                   required>
        </div>
        <div>
            <label for="invoice_date" class="block text-gray-700 mb-2">Rēķina datums</label>
            <input type="date" 
                   name="invoice_date" 
                   id="invoice_date" 
                   class="w-full px-4 py-2 border rounded-lg" 
                   value="{{ old('invoice_date', $invoice?->invoice_date ?? date('Y-m-d')) }}" 
                   required>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
            <label for="customer_id" class="block text-gray-700 mb-2">Klienta ID</label>
            <input type="text" 
                   name="customer_id" 
                   id="customer_id" 
                   class="w-full px-4 py-2 border rounded-lg" 
                   value="{{ old('customer_id', $invoice?->customer_id) }}">
        </div>
        <div>
            <label for="customer_vat" class="block text-gray-700 mb-2">Customer VAT Number</label>
            <input type="text" 
                   name="customer_vat" 
                   id="customer_vat" 
                   class="w-full px-4 py-2 border rounded-lg" 
                   value="{{ old('customer_vat', $invoice?->customer_vat) }}">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
            <label for="customer_name" class="block text-gray-700 mb-2">Klienta vārds / nosaukums</label>
            <input type="text" 
                   name="customer_name" 
                   id="customer_name" 
                   class="w-full px-4 py-2 border rounded-lg" 
                   value="{{ old('customer_name', $invoice?->customer_name) }}">
        </div>
        <div>
            <label for="customer_email" class="block text-gray-700 mb-2">Klienta e-pasts</label>
            <input type="email" 
                   name="customer_email" 
                   id="customer_email" 
                   class="w-full px-4 py-2 border rounded-lg" 
                   value="{{ old('customer_email', $invoice?->customer_email) }}">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
            <label for="customer_address" class="block text-gray-700 mb-2">Juridisko adrese</label>
            <textarea name="customer_address" 
                      id="customer_address" 
                      class="w-full px-4 py-2 border rounded-lg" 
                      rows="3">{{ old('customer_address', $invoice?->customer_address) }}</textarea>
        </div>
        <div>
            <label for="customer_post_address" class="block text-gray-700 mb-2">Pasta adrese (if different)</label>
            <textarea name="customer_post_address" 
                      id="customer_post_address" 
                      class="w-full px-4 py-2 border rounded-lg" 
                      rows="3">{{ old('customer_post_address', $invoice?->customer_post_address) }}</textarea>
        </div>
    </div>

    <!-- Items Section -->
    <div x-data="{
        items: {{ json_encode($items) }},
        
        addItem() {
            this.items.push({
                description: '',
                quantity: 1,
                price: 0,
                vat: 0
            });
        },
        
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        
        calculateItemTotal(item) {
            return ((item.quantity || 0) * (item.price || 0) * (1 + (item.vat || 0)/100)).toFixed(2);
        },
        
        calculateSubtotal() {
            return this.items.reduce((sum, item) => 
                sum + (item.quantity || 0) * (item.price || 0), 0
            ).toFixed(2);
        },
        
        calculateVat() {
            return this.items.reduce((sum, item) => 
                sum + (item.quantity || 0) * (item.price || 0) * ((item.vat || 0)/100), 0
            ).toFixed(2);
        },
        
        calculateTotal() {
            return this.items.reduce((sum, item) => 
                sum + (item.quantity || 0) * (item.price || 0) * (1 + (item.vat || 0)/100), 0
            ).toFixed(2);
        }
    }">
        <!-- Items Header -->
        <div class="grid grid-cols-6 gap-4 mb-2 font-bold text-gray-700">
            <div class="col-span-2">Apraksts</div>
            <div class="text-center">Qty</div>
            <div class="text-center">Cena</div>
            <div class="text-center">VAT %</div>
            <div class="text-right">Total</div>
        </div>

        <!-- Items Container -->
        <template x-for="(item, index) in items" :key="index">
            <div class="grid grid-cols-6 gap-4 mb-2 p-3 bg-gray-50 rounded-lg">
                <div class="col-span-2">
                    <input type="text" 
                           x-model="item.description"
                           :name="'items[' + index + '][description]'"
                           class="w-full px-3 py-2 border rounded-lg" 
                           required>
                </div>
                <div>
                    <input type="number" 
                           x-model.number="item.quantity"
                           :name="'items[' + index + '][quantity]'"
                           min="1" 
                           class="w-full px-3 py-2 border rounded-lg text-center" 
                           required>
                </div>
                <div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" 
                               x-model.number="item.price"
                               :name="'items[' + index + '][price]'"
                               step="0.01" 
                               min="0" 
                               class="w-full px-3 py-2 pl-7 border rounded-lg text-right" 
                               required>
                    </div>
                </div>
                <div>
                    <div class="relative">
                        <input type="number" 
                               x-model.number="item.vat"
                               :name="'items[' + index + '][vat]'"
                               min="0" 
                               max="100" 
                               class="w-full px-3 py-2 pr-7 border rounded-lg text-right" 
                               required>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-medium" x-text="'$' + calculateItemTotal(item)"></span>
                    <button type="button" 
                            @click="removeItem(index)"
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

        <!-- Add Item Button -->
        <button type="button" 
                @click="addItem"
                class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg mt-4 hover:bg-blue-700 
                       inline-flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Pievienot preci
        </button>

        <!-- Totals -->
        <div class="flex justify-end mt-6">
            <div class="text-xl font-bold text-right space-y-2">
                <div class="text-gray-600">
                    Total (w/o VAT): $<span x-text="calculateSubtotal()">0.00</span>
                    <input type="hidden" name="total_wo_vat" x-bind:value="calculateSubtotal()">
                </div>
                <div class="text-gray-600">
                    Total VAT: $<span x-text="calculateVat()">0.00</span>
                    <input type="hidden" name="total_vat" x-bind:value="calculateVat()">
                </div>
                <div class="text-gray-900">
                    Kopējā summa: $<span x-text="calculateTotal()">0.00</span>
                    <input type="hidden" name="total_amount" x-bind:value="calculateTotal()">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end mt-6">
            <button type="submit" 
                    class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors duration-200">
                {{ $invoice ? 'Rediģet rēķinu' : 'Izveidot rēķinu' }}
            </button>
        </div>
    </div>
</form>