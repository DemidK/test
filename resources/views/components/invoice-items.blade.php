@props(['index' => 0, 'readonly' => false])

<div class="item grid grid-cols-6 gap-4 mb-2 p-3 bg-gray-50 rounded-lg">
    <div class="col-span-2">
        <input type="text" 
            name="items[{{ $index }}][description]" 
            x-model="item.description"
            placeholder="Apraksts" 
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-200" 
            {{ $readonly ? 'readonly' : 'required' }}>
    </div>
    <div>
        <input type="number" 
            name="items[{{ $index }}][quantity]" 
            x-model.number="item.quantity"
            placeholder="Qty" 
            min="1" 
            class="w-full px-3 py-2 border rounded-lg text-center item-qty focus:ring-2 focus:ring-blue-200" 
            {{ $readonly ? 'readonly' : 'required' }}>
    </div>
    <div>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
            <input type="number" 
                name="items[{{ $index }}][price]" 
                x-model.number="item.price"
                placeholder="0.00" 
                step="0.01" 
                min="0" 
                class="w-full px-3 py-2 pl-7 border rounded-lg text-right item-price focus:ring-2 focus:ring-blue-200" 
                {{ $readonly ? 'readonly' : 'required' }}>
        </div>
    </div>
    <div>
        <div class="relative">
            <input type="number" 
                name="items[{{ $index }}][vat]" 
                x-model.number="item.vat"
                placeholder="0" 
                min="0" 
                max="100" 
                class="w-full px-3 py-2 pr-7 border rounded-lg text-right item-vat focus:ring-2 focus:ring-blue-200" 
                {{ $readonly ? 'readonly' : 'required' }}>
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">%</span>
        </div>
    </div>
    <div class="flex items-center justify-between">
        <span class="item-total font-medium" x-text="'$' + ((item.quantity || 0) * (item.price || 0) * (1 + (item.vat || 0)/100)).toFixed(2)"></span>
        @unless($readonly)
            <button type="button" 
                    @click="items.splice(index, 1)" 
                    class="remove-item text-red-500 hover:text-red-700"
                    x-show="items.length > 1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        @endunless
    </div>
</div>