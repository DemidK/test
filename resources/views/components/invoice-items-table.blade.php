@props(['items'])

<div x-data="{
    items: @js($items),
    calculateTotals() {
        let subtotal = 0;
        let vat = 0;
        
        this.items.forEach(item => {
            const itemSubtotal = item.quantity * item.price;
            const itemVat = item.vat > 0 ? (itemSubtotal * item.vat / 100) : 0;
            
            subtotal += itemSubtotal;
            vat += itemVat;
        });
        
        return {
            subtotal: subtotal,
            vat: vat,
            total: subtotal + vat
        };
    }
}" class="space-y-4">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Apraksts</th>
                    <th class="p-2 text-right">Qty</th>
                    <th class="p-2 text-right">Cena</th>
                    <th class="p-2 text-right">VAT</th>
                    <th class="p-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="item in items" :key="item.description">
                    <tr class="border-b">
                        <td class="p-2" x-text="item.description"></td>
                        <td class="p-2 text-right" x-text="item.quantity"></td>
                        <td class="p-2 text-right" x-text="'$' + Number(item.price).toFixed(2)"></td>
                        <td class="p-2 text-right" x-text="item.vat + '%'"></td>
                        <td class="p-2 text-right" x-text="'$' + Number(item.quantity * item.price * (1 + item.vat/100)).toFixed(2)"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <div class="bg-gray-100 p-4 text-right">
        <div class="space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal (w/o VAT):</span>
                <span class="font-semibold" x-text="'$' + calculateTotals().subtotal.toFixed(2)"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Total VAT:</span>
                <span class="font-semibold" x-text="'$' + calculateTotals().vat.toFixed(2)"></span>
            </div>
            <div class="flex justify-between text-xl font-bold text-green-600">
                <span>Kopējā summa:</span>
                <span x-text="'$' + calculateTotals().total.toFixed(2)"></span>
            </div>
        </div>
    </div>
</div>