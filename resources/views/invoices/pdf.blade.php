<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
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
            <h1>Invoice</h1>
            <p>Invoice Number: #{{ $invoice->invoice_number }}</p>
            <p>Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('F d, Y') }}</p>
        </div>
        <div>
            <h3>{{ $invoice->customer_name }}</h3>
            <p>{{ $invoice->customer_email }}</p>
            @if($invoice->customer_vat)
                <p>VAT No: {{ $invoice->customer_vat }}</p>
            @endif
        </div>
    </div>

    <div class="invoice-details">
        <h3>Customer Details</h3>
        <p>Address: {{ $invoice->customer_address }}</p>
        @if($invoice->customer_post_address)
            <p>Postal Address: {{ $invoice->customer_post_address }}</p>
        @endif
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Price</th>
                <th>VAT %</th>
                <th>Total</th>
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
                    <td>${{ number_format($itemPrice, 2) }}</td>
                    <td>{{ $itemVat }}%</td>
                    <td>${{ number_format($itemTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div>Subtotal (w/o VAT): ${{ number_format($totalSubtotal, 2) }}</div>
        <div>Total VAT: ${{ number_format($totalVat, 2) }}</div>
        <div class="total-row">Total Amount: ${{ number_format($grandTotal, 2) }}</div>
    </div>
</body>
</html>