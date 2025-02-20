<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .invoice-details { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 4px; }
        .total-section { text-align: right; margin-top: 20px; }
        .debug-section { background-color: #f0f0f0; padding: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="invoice-details">
        <h1>Invoice #{{ $invoice->invoice_number }}</h1>
        <p>Date: {{ $invoice->invoice_date }}</p>
        <p>Customer: {{ $invoice->customer_name }}</p>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Description</th>
                <th>VAT</th>
                <th>Nosakunums</th>
                <th>PVN</th>
                <th>Daudzums</th>
                <th>Cena EUR</th>
                <th>Summa EUR</th>
            </tr>
        </thead>
        <tbody>
            @php 
                // Ensure $items is an array
                $items = is_array($items) ? $items : [];
                
                $totalSum = 0;
                $totalVat = 0;
                $totalWoVat = 0;
            @endphp

            @forelse($items as $index => $item)
                @php
                    $itemQty = $item['quantity'] ?? 1;
                    $itemPrice = $item['price'] ?? 0;
                    $itemVat = $item['vat'] ?? 0;
                    $itemSubtotal = $itemQty * $itemPrice;
                    $itemVatAmount = $itemSubtotal * ($itemVat / 100);
                    $itemTotal = $itemSubtotal + $itemVatAmount;
                    
                    $totalSum += $itemTotal;
                    $totalVat += $itemVatAmount;
                    $totalWoVat += $itemSubtotal;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['description'] ?? 'N/A' }}</td>
                    <td>{{ number_format($itemVat, 2) }}%</td>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ number_format($itemVat, 2) }}%</td>
                    <td>{{ $itemQty }}</td>
                    <td>{{ number_format($itemPrice, 2) }}</td>
                    <td>{{ number_format($itemTotal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No items found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-section">
        <p>Total Sum (EUR): {{ number_format($totalSum, 2) }}</p>
        <p>Total without VAT (EUR): {{ number_format($totalWoVat, 2) }}</p>
        <p>Total VAT (EUR): {{ number_format($totalVat, 2) }}</p>
    </div>
</body>
</html>