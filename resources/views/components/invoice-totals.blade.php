@props(['editable' => false])

<div class="flex justify-end mt-6" 
     x-data="{ 
        subtotal: 0, 
        vat: 0, 
        total: 0,
        updateTotals() {
            let newSubtotal = 0;
            let newVat = 0;
            
            this.items.forEach(item => {
                const itemSubtotal = (item.quantity || 0) * (item.price || 0);
                const itemVat = (item.vat || 0) > 0 ? (itemSubtotal * (item.vat || 0) / 100) : 0;
                newSubtotal += itemSubtotal;
                newVat += itemVat;
            });
            
            this.subtotal = newSubtotal;
            this.vat = newVat;
            this.total = newSubtotal + newVat;
        }
     }"
     x-init="$watch('items', () => updateTotals(), { deep: true })">
    <div class="text-xl font-bold text-right space-y-2">
        <div class="text-gray-600">
            Total (w/o VAT): $<span x-text="subtotal.toFixed(2)">0.00</span>
        </div>
        <div class="text-gray-600">
            Total VAT: $<span x-text="vat.toFixed(2)">0.00</span>
        </div>
        <div class="text-gray-900">
            Kopējā summa: $<span x-text="total.toFixed(2)">0.00</span>
        </div>
        
        @if($editable)
            <input type="hidden" name="total_amount" x-bind:value="total.toFixed(2)">
            <input type="hidden" name="total_vat" x-bind:value="vat.toFixed(2)">
            <input type="hidden" name="total_wo_vat" x-bind:value="subtotal.toFixed(2)">
        @endif
    </div>
</div>