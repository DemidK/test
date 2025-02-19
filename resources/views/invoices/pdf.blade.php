<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>FAKTŪRRĒĶINS Nr. {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }
        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .invoice-date {
            text-align: center;
            margin-bottom: 30px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 150px auto;
            gap: 5px;
            margin-bottom: 20px;
        }
        .details-grid div {
            padding: 2px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .items-table th {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            float: right;
            margin-top: 10px;
        }
        .totals div {
            text-align: right;
            margin: 5px 0;
        }
        .signature-section {
            margin-top: 50px;
            display: grid;
            grid-template-columns: 150px auto;
            gap: 5px;
        }
        .signature-section .line {
            border-bottom: 1px solid #000;
            width: 200px;
            height: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="invoice-title">
        FAKTŪRRĒĶINS Nr. {{ $invoice->invoice_number }}
    </div>
    
    <div class="invoice-date">
        {{ date('Y. gada d. F', strtotime($invoice->invoice_date)) }}
    </div>

    <div class="details-grid">
        <div>Nosūtītājs</div>
        <div>{{ config('app.name') }}</div>
        
        <div>Adrese,telefons</div>
        <div>{{ config('app.address') }}</div>
        
        <div>Norēķinu rekvizīti</div>
        <div>{{ config('app.bank_details') }}</div>
        
        <div>Saņēmējs</div>
        <div>{{ $invoice->customer_name }}</div>
        
        <div>Adrese</div>
        <div>{{ $invoice->customer_address }}</div>
        
        <div>Saņemšanas vieta</div>
        <div>{{ $invoice->customer_address }}</div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Nr.</th>
                <th>Nosaukums</th>
                <th>PVN</th>
                <th>MV</th>
                <th>Daudzums</th>
                <th>Cena EUR</th>
                <th>Summa EUR</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['description'] }}</td>
                <td>{{ $item['vat'] }}%</td>
                <td>gab</td>
                <td class="text-right">{{ $item['quantity'] }}</td>
                <td class="text-right">{{ number_format($item['price'], 2) }}</td>
                <td class="text-right">{{ number_format($item['total'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div>SUMMA: {{ number_format($calculations['subtotal'], 2) }}</div>
        <div>PVN 21%: {{ number_format($calculations['total_vat'], 2) }}</div>
        <div>KOPĀ EUR: {{ number_format($invoice->total_amount, 2) }}</div>
    </div>

    <div class="signature-section">
        <div>Izniedzējs</div>
        <div></div>
        
        <div>Vārds, Uzvārds</div>
        <div><span class="line"></span></div>
        
        <div>Datums</div>
        <div>{{ date('Y. gada d. F', strtotime($invoice->invoice_date)) }}</div>
        
        <div>Paraksts</div>
        <div><span class="line"></span></div>
        
        <div>Z.V.</div>
        <div></div>
    </div>
</body>
</html>