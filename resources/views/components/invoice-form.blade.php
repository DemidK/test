<!-- invoice-form.blade.php -->
@props(['invoice' => null, 'action', 'method' => 'POST'])

<form action="{{ $action }}" method="POST" id="invoiceForm">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    @php
        $defaultItem = ['description' => '', 'quantity' => 1, 'price' => 0, 'vat' => 21];
        $items = $invoice ? old('items', $invoice->items) : [$defaultItem];
    @endphp
    
    <div x-data="{
        partnerSearch: {
            query: '{{ old('partner_name', $invoice?->partner_name) }}',
            results: [],
            isSearching: false,
            showDropdown: false,
            selectedPartnerId: '{{ old('partner_id', $invoice?->partner_id) }}'
        },
        searchPartners() {
            const query = this.partnerSearch.query;
            if (query.length < 2) {
                this.partnerSearch.results = [];
                this.partnerSearch.showDropdown = false;
                return;
            }
            
            this.partnerSearch.isSearching = true;
            this.partnerSearch.showDropdown = true;
            
            fetch(`/search-partners?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    this.partnerSearch.results = data;
                    this.partnerSearch.isSearching = false;
                })
                .catch(error => {
                    console.error('Error searching partners:', error);
                    this.partnerSearch.isSearching = false;
                });
        },
        selectPartner(partner) {
            this.partnerSearch.query = partner.name;
            this.partnerSearch.selectedPartnerId = partner.id;
            this.partnerSearch.showDropdown = false;
            
            // Fill partner fields
            document.getElementById('partner_id').value = partner.id;
            document.getElementById('partner_vat').value = partner.vat || '';
            document.getElementById('partner_email').value = partner.email || '';
            document.getElementById('partner_address').value = partner.address || '';
            document.getElementById('partner_post_address').value = partner.post_address || '';
        }
    }">
        <!-- Basic Invoice Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
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
                    value="{{ old('invoice_date', $invoice?->invoice_date ? date('Y-m-d', strtotime($invoice->invoice_date)) : date('Y-m-d')) }}" 
                    required>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="relative">
                <label for="partner_name" class="block text-gray-700 mb-2">Klienta nosaukums</label>
                <input type="text" 
                       name="partner_name" 
                       id="partner_name" 
                       class="w-full px-4 py-2 border rounded-lg" 
                       x-model="partnerSearch.query"
                       @input="searchPartners"
                       @focus="if (partnerSearch.query.length >= 2) partnerSearch.showDropdown = true"
                       required>
                       
                <!-- Search Results Dropdown -->
                <div class="absolute left-0 right-0 mt-1 z-50 bg-white rounded-lg shadow-lg border max-h-60 overflow-y-auto"
                     x-show="partnerSearch.showDropdown"
                     @click.away="partnerSearch.showDropdown = false">
                    <div x-show="partnerSearch.isSearching" class="p-2 text-gray-500">Meklē...</div>
                    <div x-show="!partnerSearch.isSearching && partnerSearch.results.length === 0 && partnerSearch.query.length >= 2" 
                         class="p-2 text-gray-500">Nav atrasti rezultāti</div>
                    <template x-for="(partner, index) in partnerSearch.results" :key="index">
                        <div @click="selectPartner(partner)"
                             class="p-2 hover:bg-gray-100 cursor-pointer">
                            <div x-text="partner.name" class="font-medium"></div>
                            <div x-text="partner.identification_number" class="text-sm text-gray-600"></div>
                        </div>
                    </template>
                </div>
            </div>
            <div>
                <label for="partner_email" class="block text-gray-700 mb-2">Klienta e-pasts</label>
                <input type="email" 
                       name="partner_email" 
                       id="partner_email" 
                       class="w-full px-4 py-2 border rounded-lg" 
                       value="{{ old('partner_email', $invoice?->partner_email) }}"
                       >
            </div>
            <div>
                <label for="partner_vat" class="block text-gray-700 mb-2">PVN numurs</label>
                <input type="text" 
                       name="partner_vat" 
                       id="partner_vat" 
                       class="w-full px-4 py-2 border rounded-lg" 
                       value="{{ old('partner_vat', $invoice?->partner_vat) }}">
            </div>
            <div>
                <label for="partner_id" class="block text-gray-700 mb-2">Reģistrācijas numurs</label>
                <input type="text" 
                       name="partner_id" 
                       id="partner_id" 
                       class="w-full px-4 py-2 border rounded-lg" 
                       value="{{ old('partner_id', $invoice?->partner_id) }}"
                       required>
            </div>
            <div class="sm:col-span-2">
                <label for="partner_address" class="block text-gray-700 mb-2">Juridiskā adrese</label>
                <textarea name="partner_address" 
                          id="partner_address" 
                          class="w-full px-4 py-2 border rounded-lg" 
                          rows="2"
                          >{{ old('partner_address', $invoice?->partner_address) }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label for="partner_post_address" class="block text-gray-700 mb-2">Pasta adrese</label>
                <textarea name="partner_post_address" 
                          id="partner_post_address" 
                          class="w-full px-4 py-2 border rounded-lg" 
                          rows="2">{{ old('partner_post_address', $invoice?->partner_post_address) }}</textarea>
            </div>
        </div>
    </div>

    <!-- Items Section -->
    <div x-data="{
        items: {{ json_encode($items) }},
        addItem() { this.items.push({ description: '', quantity: 1, price: 0, vat: 21 }); },
        removeItem(index) { if (this.items.length > 1) this.items.splice(index, 1); },
        calculateItemTotal(item) { return ((item.quantity || 0) * (item.price || 0) * (1 + (item.vat || 0)/100)).toFixed(2); },
        calculateSubtotal() { return this.items.reduce((sum, item) => sum + (item.quantity || 0) * (item.price || 0), 0).toFixed(2); },
        calculateVat() { return this.items.reduce((sum, item) => sum + (item.quantity || 0) * (item.price || 0) * ((item.vat || 0)/100), 0).toFixed(2); },
        calculateTotal() { return this.items.reduce((sum, item) => sum + (item.quantity || 0) * (item.price || 0) * (1 + (item.vat || 0)/100), 0).toFixed(2); }
    }">
        <!-- Items Header Row - Desktop Only -->
        <div class="hidden sm:grid sm:grid-cols-12 sm:gap-4 sm:px-4 bg-gray-50 py-2 rounded-t-lg">
            <div class="col-span-5">
                <span class="text-sm font-medium text-gray-700">Apraksts</span>
            </div>
            <div class="col-span-1 text-center">
                <span class="text-sm font-medium text-gray-700">Daudz.</span>
            </div>
            <div class="col-span-2 text-center">
                <span class="text-sm font-medium text-gray-700">Cena</span>
            </div>
            <div class="col-span-2 text-center">
                <span class="text-sm font-medium text-gray-700">PVN %</span>
            </div>
            <div class="col-span-2 text-center">
                <span class="text-sm font-medium text-gray-700">Kopā</span>
            </div>
        </div>

        <!-- Items Container -->
        <template x-for="(item, index) in items" :key="index">
            <div class="mb-1 py-1 px-4 bg-gray-50 relative">
                <!-- Desktop Layout: Single Row -->
                <div class="hidden sm:grid sm:grid-cols-12 sm:gap-4 sm:items-center">
                    <div class="col-span-5">
                        <input type="text" 
                               x-model="item.description"
                               :name="'items[' + index + '][description]'"
                               class="w-full px-3 py-1 border rounded-lg"
                               required>
                    </div>
                    <div class="col-span-1">
                        <input type="number" 
                               x-model.number="item.quantity"
                               :name="'items[' + index + '][quantity]'"
                               min="1" 
                               class="w-full px-3 py-1 border rounded-lg text-right"
                               required>
                    </div>
                    <div class="col-span-2">
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">€</span>
                            <input type="number" 
                                   x-model.number="item.price"
                                   :name="'items[' + index + '][price]'"
                                   step="0.01" 
                                   min="0" 
                                   class="w-full px-3 py-1 pl-7 border rounded-lg text-right"
                                   required>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <div class="relative">
                            <input type="number" 
                                   x-model.number="item.vat"
                                   :name="'items[' + index + '][vat]'"
                                   min="0" 
                                   max="100" 
                                   class="w-full px-3 py-1 pr-7 border rounded-lg text-right"
                                   required>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <div class="flex items-center justify-between">
                            <span class="font-medium" x-text="'€' + calculateItemTotal(item)"></span>
                            <button type="button" 
                                    @click="removeItem(index)"
                                    class="text-red-500 hover:text-red-700 ml-2"
                                    x-show="items.length > 1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Layout: Stacked -->
                <div class="grid grid-cols-2 gap-4 sm:hidden">
                    <div class="col-span-2">
                        <label class="block text-gray-700 mb-2">Apraksts</label>
                        <input type="text" 
                               x-model="item.description"
                               :name="'items[' + index + '][description]'"
                               class="w-full px-3 py-2 border rounded-lg"
                               required>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Daudzums</label>
                        <input type="number" 
                               x-model.number="item.quantity"
                               :name="'items[' + index + '][quantity]'"
                               min="1" 
                               class="w-full px-3 py-2 border rounded-lg text-right"
                               required>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Cena</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">€</span>
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
                        <label class="block text-gray-700 mb-2">PVN %</label>
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
                    <div>
                        <label class="block text-gray-700 mb-2">Kopā</label>
                        <div class="flex items-center justify-between">
                            <span class="font-medium" x-text="'€' + calculateItemTotal(item)"></span>
                            <button type="button" 
                                    @click="removeItem(index)"
                                    class="text-red-500 hover:text-red-700 ml-2"
                                    x-show="items.length > 1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Add Item Button -->
        <button type="button" 
                @click="addItem"
                class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 
                       inline-flex items-center justify-center gap-2 mb-6">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Pievienot pozīciju
        </button>

        <!-- Totals -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg space-y-2">
            <div class="flex justify-between text-gray-600">
                <span>Summa bez PVN:</span>
                <span x-text="'€' + calculateSubtotal()"></span>
                <input type="hidden" name="total_wo_vat" x-bind:value="calculateSubtotal()">
            </div>
            <div class="flex justify-between text-gray-600">
                <span>PVN summa:</span>
                <span x-text="'€' + calculateVat()"></span>
                <input type="hidden" name="total_vat" x-bind:value="calculateVat()">
            </div>
            <div class="flex justify-between text-xl font-bold text-gray-900">
                <span>Kopējā summa:</span>
                <span x-text="'€' + calculateTotal()"></span>
                <input type="hidden" name="total_amount" x-bind:value="calculateTotal()">
            </div>
        </div>

        <!-- Notes Section -->
        <div class="mt-6">
            <label for="notes" class="block text-gray-700 mb-2">Piezīmes</label>
            <textarea name="notes" 
                      id="notes" 
                      rows="3" 
                      class="w-full px-4 py-2 border rounded-lg">{{ old('notes', $invoice?->notes) }}</textarea>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" 
                    class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 
                           transition-colors duration-200">
                {{ $invoice ? 'Rediģēt rēķinu' : 'Izveidot rēķinu' }}
            </button>
        </div>
    </div>
</form>