<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Rēķins Nr. {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        .items-table th {
            background-color: #f2f2f2;
        }
        .items-table td:first-child {
            text-align: left;
        }
        .totals {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <div>
            <h1>Rēķins</h1>
            <p>Rēķina numurs: Nr. {{ $invoice->invoice_number }}</p>
            <p>Datums: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d.m.Y') }}</p>
        </div>
        <div>
            <h3>{{ $invoice->partner_name }}</h3>
            <p>{{ $invoice->partner_email }}</p>
            @if($invoice->partner_vat)
                <p>PVN reģ. Nr.: {{ $invoice->partner_vat }}</p>
            @endif
        </div>
    </div>

    <div class="invoice-details">
        <h3>Klienta informācija</h3>
        <p>Adrese: {{ $invoice->partner_address }}</p>
        @if($invoice->partner_post_address)
            <p>Pasta adrese: {{ $invoice->partner_post_address }}</p>
        @endif
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Apraksts</th>
                <th>Daudzums</th>
                <th>Cena</th>
                <th>PVN %</th>
                <th>Kopā</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalSubtotal = 0;
                $totalVat = 0;
                $grandTotal = 0;
            @endphp

            @foreach($items as $item)
                @php
                    // Ensure numeric values
                    $itemQty = floatval($item['quantity'] ?? 0);
                    $itemPrice = floatval($item['price'] ?? 0);
                    $itemVat = floatval($item['vat'] ?? 0);
                    $itemDescription = $item['description'] ?? '';
                    
                    $itemSubtotal = $itemQty * $itemPrice;
                    $itemVatAmount = $itemSubtotal * ($itemVat / 100);
                    $itemTotal = $itemSubtotal + $itemVatAmount;
                    
                    $totalSubtotal += $itemSubtotal;
                    $totalVat += $itemVatAmount;
                    $grandTotal += $itemTotal;
                @endphp
                <tr>
                    <td>{{ $itemDescription }}</td>
                    <td>{{ $itemQty }}</td>
                    <td>€{{ number_format($itemPrice, 2) }}</td>
                    <td>{{ $itemVat }}%</td>
                    <td>€{{ number_format($itemTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div>Summa bez PVN: €{{ number_format($totalSubtotal, 2) }}</div>
        <div>PVN summa: €{{ number_format($totalVat, 2) }}</div>
        <div class="total-row">Kopējā summa: €{{ number_format($grandTotal, 2) }}</div>
    </div>
</body>
</html>